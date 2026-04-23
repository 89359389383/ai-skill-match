<?php

namespace Tests\Feature;

use App\Models\Buyer;
use App\Models\Freelancer;
use App\Models\SkillListing;
use App\Models\SkillOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SkillDisputeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_handles_dispute_created(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createPaidOrder($buyer->id);

        $payload = [
            'id' => 'evt_test_dispute_created',
            'type' => 'charge.dispute.created',
            'data' => [
                'object' => [
                    'id' => 'dp_test_123',
                    'payment_intent' => $order->stripe_payment_intent_id,
                    'status' => 'needs_response',
                ]
            ]
        ];

        $signature = $this->generateStripeSignature($payload);

        $response = $this->postJson(route('stripe.webhook'), $payload, [
            'Stripe-Signature' => $signature
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'dispute_status' => 'needs_response',
        ]);
    }

    public function test_webhook_handles_dispute_closed_lost(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createPaidOrder($buyer->id);
        $order->update(['dispute_status' => 'needs_response']);

        $payload = [
            'id' => 'evt_test_dispute_closed',
            'type' => 'charge.dispute.closed',
            'data' => [
                'object' => [
                    'id' => 'dp_test_123',
                    'payment_intent' => $order->stripe_payment_intent_id,
                    'status' => 'lost',
                ]
            ]
        ];

        $signature = $this->generateStripeSignature($payload);

        $response = $this->postJson(route('stripe.webhook'), $payload, [
            'Stripe-Signature' => $signature
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'dispute_status' => 'lost',
        ]);
    }

    public function test_dispute_lost_triggers_transfer_reversal_if_already_transferred(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createPaidOrder($buyer->id);
        $order->update([
            'dispute_status' => 'needs_response',
            'payout_status' => SkillOrder::PAYOUT_TRANSFERRED,
            'stripe_transfer_id' => 'tr_test_dispute_lost',
        ]);

        $transferClient = \Mockery::mock(\App\Contracts\StripeTransferClientInterface::class);
        $transferClient->shouldReceive('createReversal')
            ->once()
            ->with('tr_test_dispute_lost', \Mockery::type('array'))
            ->andReturn(['id' => 'trr_test_dispute_lost']);
        $this->app->instance(\App\Contracts\StripeTransferClientInterface::class, $transferClient);

        $payload = [
            'id' => 'evt_test_dispute_closed_lost',
            'type' => 'charge.dispute.closed',
            'data' => [
                'object' => [
                    'id' => 'dp_test_123',
                    'payment_intent' => $order->stripe_payment_intent_id,
                    'status' => 'lost',
                ]
            ]
        ];

        $signature = $this->generateStripeSignature($payload);

        $response = $this->postJson(route('stripe.webhook'), $payload, [
            'Stripe-Signature' => $signature
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'dispute_status' => 'lost',
        ]);
        
        $order->refresh();
        $this->assertNotNull($order->transfer_reversed_at);
        $this->assertNotNull($order->reversal_amount);
    }

    private function generateStripeSignature(array $payload): string
    {
        $timestamp = time();
        $signedPayload = $timestamp . '.' . json_encode($payload);
        $secret = config('services.stripe.webhook_secret');
        $signature = hash_hmac('sha256', $signedPayload, $secret);
        return "t={$timestamp},v1={$signature}";
    }

    private function createBuyerUser(): User
    {
        $user = User::factory()->create(['role' => 'buyer']);
        Buyer::create([
            'user_id' => $user->id,
            'display_name' => 'Buyer',
        ]);

        return $user;
    }

    private function createPaidOrder(int $buyerUserId): SkillOrder
    {
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
            'stripe_connect_account_id' => 'acct_test_001',
        ]);

        $listing = SkillListing::create([
            'freelancer_id' => $freelancer->id,
            'title' => 'テストスキル',
            'description' => '説明',
            'purchase_instructions' => '手順',
            'price' => 10000,
            'pricing_type' => 'fixed',
            'status' => 1,
        ]);

        return SkillOrder::create([
            'skill_listing_id' => $listing->id,
            'buyer_user_id' => $buyerUserId,
            'amount' => 10000,
            'status' => SkillOrder::STATUS_PAID,
            'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
            'transaction_status' => SkillOrder::TX_IN_PROGRESS,
            'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
            'purchased_at' => Carbon::now(),
            'paid_at' => Carbon::now(),
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);
    }
}
