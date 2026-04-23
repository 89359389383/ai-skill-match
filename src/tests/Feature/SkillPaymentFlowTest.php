<?php

namespace Tests\Feature;

use App\Models\Buyer;
use App\Models\Freelancer;
use App\Models\SkillListing;
use App\Models\SkillOrder;
use App\Models\User;
use App\Services\PayoutService;
use App\Services\StripeCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class SkillPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_purchase_start_creates_pending_order(): void
    {
        [$sellerUser, $listing] = $this->createListing();
        $buyer = $this->createBuyerUser();

        $checkoutService = Mockery::mock(StripeCheckoutService::class);
        $checkoutService->shouldReceive('createSessionForOrder')
            ->once()
            ->andReturn('https://checkout.example.test/session');
        $this->app->instance(StripeCheckoutService::class, $checkoutService);

        $res = $this->actingAs($buyer, 'buyer')
            ->post(route('skills.purchase', ['skill_listing' => $listing->id]));

        $res->assertRedirect('https://checkout.example.test/session');

        $this->assertDatabaseHas('skill_orders', [
            'skill_listing_id' => $listing->id,
            'buyer_user_id' => $buyer->id,
            'status' => SkillOrder::STATUS_PENDING,
            'transaction_status' => SkillOrder::TX_WAITING_PAYMENT,
            'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
            'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
        ]);
    }

    public function test_self_purchase_is_forbidden(): void
    {
        [$sellerUser, $listing] = $this->createListing();

        $checkoutService = Mockery::mock(StripeCheckoutService::class);
        $checkoutService->shouldReceive('createSessionForOrder')->never();
        $this->app->instance(StripeCheckoutService::class, $checkoutService);

        $res = $this->actingAs($sellerUser, 'freelancer')
            ->post(route('skills.purchase', ['skill_listing' => $listing->id]));

        $res->assertSessionHas('error');
        $this->assertDatabaseCount('skill_orders', 0);
    }

    public function test_success_url_does_not_mark_paid(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createOrder($buyer->id, SkillOrder::STATUS_PENDING, SkillOrder::TX_WAITING_PAYMENT);

        $res = $this->actingAs($buyer, 'buyer')
            ->get(route('skills.checkout.success', ['order' => $order->id]));

        $res->assertOk();
        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'status' => SkillOrder::STATUS_PENDING,
            'transaction_status' => SkillOrder::TX_WAITING_PAYMENT,
        ]);
    }

    public function test_webhook_marks_pending_order_paid_and_in_progress(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createOrder($buyer->id, SkillOrder::STATUS_PENDING, SkillOrder::TX_WAITING_PAYMENT);

        $eventId = 'evt_test_100';
        $payload = json_encode([
            'id' => $eventId,
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_100',
                    'payment_intent' => 'pi_test_100',
                    'metadata' => [
                        'order_id' => (string) $order->id,
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $this->postWebhookPayload($payload)->assertOk();

        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'status' => SkillOrder::STATUS_PAID,
            'transaction_status' => SkillOrder::TX_COMPLETED,
            'stripe_webhook_event_id' => $eventId,
            'stripe_checkout_session_id' => 'cs_test_100',
            'stripe_payment_intent_id' => 'pi_test_100',
        ]);
    }

    public function test_webhook_marks_instant_order_paid_and_completed(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createOrder(
            $buyer->id,
            SkillOrder::STATUS_PENDING,
            SkillOrder::TX_WAITING_PAYMENT,
            SkillOrder::PAYMENT_TYPE_INSTANT
        );

        $eventId = 'evt_test_instant_1';
        $payload = json_encode([
            'id' => $eventId,
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_instant_1',
                    'payment_intent' => 'pi_test_instant_1',
                    'metadata' => [
                        'order_id' => (string) $order->id,
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $this->postWebhookPayload($payload)->assertOk();

        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'status' => SkillOrder::STATUS_PAID,
            'transaction_status' => SkillOrder::TX_COMPLETED,
            'stripe_webhook_event_id' => $eventId,
            'stripe_checkout_session_id' => 'cs_test_instant_1',
            'stripe_payment_intent_id' => 'pi_test_instant_1',
        ]);
    }

    public function test_duplicate_webhook_does_not_double_update(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createOrder($buyer->id, SkillOrder::STATUS_PENDING, SkillOrder::TX_WAITING_PAYMENT);

        $payload = json_encode([
            'id' => 'evt_dup_1',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_dup_1',
                    'payment_intent' => 'pi_dup_1',
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

        $this->assertEquals($firstPaidAt?->format('Y-m-d H:i:s'), $order->paid_at?->format('Y-m-d H:i:s'));
        $this->assertSame('evt_dup_1', $order->stripe_webhook_event_id);
    }

    public function test_seller_can_deliver_when_paid_in_progress(): void
    {
        [$sellerUser, $listing] = $this->createListing();
        $buyer = $this->createBuyerUser();
        $order = SkillOrder::create([
            'skill_listing_id' => $listing->id,
            'buyer_user_id' => $buyer->id,
            'amount' => 10000,
            'status' => SkillOrder::STATUS_PAID,
            'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
            'transaction_status' => SkillOrder::TX_IN_PROGRESS,
            'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
            'purchased_at' => Carbon::now(),
            'paid_at' => Carbon::now(),
        ]);

        $res = $this->actingAs($sellerUser, 'freelancer')
            ->post(route('transactions.deliver', ['skill_order' => $order->id]));

        $res->assertRedirect();
        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'transaction_status' => SkillOrder::TX_DELIVERED,
        ]);
    }

    public function test_buyer_can_complete_and_transfer_once(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createDeliveredPaidOrderForBuyer($buyer->id, true);

        $payout = Mockery::mock(PayoutService::class);
        $payout->shouldReceive('transferForOrder')
            ->once()
            ->andReturnUsing(function (SkillOrder $order) {
                $order->stripe_transfer_id = 'tr_test_1';
                $order->payout_status = SkillOrder::PAYOUT_TRANSFERRED;
                $order->save();
                return $order;
            });
        $this->app->instance(PayoutService::class, $payout);

        $res = $this->actingAs($buyer, 'buyer')
            ->post(route('buyer.transactions.complete', ['skill_order' => $order->id]), [
                'rating' => 5,
                'review' => 'great',
            ]);

        $res->assertRedirect();

        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'transaction_status' => SkillOrder::TX_COMPLETED,
            'payout_status' => SkillOrder::PAYOUT_TRANSFERRED,
            'stripe_transfer_id' => 'tr_test_1',
        ]);
    }

    public function test_double_complete_does_not_double_transfer(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createDeliveredPaidOrderForBuyer($buyer->id, true);

        $payout = Mockery::mock(PayoutService::class);
        $payout->shouldReceive('transferForOrder')
            ->once()
            ->andReturnUsing(function (SkillOrder $order) {
                $order->stripe_transfer_id = 'tr_once';
                $order->payout_status = SkillOrder::PAYOUT_TRANSFERRED;
                $order->save();
                return $order;
            });
        $this->app->instance(PayoutService::class, $payout);

        $this->actingAs($buyer, 'buyer')
            ->post(route('buyer.transactions.complete', ['skill_order' => $order->id]), ['rating' => 5]);

        $this->actingAs($buyer, 'buyer')
            ->post(route('buyer.transactions.complete', ['skill_order' => $order->id]), ['rating' => 5]);

        $this->assertSame(1, SkillOrder::query()->where('id', $order->id)->whereNotNull('stripe_transfer_id')->count());
    }

    public function test_complete_fails_safely_when_connected_account_missing(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createDeliveredPaidOrderForBuyer($buyer->id, false);

        $res = $this->actingAs($buyer, 'buyer')
            ->post(route('buyer.transactions.complete', ['skill_order' => $order->id]), [
                'rating' => 4,
            ]);

        $res->assertRedirect();

        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'payout_status' => SkillOrder::PAYOUT_FAILED,
        ]);
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

    /** @return array{0:User,1:SkillListing} */
    private function createListing(): array
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

        return [$sellerUser, $listing];
    }

    private function createOrder(int $buyerUserId, string $status, string $tx, string $paymentType = SkillOrder::PAYMENT_TYPE_ESCROW): SkillOrder
    {
        [, $listing] = $this->createListing();

        return SkillOrder::create([
            'skill_listing_id' => $listing->id,
            'buyer_user_id' => $buyerUserId,
            'amount' => 10000,
            'status' => $status,
            'payment_type' => $paymentType,
            'transaction_status' => $tx,
            'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
            'purchased_at' => Carbon::now(),
        ]);
    }

    private function createDeliveredPaidOrderForBuyer(int $buyerUserId, bool $withConnect): SkillOrder
    {
        [$sellerUser, $listing] = $this->createListing();
        if (!$withConnect) {
            $listing->freelancer->update(['stripe_connect_account_id' => null]);
        }

        return SkillOrder::create([
            'skill_listing_id' => $listing->id,
            'buyer_user_id' => $buyerUserId,
            'amount' => 10000,
            'status' => SkillOrder::STATUS_PAID,
            'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
            'transaction_status' => SkillOrder::TX_DELIVERED,
            'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
            'purchased_at' => Carbon::now(),
            'paid_at' => Carbon::now(),
            'delivered_at' => Carbon::now(),
        ]);
    }

    private function postWebhookPayload(string $payload)
    {
        $secret = 'whsec_test';
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
