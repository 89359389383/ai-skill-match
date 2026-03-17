<?php

namespace App\Services;

use App\Models\SkillListing;
use App\Models\SkillOrder;
use App\Models\SkillOrderMessage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SkillOrderService
{
    /**
     * スキル購入（注文）を作成する。
     *
     * 現時点は「決済連携なし」のため:
     * - order を pending で作成するだけ
     * - amount は出品価格をスナップショットとして保存する
     */
    public function purchase(User $buyer, SkillListing $listing): SkillOrder
    {
        return DB::transaction(function () use ($buyer, $listing): SkillOrder {
            // すでに購入済みか、などの制約は将来追加する（仕様が固まったら）

            $order = SkillOrder::create([
                'skill_listing_id' => $listing->id,
                'buyer_user_id' => $buyer->id,
                // 注文時点の価格を保存しておく（後から出品価格が変わっても注文は変えない）
                'amount' => $listing->price,
                'status' => 'pending',
                'purchased_at' => Carbon::now(),
            ]);

            // 取引開始のシステムメッセージ（任意）
            SkillOrderMessage::create([
                'skill_order_id' => $order->id,
                'sender_user_id' => null,
                'message_type' => 'system',
                'body' => '取引を開始しました。チャットでやり取りしてください。',
                'sent_at' => Carbon::now(),
            ]);

            return $order;
        });
    }
}

