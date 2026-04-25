<?php

namespace App\Http\Controllers;

use App\Models\SkillOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class StripeCheckoutController extends Controller
{
    public function success(Request $request, SkillOrder $order)
    {
        $user = $request->user();
        $sessionCookieName = (string) config('session.cookie');
        if (!$user || (int) $user->id !== (int) $order->buyer_user_id) {
            Log::warning('StripeCheckoutController@success denied by buyer mismatch.', [
                'order_id' => $order->id,
                'order_buyer_user_id' => $order->buyer_user_id,
                'auth_user_id' => $user?->id,
                'auth_user_role' => $user?->role,
                'session_cookie_name' => $sessionCookieName,
                'has_session_cookie' => $request->cookies->has($sessionCookieName),
                'query_slot' => $request->query('slot'),
                'resolved_slot_attr' => $request->attributes->get('resolved_slot'),
            ]);
            abort(403);
        }

        Log::info('StripeCheckoutController@success view.', [
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_user_id,
            'payment_type' => $order->payment_type,
            'status' => $order->status,
            'transaction_status' => $order->transaction_status,
            'payout_status' => $order->payout_status,
            'session_cookie_name' => $sessionCookieName,
            'has_session_cookie' => $request->cookies->has($sessionCookieName),
            'query_slot' => $request->query('slot'),
            'slot_attr' => $request->attributes->get('slot'),
            'resolved_slot_attr' => $request->attributes->get('resolved_slot'),
            'auth_buyer_check' => auth('buyer')->check(),
            'auth_buyer_user_id' => auth('buyer')->user()?->id,
            'checkout_cancelled_at' => $order->checkout_cancelled_at,
            'stripe_checkout_session_id' => $order->stripe_checkout_session_id,
            'stripe_payment_intent_id' => $order->stripe_payment_intent_id,
            'stripe_webhook_event_id' => $order->stripe_webhook_event_id,
            'last_webhook_type' => $order->last_webhook_type,
            'last_webhook_received_at' => $order->last_webhook_received_at,
            'session_id_param' => $request->query('session_id'),
        ]);

        $showTransactionButton =
            $order->status === SkillOrder::STATUS_PAID
            && $order->transaction_status === SkillOrder::TX_COMPLETED;

        // success_url は「公開ページ」であり、決済確定（paid確定）などは行わず画面表示だけ行う。
        // （テスト要件: 302ではなく 200 を返す）
        return view('skills.checkout_success', [
            'order' => $order,
            'showTransactionButton' => $showTransactionButton,
        ]);
    }

    public function cancel(Request $request, SkillOrder $order)
    {
        $user = $request->user();
        $sessionCookieName = (string) config('session.cookie');
        if (!$user || (int) $user->id !== (int) $order->buyer_user_id) {
            Log::warning('StripeCheckoutController@cancel denied by buyer mismatch.', [
                'order_id' => $order->id,
                'order_buyer_user_id' => $order->buyer_user_id,
                'auth_user_id' => $user?->id,
                'auth_user_role' => $user?->role,
                'session_cookie_name' => $sessionCookieName,
                'has_session_cookie' => $request->cookies->has($sessionCookieName),
                'query_slot' => $request->query('slot'),
                'resolved_slot_attr' => $request->attributes->get('resolved_slot'),
            ]);
            abort(403);
        }

        Log::info('StripeCheckoutController@cancel entered.', [
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_user_id,
            'status' => $order->status,
            'transaction_status' => $order->transaction_status,
            'payout_status' => $order->payout_status,
            'checkout_cancelled_at_before' => $order->checkout_cancelled_at,
            'session_id_param' => $request->query('session_id'),
        ]);

        if ($order->status === SkillOrder::STATUS_PENDING && $order->checkout_cancelled_at === null) {
            $order->checkout_cancelled_at = Carbon::now();
            $order->save();
        }

        Log::info('StripeCheckoutController@cancel saved (if needed).', [
            'order_id' => $order->id,
            'checkout_cancelled_at_after' => $order->checkout_cancelled_at,
        ]);

        return view('skills.checkout_cancel', [
            'order' => $order,
        ]);
    }
}
