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

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed.', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'invalid payload'], 400);
        }

        Log::info('Stripe webhook received.', [
            'event_id' => $event->id,
            'event_type' => $event->type,
        ]);

        if ($event->type !== 'checkout.session.completed') {
            return response()->json(['message' => 'ignored']);
        }

        $session = $event->data->object;
        $metadata = (array) ($session->metadata ?? []);
        $orderId = (int) ($metadata['order_id'] ?? 0);

        if ($orderId <= 0) {
            Log::error('Stripe webhook missing order_id metadata.', [
                'event_id' => $event->id,
                'event_type' => $event->type,
            ]);

            return response()->json(['message' => 'missing order id'], 422);
        }

        try {
            DB::transaction(function () use ($orderId, $event, $session): void {
                /** @var SkillOrder|null $order */
                $order = SkillOrder::query()->whereKey($orderId)->lockForUpdate()->first();
                if (!$order) {
                    Log::error('Stripe webhook target order not found.', [
                        'event_id' => $event->id,
                        'order_id' => $orderId,
                    ]);

                    return;
                }

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

                if ($order->status === SkillOrder::STATUS_PAID) {
                    $order->last_webhook_type = $event->type;
                    $order->last_webhook_received_at = Carbon::now();
                    $order->stripe_webhook_event_id = $event->id;
                    $order->save();

                    Log::info('Stripe webhook skipped because order already paid.', [
                        'event_id' => $event->id,
                        'order_id' => $order->id,
                    ]);
                    return;
                }

                $order->status = SkillOrder::STATUS_PAID;
                $order->paid_at = Carbon::now();
                $order->stripe_checkout_session_id = $session->id ?? $order->stripe_checkout_session_id;
                $order->stripe_payment_intent_id = is_string($session->payment_intent ?? null)
                    ? $session->payment_intent
                    : ($order->stripe_payment_intent_id);
                $order->stripe_webhook_event_id = $event->id;
                $order->last_webhook_type = $event->type;
                $order->last_webhook_received_at = Carbon::now();
                if ($order->payment_type === SkillOrder::PAYMENT_TYPE_ESCROW) {
                    $order->transaction_status = SkillOrder::TX_IN_PROGRESS;
                }
                $order->save();

                Log::info('Stripe webhook applied successfully.', [
                    'event_id' => $event->id,
                    'order_id' => $order->id,
                    'buyer_id' => $order->buyer_user_id,
                    'payment_type' => $order->payment_type,
                    'session_id' => $order->stripe_checkout_session_id,
                    'payment_intent_id' => $order->stripe_payment_intent_id,
                    'result' => 'paid',
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
}
