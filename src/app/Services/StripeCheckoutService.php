<?php

namespace App\Services;

use App\Contracts\StripeCheckoutClientInterface;
use App\Models\SkillOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        Log::info('StripeCheckoutService createSessionForOrder begin.', [
            'order_id' => $order->id,
            'buyer_id' => $order->buyer_user_id,
            'listing_id' => $order->skill_listing_id,
            'amount' => (int) $order->amount,
            'payment_type' => $order->payment_type,
            'stripe_checkout_session_id_before' => $order->stripe_checkout_session_id,
        ]);

        // slot によりセッションCookie名が分割されるため、
        // Stripe へ外部遷移する前に決まっている slot を success/cancel URL に引き継ぎ、
        // 戻ってきた直後も同じログイン状態を維持する。
        $slot = request()->attributes->get('slot')
            ?? request()->attributes->get('resolved_slot')
            ?? request()->query('slot')
            ?? request()->input('slot');
        $slot = is_string($slot) ? trim($slot) : null;

        $successUrl = route('skills.checkout.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('skills.checkout.cancel', ['order' => $order->id]);

        if (is_string($slot) && $slot !== '' && !Str::contains($successUrl, 'slot=')) {
            $successUrl .= '&slot=' . urlencode($slot);
        }

        if (is_string($slot) && $slot !== '' && !Str::contains($cancelUrl, 'slot=')) {
            $cancelUrl .= '?slot=' . urlencode($slot);
        }

        $payload = [
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
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

        // payloadは秘密情報を含まない想定（ログはキー中心）
        Log::info('StripeCheckoutService payload prepared.', [
            'order_id' => $order->id,
            'mode' => $payload['mode'] ?? null,
            'client_reference_id' => $payload['client_reference_id'] ?? null,
            'metadata_order_id' => $payload['metadata']['order_id'] ?? null,
            'unit_amount' => $payload['line_items'][0]['price_data']['unit_amount'] ?? null,
            'has_success_url' => !empty($payload['success_url'] ?? null),
            'has_cancel_url' => !empty($payload['cancel_url'] ?? null),
        ]);

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

        // 重要: Stripe が返す Checkout URL はハッシュ（#...）を含めて完全一致で返却する必要があります。
        // URL を一切加工せず、そのまま返します。
        $url = (string) ($session['url'] ?? '');
        $originalUrlHadHash = $url !== '' && strpos($url, '#') !== false;

        Log::info('StripeCheckoutService createSessionForOrder returning checkout url.', [
            'order_id' => $order->id,
            'stripe_checkout_session_id' => $session['id'] ?? null,
            'payment_intent_id' => $session['payment_intent'] ?? null,
            'returned_url_length' => strlen($url),
            'original_url_had_hash' => $originalUrlHadHash,
            'returned_url_has_hash' => strpos($url, '#') !== false,
        ]);

        return $url;
    }
}
