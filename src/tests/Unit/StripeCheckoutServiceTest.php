<?php

namespace Tests\Unit;

use App\Contracts\StripeCheckoutClientInterface;
use App\Models\Buyer;
use App\Models\Freelancer;
use App\Models\SkillListing;
use App\Models\SkillOrder;
use App\Models\User;
use App\Services\StripeCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class StripeCheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_stripe_checkout_success_cancel_url_include_slot(): void
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

        $order = SkillOrder::create([
            'skill_listing_id' => $listing->id,
            'buyer_user_id' => $buyerUser->id,
            'amount' => 10000,
            'status' => SkillOrder::STATUS_PENDING,
            'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
            'transaction_status' => SkillOrder::TX_WAITING_PAYMENT,
            'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
            'purchased_at' => Carbon::now(),
        ]);

        $capturedPayload = null;
        $client = Mockery::mock(StripeCheckoutClientInterface::class);
        $client->shouldReceive('createSession')->once()->andReturnUsing(function (array $payload) use (&$capturedPayload) {
            $capturedPayload = $payload;
            return [
                'id' => 'cs_test_1',
                'payment_intent' => 'pi_test_1',
                'url' => 'https://checkout.example.test/session',
            ];
        });

        $service = new StripeCheckoutService($client);

        // SetSessionCookieSlot が付ける slot を模擬
        $this->app['request']->attributes->set('slot', 'slot_test_123');

        $service->createSessionForOrder($order);

        $this->assertNotNull($capturedPayload);
        $this->assertArrayHasKey('success_url', $capturedPayload);
        $this->assertArrayHasKey('cancel_url', $capturedPayload);

        $this->assertStringContainsString('slot=slot_test_123', (string) $capturedPayload['success_url']);
        $this->assertStringContainsString('slot=slot_test_123', (string) $capturedPayload['cancel_url']);
    }
}

