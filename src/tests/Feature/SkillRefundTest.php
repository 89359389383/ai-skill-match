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

class SkillRefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_refund_updates_status_and_records_refund_id(): void
    {
        // 1. 支払い済みの注文を作成
        $buyer = $this->createBuyerUser();
        $order = $this->createPaidOrder($buyer->id);

        $refundClient = \Mockery::mock(\App\Contracts\StripeRefundClientInterface::class);
        $refundClient->shouldReceive('createRefund')
            ->once()
            ->andReturn(['id' => 're_test_123']);
        $this->app->instance(\App\Contracts\StripeRefundClientInterface::class, $refundClient);

        // 2. 返金エンドポイント（運営またはシステム）にPOST
        $refundAmount = $order->amount;
        
        $res = $this->actingAs($buyer, 'buyer') // Use proper guard
            ->postJson(route('skills.orders.refund', ['skill_order' => $order->id]), [
                'refund_amount' => $refundAmount,
                'refund_reason' => 'requested_by_customer',
            ]);

        // 3. 成功レスポンスの確認
        if ($res->status() !== 200) {
            dump($res->json());
        }
        $res->assertOk();

        // 4. DBの更新（refund_statusやstripe_refund_id等）を確認
        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'refund_status' => 'refunded',
        ]);
        
        $order->refresh();
        $this->assertNotNull($order->stripe_refund_id);
        $this->assertNotNull($order->refunded_at);
    }

    public function test_partial_refund_updates_status_to_partial_refunded(): void
    {
        // 1. 支払い済みの注文を作成
        $buyer = $this->createBuyerUser();
        $order = $this->createPaidOrder($buyer->id); // amount is 10000

        $refundClient = \Mockery::mock(\App\Contracts\StripeRefundClientInterface::class);
        $refundClient->shouldReceive('createRefund')
            ->once()
            ->andReturn(['id' => 're_test_partial_456']);
        $this->app->instance(\App\Contracts\StripeRefundClientInterface::class, $refundClient);

        // 2. 一部金額（5000円）で返金リクエスト
        $partialRefundAmount = 5000;
        
        $res = $this->actingAs($buyer, 'buyer')
            ->postJson(route('skills.orders.refund', ['skill_order' => $order->id]), [
                'refund_amount' => $partialRefundAmount,
                'refund_reason' => 'requested_by_customer',
            ]);

        // 3. 成功レスポンスの確認
        if ($res->status() !== 200) {
            dump($res->json());
        }
        $res->assertOk();

        // 4. DBの更新（refund_statusがpartial_refundedになるか等）を確認
        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'refund_status' => 'partial_refunded',
        ]);
        
        $order->refresh();
        $this->assertNotNull($order->stripe_refund_id);
        $this->assertNotNull($order->refunded_at);
    }

    public function test_refund_triggers_transfer_reversal_if_already_transferred(): void
    {
        $buyer = $this->createBuyerUser();
        $order = $this->createPaidOrder($buyer->id);
        $order->update([
            'payout_status' => SkillOrder::PAYOUT_TRANSFERRED,
            'stripe_transfer_id' => 'tr_test_123',
        ]);

        $refundClient = \Mockery::mock(\App\Contracts\StripeRefundClientInterface::class);
        $refundClient->shouldReceive('createRefund')->once()->andReturn(['id' => 're_test_789']);
        $this->app->instance(\App\Contracts\StripeRefundClientInterface::class, $refundClient);

        $transferClient = \Mockery::mock(\App\Contracts\StripeTransferClientInterface::class);
        $transferClient->shouldReceive('createReversal')
            ->once()
            ->with('tr_test_123', \Mockery::type('array'))
            ->andReturn(['id' => 'trr_test_123']);
        $this->app->instance(\App\Contracts\StripeTransferClientInterface::class, $transferClient);

        $res = $this->actingAs($buyer, 'buyer')
            ->postJson(route('skills.orders.refund', ['skill_order' => $order->id]), [
                'refund_amount' => 10000,
                'refund_reason' => 'requested_by_customer',
            ]);

        $res->assertOk();

        $this->assertDatabaseHas('skill_orders', [
            'id' => $order->id,
            'refund_status' => 'refunded',
        ]);
        
        $order->refresh();
        $this->assertNotNull($order->transfer_reversed_at);
        $this->assertNotNull($order->reversal_amount);
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
