<?php

namespace App\Http\Controllers;

use App\Models\SkillListing;
use App\Services\SkillOrderService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkillOrderController extends Controller
{
    public function store(
        Request $request,
        SkillListing $skill_listing,
        SkillOrderService $orderService,
        StripeCheckoutService $checkoutService
    ) {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // 非公開スキルは本人のみ閲覧可
        if ((int) $skill_listing->status !== 1) {
            $viewerFreelancerId = $user->role === 'freelancer' ? $user->freelancer?->id : null;
            if (!$viewerFreelancerId || (int) $skill_listing->freelancer_id !== (int) $viewerFreelancerId) {
                abort(404);
            }
        }

        // 自己購入防止（buyer/企業/フリーランス全ロール共通）
        $sellerUserId = $skill_listing->freelancer?->user_id;
        if ((int) $user->id === (int) $sellerUserId) {
            return back()->with('error', '自分の出品は購入できません。');
        }

        $request->validate([
            'confirm' => ['nullable', 'boolean'],
        ]);

        $order = $orderService->createPendingOrder($user, $skill_listing);
        $checkoutUrl = $checkoutService->createSessionForOrder($order);

        Log::info('Checkout start requested.', [
            'order_id' => $order->id,
            'buyer_id' => $user->id,
            'seller_id' => $sellerUserId,
            'payment_type' => $order->payment_type,
            'session_id' => $order->stripe_checkout_session_id,
            'payment_intent_id' => $order->stripe_payment_intent_id,
            'result' => 'redirect_checkout',
        ]);

        if (empty($checkoutUrl)) {
            return back()->with('error', '決済画面の作成に失敗しました。時間をおいて再度お試しください。');
        }

        return redirect()->away($checkoutUrl);
    }
}
