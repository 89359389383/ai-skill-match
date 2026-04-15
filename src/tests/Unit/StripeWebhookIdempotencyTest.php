<?php

namespace Tests\Unit;

use App\Models\Buyer;
use App\Models\Freelancer;
use App\Models\SkillListing;
use App\Models\SkillOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StripeWebhookIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_event_id_is_idempotent(): void
    {
        $order = $this->makePendingOrder();

        $payload = json_encode([
            'id' => 'evt_unit_idempotent',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_unit_1',
                    'payment_intent' => 'pi_unit_1',
                    'metadata' => [
                        'order_id' => (string) $order->id,
                    ],
                ],
            ],
        ]);

        $this->postWebhookPayload($payload)->assertOk();
        $firstPaidAt = SkillOrder::query()->findOrFail($order->id)->paid_at;

        $this->postWebhookPayload($payload)->assertOk();

        $order->refresh();
        $this->assertSame($firstPaidAt?->format('Y-m-d H:i:s'), $order->paid_at?->format('Y-m-d H:i:s'));
        $this->assertSame('evt_unit_idempotent', $order->stripe_webhook_event_id);
    }

    private function makePendingOrder(): SkillOrder
    {
        $buyerUser = User::factory()->create(['role' => 'buyer']);
        Buyer::create(['user_id' => $buyerUser->id, 'display_name' => 'Buyer']);

        $sellerUser = User::factory()->create(['role' => 'freelancer']);
        $freelancer = Freelancer::create([
            'user_id' => $sellerUser->id,
            'display_name' => 'Seller',
            'job_title' => 'Engineer',
            'bio' => 'bio',
            'min_hours_per_week' => 10,
            'max_hours_per_week' => 40,
            'hours_per_day' => 8,
            'days_per_week' => 5,
            'work_availability_status' => 'available_full',
            'min_rate' => 1000,
            'max_rate' => 2000,
            'stripe_connect_account_id' => 'acct_unit_2',
        ]);

        $listing = SkillListing::create([
            'freelancer_id' => $freelancer->id,
            'title' => 'unit',
            'description' => 'desc',
            'purchase_instructions' => 'instruction',
            'price' => 10000,
            'pricing_type' => 'fixed',
            'status' => 1,
        ]);

        return SkillOrder::create([
            'skill_listing_id' => $listing->id,
            'buyer_user_id' => $buyerUser->id,
            'amount' => 10000,
            'status' => SkillOrder::STATUS_PENDING,
            'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
            'transaction_status' => SkillOrder::TX_WAITING_PAYMENT,
            'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
            'purchased_at' => Carbon::now(),
        ]);
    }

    private function postWebhookPayload(string $payload)
    {
        $secret = 'whsec_unit';
        config()->set('services.stripe.webhook_secret', $secret);

        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        return $this->call(
            'POST',
            route('stripe.webhook'),
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );
    }
}
