<?php

namespace App\Http\Controllers;

use App\Models\SkillOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature', '');
        $secret = (string) config('services.stripe.webhook_secret');
        $verbose = $this->shouldLogWebhookVerbose();

        if ($verbose) {
            $decodedPreview = json_decode($payload, true);
            Log::info('Stripe webhook HTTP inbound (verbose).', [
                'app_env' => config('app.env'),
                'app_debug' => (bool) config('app.debug'),
                'method' => $request->method(),
                'path' => $request->path(),
                'full_url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'forwarded_for' => $request->header('X-Forwarded-For'),
                'user_agent' => $request->userAgent(),
                'content_type' => $request->header('Content-Type'),
                'content_length_header' => $request->header('Content-Length'),
                'payload_bytes' => strlen($payload),
                'payload_sha256_hex' => hash('sha256', $payload),
                'payload_json_ok' => is_array($decodedPreview),
                'payload_preview_keys' => is_array($decodedPreview) ? array_keys($decodedPreview) : null,
                'unverified_event_id' => is_array($decodedPreview) ? ($decodedPreview['id'] ?? null) : null,
                'unverified_event_type' => is_array($decodedPreview) ? ($decodedPreview['type'] ?? null) : null,
                'unverified_livemode' => is_array($decodedPreview) ? ($decodedPreview['livemode'] ?? null) : null,
                'stripe_signature_header_length' => strlen($signature),
                'stripe_signature_header_present' => $signature !== '',
                'webhook_secret_configured' => $secret !== '',
                'webhook_secret_length' => strlen($secret),
            ]);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed.', [
                'error' => $e->getMessage(),
                'exception' => $e::class,
                'payload_bytes' => strlen($payload),
                'payload_sha256_hex' => hash('sha256', $payload),
                'stripe_signature_header_length' => strlen($signature),
                'stripe_signature_header_present' => $signature !== '',
                'webhook_secret_configured' => $secret !== '',
            ]);

            return response()->json(['message' => 'invalid payload'], 400);
        }

        Log::info('Webhook received.', [
            'event_id' => $event->id,
            'event_type' => $event->type,
            'livemode' => $event->livemode ?? null,
            'request_id' => $event->request ?? null,
        ]);

        if ($verbose) {
            Log::info('Webhook event detail (verbose).', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'created' => $event->created ?? null,
                'livemode' => $event->livemode ?? null,
                'api_version' => $event->api_version ?? null,
            ]);
        }

        if ($event->type !== 'checkout.session.completed') {
            Log::info('Webhook ignored (event type mismatch).', [
                'event_id' => $event->id,
                'event_type' => $event->type,
            ]);

            return response()->json(['message' => 'ignored']);
        }

        $session = $event->data->object;
        $metadataRaw = $session->metadata ?? null;
        // Stripe SDK の metadata は StripeObject のことがあり、(array)キャストだと内部プロパティだけになるため toArray() を優先する
        $metadata = (is_object($metadataRaw) && method_exists($metadataRaw, 'toArray'))
            ? $metadataRaw->toArray()
            : (array) $metadataRaw;

        $orderId = (int) ($metadata['order_id'] ?? 0);

        Log::info('Order ID from metadata.', [
            'event_id' => $event->id,
            'order_id' => $orderId > 0 ? $orderId : null,
            'metadata_keys' => array_keys($metadata),
        ]);

        if ($verbose) {
            Log::info('Webhook checkout.session object (verbose).', [
                'event_id' => $event->id,
                'session_id' => $session->id ?? null,
                'payment_status' => $session->payment_status ?? null,
                'status' => $session->status ?? null,
                'mode' => $session->mode ?? null,
                'client_reference_id' => $session->client_reference_id ?? null,
                'payment_intent' => is_string($session->payment_intent ?? null)
                    ? $session->payment_intent
                    : (is_object($session->payment_intent ?? null) ? '[object]' : null),
                'metadata_keys' => array_keys($metadata),
                'metadata_order_id_raw' => $metadata['order_id'] ?? null,
            ]);
        }

        if ($orderId <= 0) {
            Log::error('Stripe webhook missing order_id metadata.', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'session_id' => $session->id ?? null,
                'metadata_keys' => array_keys($metadata),
            ]);

            return response()->json(['message' => 'missing order id'], 422);
        }

        try {
            DB::transaction(function () use ($orderId, $event, $session): void {
                /** @var SkillOrder|null $order */
                $order = SkillOrder::query()->whereKey($orderId)->lockForUpdate()->first();
                if (!$order) {
                    Log::error('Order not found.', [
                        'event_id' => $event->id,
                        'order_id' => $orderId,
                    ]);

                    return;
                }

                Log::info('Stripe webhook applying.', [
                    'event_id' => $event->id,
                    'order_id' => $order->id,
                    'order_status_before' => $order->status,
                    'transaction_status_before' => $order->transaction_status,
                    'payout_status_before' => $order->payout_status,
                    'payment_type' => $order->payment_type,
                    'stripe_checkout_session_id_before' => $order->stripe_checkout_session_id,
                ]);

                // event_id 単位で冪等化
                $duplicatedEvent = SkillOrder::query()
                    ->where('stripe_webhook_event_id', $event->id)
                    ->where('id', '!=', $order->id)
                    ->exists();

                if ($duplicatedEvent || $order->stripe_webhook_event_id === $event->id) {
                    Log::info('Stripe webhook skipped as duplicate event.', [
                        'event_id' => $event->id,
                        'order_id' => $order->id,
                    ]);
                    return;
                }

                $order->status = SkillOrder::STATUS_PAID;
                $order->paid_at = $order->paid_at ?? Carbon::now();
                $order->stripe_checkout_session_id = $session->id ?? $order->stripe_checkout_session_id;
                $order->stripe_payment_intent_id = is_string($session->payment_intent ?? null)
                    ? $session->payment_intent
                    : ($order->stripe_payment_intent_id);
                $order->stripe_webhook_event_id = $event->id;
                $order->last_webhook_type = $event->type;
                $order->last_webhook_received_at = Carbon::now();

                // 要件通り、checkout.session.completed 受信時点で「completed」に更新する
                if ($order->transaction_status !== SkillOrder::TX_COMPLETED) {
                    $order->transaction_status = SkillOrder::TX_COMPLETED;
                    $order->completed_at = $order->completed_at ?? Carbon::now();
                }

                $order->save();

                Log::info('Order updated successfully.', [
                    'event_id' => $event->id,
                    'order_id' => $order->id,
                    'buyer_id' => $order->buyer_user_id,
                    'payment_type' => $order->payment_type,
                    'session_id' => $order->stripe_checkout_session_id,
                    'payment_intent_id' => $order->stripe_payment_intent_id,
                    'transaction_status_after' => $order->transaction_status,
                    'result' => 'paid_and_completed',
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Stripe webhook apply failed.', [
                'event_id' => $event->id,
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'failed'], 500);
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * stripe listen 等で到達確認するための詳細ログ。
     * 本番では冗長・情報量が多いため、local / testing または app.debug のときのみ有効。
     */
    private function shouldLogWebhookVerbose(): bool
    {
        if (app()->environment(['local', 'testing'])) {
            return true;
        }

        return (bool) config('app.debug');
    }
}
