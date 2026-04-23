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
        if (!$user || (int) $user->id !== (int) $order->buyer_user_id) {
            Log::warning('StripeCheckoutController@success denied by buyer mismatch.', [
                'order_id' => $order->id,
                'order_buyer_user_id' => $order->buyer_user_id,
                'auth_user_id' => $user?->id,
                'auth_user_role' => $user?->role,
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
            'checkout_cancelled_at' => $order->checkout_cancelled_at,
            'stripe_checkout_session_id' => $order->stripe_checkout_session_id,
            'stripe_payment_intent_id' => $order->stripe_payment_intent_id,
            'stripe_webhook_event_id' => $order->stripe_webhook_event_id,
            'last_webhook_type' => $order->last_webhook_type,
            'last_webhook_received_at' => $order->last_webhook_received_at,
            'session_id_param' => $request->query('session_id'),
        ]);

        // 決済完了後は「取引チャット」へ自動遷移する
        // Webhook反映がまだの場合でも `transactions.show` 側で支払い確認中表示になるため、
        // このタイミングで遷移しても問題ありません。
        $routeName = $user->role === 'buyer'
            ? 'buyer.transactions.show'
            : 'transactions.show';

        Log::info('StripeCheckoutController@success redirecting to transaction screen.', [
            'order_id' => $order->id,
            'route_name' => $routeName,
            'order_status' => $order->status,
            'transaction_status' => $order->transaction_status,
            'payout_status' => $order->payout_status,
            'stripe_webhook_event_id' => $order->stripe_webhook_event_id,
            'last_webhook_received_at' => $order->last_webhook_received_at,
            'session_id_param' => $request->query('session_id'),
        ]);

        return redirect()->route($routeName, ['skill_order' => $order->id]);
    }

    public function cancel(Request $request, SkillOrder $order)
    {
        $user = $request->user();
        if (!$user || (int) $user->id !== (int) $order->buyer_user_id) {
            Log::warning('StripeCheckoutController@cancel denied by buyer mismatch.', [
                'order_id' => $order->id,
                'order_buyer_user_id' => $order->buyer_user_id,
                'auth_user_id' => $user?->id,
                'auth_user_role' => $user?->role,
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
