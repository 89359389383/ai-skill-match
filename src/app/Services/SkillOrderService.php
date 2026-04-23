<?php

namespace App\Services;

use App\Models\SkillListing;
use App\Models\SkillOrder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SkillOrderService
{
    /**
     * 決済開始前の仮注文を作成する。
     */
    public function createPendingOrder(User $buyer, SkillListing $listing): SkillOrder
    {
        Log::info('SkillOrderService createPendingOrder begin.', [
            'buyer_id' => $buyer->id,
            'listing_id' => $listing->id,
            'listing_price' => $listing->price,
            'listing_status' => $listing->status,
        ]);

        return DB::transaction(function () use ($buyer, $listing): SkillOrder {
            $order = SkillOrder::create([
                'skill_listing_id' => $listing->id,
                'buyer_user_id' => $buyer->id,
                'amount' => (int) $listing->price,
                'status' => SkillOrder::STATUS_PENDING,
                'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
                'transaction_status' => SkillOrder::TX_WAITING_PAYMENT,
                'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
                'purchased_at' => Carbon::now(),
            ]);

            Log::info('SkillOrderService createPendingOrder created.', [
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_user_id,
                'skill_listing_id' => $order->skill_listing_id,
                'amount' => $order->amount,
                'status' => $order->status,
                'transaction_status' => $order->transaction_status,
                'payout_status' => $order->payout_status,
            ]);

            return $order;
        });
    }
}
