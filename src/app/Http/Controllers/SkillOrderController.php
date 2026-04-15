<?php

namespace App\Http\Controllers;

use App\Models\SkillListing;
use App\Services\SkillOrderService;
use Illuminate\Http\Request;

class SkillOrderController extends Controller
{
    /**
     * スキル購入（ログイン必須）。
     *
     * 現段階:
     * - 決済連携が未実装のため「注文レコードを作る」まで
     * - 成功後は一覧 or 詳細に戻す（UIが固まり次第調整）
     */
    public function store(Request $request, SkillListing $skill_listing, SkillOrderService $service)
    {
        // “ログインしているユーザー” は auth.any ミドルウェアで確定させているため
        // request()->user() で取得できる（Auth::shouldUse が効く）
        $user = $request->user();

        // 念のため（ミドルウェアが外れていた場合の防御）
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // 非公開のスキル購入は不可（本人のみ可）
        if ((int) $skill_listing->status !== 1) {
            $viewerFreelancerId = $user->role === 'freelancer' ? $user->freelancer?->id : null;
            if (!$viewerFreelancerId || (int) $skill_listing->freelancer_id !== (int) $viewerFreelancerId) {
                abort(404);
            }
        }

        // ここで「購入ボタン押下」の最低限を validate しておく
        // 将来: クーポン、数量、要望などを追加する
        $request->validate([
            'confirm' => ['nullable', 'boolean'],
        ]);

        $order = $service->purchase($user, $skill_listing);

        // 購入後は取引（チャット）画面へ遷移する（buyer は専用URLへ）
        $routeName = $user->role === 'buyer' ? 'buyer.transactions.show' : 'transactions.show';
        return redirect()->route($routeName, ['skill_order' => $order->id]);
    }
}

