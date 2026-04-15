<?php

namespace App\Services;

use App\Models\SkillListing;
use App\Models\SkillOrder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SkillOrderService
{
    /**
     * 決済開始前の仮注文を作成する。
     */
    public function createPendingOrder(User $buyer, SkillListing $listing): SkillOrder
    {
        return DB::transaction(function () use ($buyer, $listing): SkillOrder {
            return SkillOrder::create([
                'skill_listing_id' => $listing->id,
                'buyer_user_id' => $buyer->id,
                'amount' => (int) $listing->price,
                'status' => SkillOrder::STATUS_PENDING,
                'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
                'transaction_status' => SkillOrder::TX_WAITING_PAYMENT,
                'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
                'purchased_at' => Carbon::now(),
            ]);
        });
    }
}
