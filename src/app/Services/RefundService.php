<?php

namespace App\Services;

use App\Models\SkillOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;
use App\Contracts\StripeRefundClientInterface;
use App\Contracts\StripeTransferClientInterface;

class RefundService
{
    private StripeRefundClientInterface $refundClient;
    private StripeTransferClientInterface $transferClient;

    public function __construct(StripeRefundClientInterface $refundClient, StripeTransferClientInterface $transferClient)
    {
        $this->refundClient = $refundClient;
        $this->transferClient = $transferClient;
    }

    public function refundOrder(SkillOrder $order, int $refundAmount, string $refundReason): SkillOrder
    {
        return DB::transaction(function () use ($order, $refundAmount, $refundReason) {
            $order = SkillOrder::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->refund_status === 'refunded') {
                return $order;
            }

            $order->refund_status = 'refunding';
            $order->save();

            try {
                // Call Stripe API
                $refund = $this->refundClient->createRefund([
                    'payment_intent' => $order->stripe_payment_intent_id,
                    'amount' => $refundAmount,
                    'reason' => $refundReason === 'requested_by_customer' ? 'requested_by_customer' : null,
                ]);

                $order->stripe_refund_id = $refund['id'] ?? null;
                if ($refundAmount < $order->amount) {
                    $order->refund_status = 'partial_refunded';
                } else {
                    $order->refund_status = 'refunded';
                }
                $order->refunded_at = Carbon::now();
                
                // If already transferred, reverse the transfer (not checked in current basic test but good for future)
                if ($order->payout_status === SkillOrder::PAYOUT_TRANSFERRED && $order->stripe_transfer_id) {
                    $reversalAmount = (int) ($refundAmount * 0.9);
                    $reversal = $this->transferClient->createReversal($order->stripe_transfer_id, [
                        'amount' => $reversalAmount,
                    ]);
                    $order->transfer_reversed_at = Carbon::now();
                    $order->reversal_amount = $reversalAmount;
                }

                $order->save();
                return $order;
            } catch (\Exception $e) {
                Log::error('Refund failed', ['error' => $e->getMessage()]);
                $order->refund_status = 'failed';
                $order->save();
                throw $e;
            }
        });
    }
}
