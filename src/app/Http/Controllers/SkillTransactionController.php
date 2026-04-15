<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkillTransactionCompleteRequest;
use App\Http\Requests\SkillTransactionMessageRequest;
use App\Models\SkillOrder;
use App\Models\SkillOrderMessage;
use App\Models\SkillReview;
use App\Services\PayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use RuntimeException;

class SkillTransactionController extends Controller
{
    public function purchasedSkills(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $base = SkillOrder::query()
            ->where('buyer_user_id', $user->id)
            ->with(['skillListing.freelancer', 'buyer.company', 'buyer.freelancer', 'buyer.buyer']);

        $current = (clone $base)
            ->whereIn('transaction_status', [SkillOrder::TX_WAITING_PAYMENT, SkillOrder::TX_IN_PROGRESS, SkillOrder::TX_DELIVERED])
            ->orderByDesc('purchased_at')
            ->get();

        $past = (clone $base)
            ->where('transaction_status', SkillOrder::TX_COMPLETED)
            ->orderByDesc('completed_at')
            ->get();

        if (!View::exists('transactions.purchased_skills')) {
            return view('welcome');
        }

        return view('transactions.purchased_skills', [
            'currentTransactions' => $current,
            'pastTransactions' => $past,
        ]);
    }

    public function salesPerformance(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        if ($user->role !== 'freelancer') {
            abort(403);
        }

        $freelancer = $user->freelancer;
        if ($freelancer === null) {
            return redirect('/freelancer/profile')->with('error', '先にプロフィール登録が必要です');
        }

        $base = SkillOrder::query()
            ->whereHas('skillListing', function ($q) use ($freelancer) {
                $q->where('freelancer_id', $freelancer->id);
            })
            ->with(['skillListing', 'buyer.company', 'buyer.freelancer']);

        $current = (clone $base)
            ->whereIn('transaction_status', [SkillOrder::TX_WAITING_PAYMENT, SkillOrder::TX_IN_PROGRESS, SkillOrder::TX_DELIVERED])
            ->orderByDesc('purchased_at')
            ->get();

        $past = (clone $base)
            ->where('transaction_status', SkillOrder::TX_COMPLETED)
            ->orderByDesc('completed_at')
            ->get();

        $totalSales = (clone $base)
            ->where('transaction_status', SkillOrder::TX_COMPLETED)
            ->sum('amount');

        $completedCount = (clone $base)
            ->where('transaction_status', SkillOrder::TX_COMPLETED)
            ->count();

        $avgRating = SkillReview::query()
            ->whereHas('skillListing', function ($q) use ($freelancer) {
                $q->where('freelancer_id', $freelancer->id);
            })
            ->avg('rating');

        if (!View::exists('transactions.sales_performance')) {
            return view('welcome');
        }

        return view('transactions.sales_performance', [
            'currentTransactions' => $current,
            'pastTransactions' => $past,
            'totalSales' => (int) $totalSales,
            'completedCount' => (int) $completedCount,
            'avgRating' => $avgRating !== null ? round((float) $avgRating, 1) : 0,
        ]);
    }

    public function show(Request $request, SkillOrder $skill_order)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $skill_order->load([
            'skillListing.freelancer',
            'buyer.company',
            'buyer.freelancer',
            'buyer.buyer',
            'messages.sender.company',
            'messages.sender.freelancer',
        ]);

        $sellerUserId = $skill_order->skillListing?->freelancer?->user_id;
        if ((int) $user->id !== (int) $skill_order->buyer_user_id && (int) $user->id !== (int) $sellerUserId) {
            abort(403);
        }

        if (!View::exists('transactions.show')) {
            return view('welcome');
        }

