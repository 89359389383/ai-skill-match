<?php

namespace App\Services;

use App\Contracts\StripeTransferClientInterface;
use App\Models\SkillOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PayoutService
{
    public const PLATFORM_FEE_RATE = 0.10;

    private StripeTransferClientInterface $transferClient;

    public function __construct(StripeTransferClientInterface $transferClient)
    {
        $this->transferClient = $transferClient;
    }

    /**
     * @return array{platform_fee:int,seller_amount:int}
     */
    public function calculateAmounts(int $amount): array
    {
        $platformFee = (int) floor($amount * self::PLATFORM_FEE_RATE);
        $sellerAmount = max(0, $amount - $platformFee);

        return [
            'platform_fee' => $platformFee,
            'seller_amount' => $sellerAmount,
        ];
    }

    /**
     * Escrow 取引の販売者送金。
     */
    public function transferForOrder(SkillOrder $order): SkillOrder
    {
        return DB::transaction(function () use ($order): SkillOrder {
            /** @var SkillOrder $locked */
            $locked = SkillOrder::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($locked->alreadyTransferred()) {
                return $locked;
            }

            $locked->loadMissing(['skillListing.freelancer']);
            $locked->transfer_attempts = (int) $locked->transfer_attempts + 1;

            $connectedAccountId = $locked->skillListing?->freelancer?->stripe_connect_account_id;
            if (empty($connectedAccountId)) {
                $locked->payout_status = SkillOrder::PAYOUT_FAILED;
                $locked->last_transfer_error = 'Stripe connected account が未設定のため送金できません。';
                $locked->save();

                Log::warning('Payout failed: missing connected account.', [
                    'order_id' => $locked->id,
                    'buyer_id' => $locked->buyer_user_id,
                    'seller_id' => optional(optional($locked->skillListing)->freelancer)->user_id,
                    'payment_type' => $locked->payment_type,
                    'result' => 'failed',
                ]);

                throw new RuntimeException('connected account missing');
            }

            $amounts = $this->calculateAmounts((int) $locked->amount);

            try {
                $transfer = $this->transferClient->createTransfer([
                    'amount' => $amounts['seller_amount'],
                    'currency' => 'jpy',
                    'destination' => $connectedAccountId,
                    'metadata' => [
                        'order_id' => (string) $locked->id,
                        'payment_type' => (string) $locked->payment_type,
                    ],
                ]);

                $locked->stripe_transfer_id = $transfer['id'] ?? null;
                $locked->transferred_at = Carbon::now();
                $locked->payout_status = SkillOrder::PAYOUT_TRANSFERRED;
                $locked->last_transfer_error = null;
                $locked->save();

                Log::info('Payout transfer succeeded.', [
                    'order_id' => $locked->id,
                    'buyer_id' => $locked->buyer_user_id,
                    'seller_id' => optional(optional($locked->skillListing)->freelancer)->user_id,
                    'payment_type' => $locked->payment_type,
                    'transfer_id' => $locked->stripe_transfer_id,
                    'result' => 'success',
                ]);

                return $locked;
            } catch (Throwable $e) {
                $locked->payout_status = SkillOrder::PAYOUT_FAILED;
                $locked->last_transfer_error = mb_substr($e->getMessage(), 0, 1000);
                $locked->save();

                Log::error('Payout transfer failed.', [
                    'order_id' => $locked->id,
                    'buyer_id' => $locked->buyer_user_id,
                    'seller_id' => optional(optional($locked->skillListing)->freelancer)->user_id,
                    'payment_type' => $locked->payment_type,
                    'result' => 'failed',
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }
}
