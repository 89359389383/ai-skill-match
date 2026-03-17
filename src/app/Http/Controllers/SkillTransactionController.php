<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkillTransactionCompleteRequest;
use App\Http\Requests\SkillTransactionMessageRequest;
use App\Models\SkillOrder;
use App\Models\SkillOrderMessage;
use App\Models\SkillReview;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SkillTransactionController extends Controller
{
    /**
     * 購入したスキル一覧（購入者向け）
     * - 現在取引中: in_progress / delivered
     * - 過去の取引: completed
     */
    public function purchasedSkills(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $base = SkillOrder::query()
            ->where('buyer_user_id', $user->id)
            ->with(['skillListing.freelancer', 'buyer.company', 'buyer.freelancer']);

        $current = (clone $base)
            ->whereIn('transaction_status', ['in_progress', 'delivered'])
            ->orderByDesc('purchased_at')
            ->get();

        $past = (clone $base)
            ->where('transaction_status', 'completed')
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

    /**
     * 販売実績（出品者向け）
     */
    public function salesPerformance(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // 出品者はフリーランスのみ（skill_listings.freelancer_id 前提）
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
            ->whereIn('transaction_status', ['in_progress', 'delivered'])
            ->orderByDesc('purchased_at')
            ->get();

        $past = (clone $base)
            ->where('transaction_status', 'completed')
            ->orderByDesc('completed_at')
            ->get();

        $totalSales = (clone $base)
            ->where('transaction_status', 'completed')
            ->sum('amount');

        $completedCount = (clone $base)
            ->where('transaction_status', 'completed')
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

    /**
     * 取引チャット画面
     */
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
            'messages.sender.company',
            'messages.sender.freelancer',
        ]);

        $sellerUserId = $skill_order->skillListing?->freelancer?->user_id;

        // 当事者チェック（購入者 or 出品者のみ）
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

    /**
     * メッセージ送信（テキスト）
     */
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

        // 取引完了後は送信不可（仕様：入力エリアは完了まで）
        if ($skill_order->transaction_status === 'completed') {
            return redirect()
                ->route('transactions.show', ['skill_order' => $skill_order->id])
                ->with('error', '取引が完了しているため送信できません');
        }

        $validated = $request->validated();

        SkillOrderMessage::create([
            'skill_order_id' => $skill_order->id,
            'sender_user_id' => $user->id,
            'message_type' => 'text',
            'body' => $validated['content'],
            'sent_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('transactions.show', ['skill_order' => $skill_order->id])
            ->with('success', 'メッセージを送信しました');
    }

    /**
     * 納品（出品者のみ、in_progress -> delivered）
     */
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

        if ($skill_order->transaction_status !== 'in_progress') {
            return redirect()
                ->route('transactions.show', ['skill_order' => $skill_order->id])
                ->with('error', '現在のステータスでは納品できません');
        }

        DB::transaction(function () use ($skill_order) {
            $skill_order->transaction_status = 'delivered';
            $skill_order->delivered_at = Carbon::now();
            $skill_order->save();

            SkillOrderMessage::create([
                'skill_order_id' => $skill_order->id,
                'sender_user_id' => null,
                'message_type' => 'system',
                'body' => '出品者が納品しました。内容を確認して承認してください。',
                'sent_at' => Carbon::now(),
            ]);
        });

        return redirect()
            ->route('transactions.show', ['skill_order' => $skill_order->id])
            ->with('success', '納品しました');
    }

    /**
     * 承認＋評価（購入者のみ、delivered -> completed）
     */
    public function complete(SkillTransactionCompleteRequest $request, SkillOrder $skill_order)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        if ((int) $user->id !== (int) $skill_order->buyer_user_id) {
            abort(403);
        }

        if ($skill_order->transaction_status !== 'delivered') {
            return redirect()
                ->route('transactions.show', ['skill_order' => $skill_order->id])
                ->with('error', '現在のステータスでは承認できません');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($skill_order, $user, $validated) {
            // レビューを保存（1取引=1レビューを厳密にする制約は将来追加）
            SkillReview::create([
                'skill_listing_id' => $skill_order->skill_listing_id,
                'user_id' => $user->id,
                'rating' => (int) $validated['rating'],
                'body' => $validated['review'] ?? null,
            ]);

            // 出品の集計を更新（簡易に再集計）
            $listing = $skill_order->skillListing()->lockForUpdate()->first();
            if ($listing) {
                $reviewsCount = SkillReview::query()->where('skill_listing_id', $listing->id)->count();
                $avg = SkillReview::query()->where('skill_listing_id', $listing->id)->avg('rating');
                $listing->reviews_count = (int) $reviewsCount;
                $listing->rating_average = $avg !== null ? round((float) $avg, 1) : 0;
                $listing->save();
            }

            $skill_order->transaction_status = 'completed';
            $skill_order->completed_at = Carbon::now();
            $skill_order->save();

            $stars = str_repeat('★', (int) $validated['rating']);

            SkillOrderMessage::create([
                'skill_order_id' => $skill_order->id,
                'sender_user_id' => null,
                'message_type' => 'system',
                'body' => "取引が完了しました。評価：{$stars}",
                'sent_at' => Carbon::now(),
            ]);
        });

        return redirect()
            ->route('transactions.show', ['skill_order' => $skill_order->id])
            ->with('success', '取引を完了しました');
    }
}