        return view('transactions.show', [
            'transaction' => $skill_order,
            'messages' => $skill_order->messages,
            'isSeller' => (int) $user->id === (int) $sellerUserId,
        ]);
    }

    public function storeMessage(SkillTransactionMessageRequest $request, SkillOrder $skill_order)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $skill_order->load(['skillListing.freelancer']);
        $sellerUserId = $skill_order->skillListing?->freelancer?->user_id;

        if ((int) $user->id !== (int) $skill_order->buyer_user_id && (int) $user->id !== (int) $sellerUserId) {
            abort(403);
        }

        // 支払い待ち・完了後はチャット送信不可
        if (in_array($skill_order->transaction_status, [SkillOrder::TX_WAITING_PAYMENT, SkillOrder::TX_COMPLETED], true)) {
            $routeName = $user->role === 'buyer' ? 'buyer.transactions.show' : 'transactions.show';
            return redirect()
                ->route($routeName, ['skill_order' => $skill_order->id])
                ->with('error', '現在のステータスではメッセージ送信できません');
        }

        $validated = $request->validated();

        $attachments = $request->file('attachments', []);
        $attachmentList = is_array($attachments) ? $attachments : [];

        $attachmentNames = [];
        $attachmentPaths = [];
        foreach ($attachmentList as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $path = $file->store('transaction-attachments', 'public');
            if (!$path) {
                continue;
            }
            $attachmentPaths[] = $path;
            $attachmentNames[] = $file->getClientOriginalName();
        }

        $hasAttachments = !empty($attachmentPaths);

        SkillOrderMessage::create([
            'skill_order_id' => $skill_order->id,
            'sender_user_id' => $user->id,
            'message_type' => $hasAttachments ? 'file' : 'text',
            'body' => $validated['content'] ?? '',
            'file_name' => $hasAttachments ? json_encode($attachmentNames, JSON_UNESCAPED_UNICODE) : null,
            'file_path' => $hasAttachments ? json_encode($attachmentPaths, JSON_UNESCAPED_UNICODE) : null,
            'sent_at' => Carbon::now(),
        ]);

        return redirect()->route(
            $user->role === 'buyer' ? 'buyer.transactions.show' : 'transactions.show',
            ['skill_order' => $skill_order->id]
        )->with('success', 'メッセージを送信しました');
    }

    public function deliver(Request $request, SkillOrder $skill_order)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $skill_order->load(['skillListing.freelancer']);
        $sellerUserId = $skill_order->skillListing?->freelancer?->user_id;

        if ((int) $user->id !== (int) $sellerUserId) {
            abort(403);
        }

        if (!$skill_order->canDeliver()) {
            return redirect()
                ->route('transactions.show', ['skill_order' => $skill_order->id])
                ->with('error', '現在のステータスでは納品できません');
        }

        DB::transaction(function () use ($skill_order, $user) {
            $order = SkillOrder::query()->whereKey($skill_order->id)->lockForUpdate()->firstOrFail();
            if (!$order->canDeliver()) {
                return;
            }

            $order->transaction_status = SkillOrder::TX_DELIVERED;
            $order->delivered_at = Carbon::now();
            $order->save();

            SkillOrderMessage::create([
                'skill_order_id' => $order->id,
                'sender_user_id' => null,
                'message_type' => 'system',
                'body' => '出品者が納品しました。内容を確認して承認してください。',
                'sent_at' => Carbon::now(),
            ]);

            Log::info('Order delivered.', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'buyer_id' => $order->buyer_user_id,
                'seller_id' => optional(optional($order->skillListing)->freelancer)->user_id,
                'payment_type' => $order->payment_type,
                'result' => 'delivered',
            ]);
        });

        return redirect()
            ->route('transactions.show', ['skill_order' => $skill_order->id])
            ->with('success', '納品しました');
    }

    public function complete(SkillTransactionCompleteRequest $request, SkillOrder $skill_order, PayoutService $payoutService)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        if ((int) $user->id !== (int) $skill_order->buyer_user_id) {
            abort(403);
        }

        if (!$skill_order->canCompleteEscrow()) {
            $routeName = $user->role === 'buyer' ? 'buyer.transactions.show' : 'transactions.show';
            return redirect()
                ->route($routeName, ['skill_order' => $skill_order->id])
                ->with('error', '現在のステータスでは承認できません');
        }

        $validated = $request->validated();

        try {
            DB::transaction(function () use ($skill_order, $user, $validated, $payoutService) {
                /** @var SkillOrder $order */
                $order = SkillOrder::query()->whereKey($skill_order->id)->lockForUpdate()->firstOrFail();
                if (!$order->canCompleteEscrow()) {
                    return;
                }
                if ($order->alreadyTransferred()) {
                    return;
                }

                SkillReview::create([
                    'skill_listing_id' => $order->skill_listing_id,
                    'user_id' => $user->id,
                    'rating' => (int) $validated['rating'],
                    'body' => $validated['review'] ?? null,
                ]);

                $listing = $order->skillListing()->lockForUpdate()->first();
                if ($listing) {
                    $reviewsCount = SkillReview::query()->where('skill_listing_id', $listing->id)->count();
                    $avg = SkillReview::query()->where('skill_listing_id', $listing->id)->avg('rating');
                    $listing->reviews_count = (int) $reviewsCount;
                    $listing->rating_average = $avg !== null ? round((float) $avg, 1) : 0;
                    $listing->save();
                }

                $payoutOrder = $payoutService->transferForOrder($order);
                if ($payoutOrder->payout_status !== SkillOrder::PAYOUT_TRANSFERRED) {
                    throw new RuntimeException('transfer failed');
                }

                $order->refresh();
                $order->transaction_status = SkillOrder::TX_COMPLETED;
                $order->completed_at = Carbon::now();
                $order->save();

                $stars = str_repeat('★', (int) $validated['rating']);
                SkillOrderMessage::create([
                    'skill_order_id' => $order->id,
                    'sender_user_id' => null,
                    'message_type' => 'system',
                    'body' => "取引が完了しました。評価：{$stars}",
                    'sent_at' => Carbon::now(),
                ]);

                Log::info('Order completed.', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'buyer_id' => $order->buyer_user_id,
                    'seller_id' => optional(optional($order->skillListing)->freelancer)->user_id,
                    'payment_type' => $order->payment_type,
                    'transfer_id' => $order->stripe_transfer_id,
                    'result' => 'completed',
                ]);
            });
        } catch (\Throwable $e) {
            $routeName = $user->role === 'buyer' ? 'buyer.transactions.show' : 'transactions.show';
            return redirect()->route($routeName, ['skill_order' => $skill_order->id])
                ->with('error', '取引完了処理に失敗しました: ' . $e->getMessage());
        }

        return redirect()->route(
            $user->role === 'buyer' ? 'buyer.transactions.show' : 'transactions.show',
            ['skill_order' => $skill_order->id]
        )->with('success', '取引を完了しました');
    }
}
