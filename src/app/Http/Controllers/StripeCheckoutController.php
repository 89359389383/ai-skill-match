<?php

namespace App\Http\Controllers;

use App\Models\SkillOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StripeCheckoutController extends Controller
{
    public function success(Request $request, SkillOrder $order)
    {
        $user = $request->user();
        if (!$user || (int) $user->id !== (int) $order->buyer_user_id) {
            abort(403);
        }

        return view('skills.checkout_success', [
            'order' => $order,
        ]);
    }

    public function cancel(Request $request, SkillOrder $order)
    {
        $user = $request->user();
        if (!$user || (int) $user->id !== (int) $order->buyer_user_id) {
            abort(403);
        }

        if ($order->status === SkillOrder::STATUS_PENDING && $order->checkout_cancelled_at === null) {
            $order->checkout_cancelled_at = Carbon::now();
            $order->save();
        }

        return view('skills.checkout_cancel', [
            'order' => $order,
        ]);
    }
}
