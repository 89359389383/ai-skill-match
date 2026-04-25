<?php

namespace App\Http\Controllers;

use App\Models\SkillListing;
use App\Services\SkillOrderService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkillOrderController extends Controller
{
    /**
     * auth.any により複数ガードが成立し得るため、
     * default guard(web) 前提で Request::user() しない。
     */
    private function resolveAuthenticatedUser(Request $request): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return $request->user('buyer')
            ?? $request->user('freelancer')
            ?? $request->user('company')
            ?? $request->user();
    }

    /**
     * ログイン後リダイレクトで GET /skills/{id}/purchase が来た場合、
     * CSRF 付き POST に自動投げして Stripe 側の支払い遷移へ進める。
     */
    public function purchaseGet(Request $request, SkillListing $skill_listing)
    {
        return response()->view('skills.purchase_autosubmit', [
            'skill_listing' => $skill_listing,
        ]);
    }

    public function store(
        Request $request,
        SkillListing $skill_listing,
        SkillOrderService $orderService,
        StripeCheckoutService $checkoutService
    ) {
        $user = $this->resolveAuthenticatedUser($request);
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        Log::info('SkillOrderController@store received purchase request.', [
            'buyer_id' => $user->id,
            'buyer_role' => $user->role,
            'skill_listing_id' => $skill_listing->id,
            'skill_listing_freelancer_id' => $skill_listing->freelancer?->id,
            'skill_listing_status' => $skill_listing->status,
            'payment_type' => null, // createPendingOrder側で確定
        ]);

        Log::info('SkillOrderController@store request context.', [
            'method' => $request->method(),
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'confirm_input_present' => $request->has('confirm'),
            'confirm_input' => $request->input('confirm'),
        ]);

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

        Log::info('SkillOrderController@store validation passed.', [
            'confirm_input' => $request->input('confirm'),
        ]);

        try {
            Log::info('SkillOrderController@store createPendingOrder about to run.', [
                'buyer_id' => $user->id,
                'seller_user_id' => $sellerUserId,
                'skill_listing_id' => $skill_listing->id,
                'skill_listing_status' => $skill_listing->status,
            ]);
            $order = $orderService->createPendingOrder($user, $skill_listing);
            Log::info('SkillOrderController@store pending order created.', [
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_user_id,
                'seller_user_id' => $sellerUserId,
                'amount' => $order->amount,
                'status' => $order->status,
                'transaction_status' => $order->transaction_status,
                'payout_status' => $order->payout_status,
                'payment_type' => $order->payment_type,
                'stripe_checkout_session_id_before' => $order->stripe_checkout_session_id,
            ]);

            Log::info('SkillOrderController@store about to create stripe session.', [
                'order_id' => $order->id,
                'payment_type' => $order->payment_type,
                'transaction_status' => $order->transaction_status,
                'payout_status' => $order->payout_status,
                'stripe_checkout_session_id_before' => $order->stripe_checkout_session_id,
                'stripe_payment_intent_id_before' => $order->stripe_payment_intent_id,
            ]);
            $checkoutUrl = $checkoutService->createSessionForOrder($order);
            Log::info('SkillOrderController@store checkout session created.', [
                'order_id' => $order->id,
                'checkout_url_present' => !empty($checkoutUrl),
                'checkout_url_length' => is_string($checkoutUrl) ? strlen($checkoutUrl) : null,
                'stripe_checkout_session_id_after' => $order->stripe_checkout_session_id,
                'stripe_payment_intent_id_after' => $order->stripe_payment_intent_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('SkillOrderController@store checkout flow failed.', [
                'buyer_id' => $user->id,
                'buyer_role' => $user->role,
                'skill_listing_id' => $skill_listing->id,
                'seller_user_id' => $sellerUserId,
                'confirm_input' => $request->input('confirm'),
                'skill_listing_status' => $skill_listing->status,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', '決済画面の作成に失敗しました。時間をおいて再度お試しください。');
        }

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

        Log::info('SkillOrderController@store redirecting to checkout url.', [
            'order_id' => $order->id,
            'checkout_url_has_hash' => strpos($checkoutUrl, '#') !== false,
            'checkout_url_md5' => md5($checkoutUrl),
            'checkout_url_prefix' => substr($checkoutUrl, 0, 80),
            'checkout_url_suffix' => strlen($checkoutUrl) > 80 ? substr($checkoutUrl, -80) : $checkoutUrl,
        ]);

        // redirect()->away() 経由だと URL の取り扱いでフラグメント（#...）が変化するケースがあるため、
        // 完全一致が必要な Stripe Checkout URL は Location ヘッダへ生文字列のまま設定して返します。
        return response('', 302)->header('Location', $checkoutUrl);
    }
}
