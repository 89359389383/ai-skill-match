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
        // NOTE:
        // - 失敗状態（PAYOUT_FAILED）は「必ず永続化」する必要があるため、
        //   DB::transaction の外側で catch して保存する構造に変更する。
        // - トランザクション内では throw のみを行い、ロールバックにより失敗保存が無効化されないようにする。
        try {
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
                    $failureMessage = 'Stripe connected account が未設定のため送金できません。';

                    // ログ出力は維持（保存はトランザクション外で行う）
                    Log::warning('Payout failed: missing connected account.', [
                        'order_id' => $locked->id,
                        'buyer_id' => $locked->buyer_user_id,
                        'seller_id' => optional(optional($locked->skillListing)->freelancer)->user_id,
                        'payment_type' => $locked->payment_type,
                        'result' => 'failed',
                    ]);

                    throw new RuntimeException($failureMessage);
                }

                $amounts = $this->calculateAmounts((int) $locked->amount);

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
            });
        } catch (Throwable $e) {
            // 失敗状態は「必ずDBに保存」
            $failedOrder = SkillOrder::query()
                ->with(['skillListing.freelancer'])
                ->find($order->id);

            if ($failedOrder) {

                // 競合対策：catch側でFAILED上書きを抑止するために、最新状態に基づき判定
                $failedOrder->refresh(); // 要件①：必ず最新状態を取得

                // 🔴 送金済みチェック（強化版：要件②）
                $alreadyTransferred =
                    $failedOrder->payout_status === SkillOrder::PAYOUT_TRANSFERRED
                    || !empty($failedOrder->stripe_transfer_id);

                if ($alreadyTransferred) {
                    // 送金済み状態は絶対に上書きしない（要件④のスキップ分岐）
                    Log::warning('Payout failed but already transferred; skip FAILED overwrite.', [
                        'order_id' => $failedOrder->id,
                        'seller_id' => optional($failedOrder->skillListing?->freelancer)->user_id,
                        'result' => 'skip_failed_overwrite',
                        'error' => $e->getMessage(),
                    ]);
                } else {
                    // それ以外の場合のみ FAILED と last_transfer_error を保存（要件④）
                    $failedOrder->payout_status = SkillOrder::PAYOUT_FAILED;
                    $failedOrder->last_transfer_error = mb_substr($e->getMessage(), 0, 1000);
                    $failedOrder->save();
                }
            }

            // ログ出力は維持（要件⑤：例外は必ず throw して呼び出し元へ伝播）
            Log::error('Payout transfer failed.', [
                'order_id' => $order->id,
                'seller_id' => optional($failedOrder?->skillListing?->freelancer)->user_id,
                'result' => 'failed',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
