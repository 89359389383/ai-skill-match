<?php

namespace App\Services;

use App\Contracts\StripeCheckoutClientInterface;
use App\Models\SkillOrder;
use Illuminate\Support\Facades\Log;

class StripeCheckoutService
{
    private StripeCheckoutClientInterface $stripeCheckout;

    public function __construct(StripeCheckoutClientInterface $stripeCheckout)
    {
        $this->stripeCheckout = $stripeCheckout;
    }

    /**
     * Checkout Session を作成し、注文へセッション情報を保存する。
     */
    public function createSessionForOrder(SkillOrder $order): string
    {
        $order->loadMissing(['skillListing', 'buyer']);

        $payload = [
            'mode' => 'payment',
            'success_url' => route('skills.checkout.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('skills.checkout.cancel', ['order' => $order->id]),
            'client_reference_id' => (string) $order->id,
            'metadata' => [
                'order_id' => (string) $order->id,
                'payment_type' => (string) $order->payment_type,
                'buyer_user_id' => (string) $order->buyer_user_id,
            ],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => (int) $order->amount,
                    'product_data' => [
                        'name' => (string) optional($order->skillListing)->title ?: 'スキル購入',
                    ],
                ],
            ]],
        ];

        $session = $this->stripeCheckout->createSession($payload);

        $order->forceFill([
            'stripe_checkout_session_id' => $session['id'] ?? null,
            'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
        ])->save();

        Log::info('Stripe checkout session created.', [
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_user_id,
            'payment_type' => $order->payment_type,
            'session_id' => $session['id'] ?? null,
            'payment_intent_id' => $session['payment_intent'] ?? null,
        ]);

        return (string) ($session['url'] ?? '');
    }
}
