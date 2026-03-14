<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkillInquiryRequest;
use App\Models\SkillListing;
use App\Services\SkillInquiryService;
use Illuminate\Http\Request;

class SkillInquiryController extends Controller
{
    /**
     * スキルへの問い合わせ（ログイン必須）。
     *
     * 現段階:
     * - 問い合わせ保存先（テーブル/チャット連携）が未確定
     * - そのため「入力チェック→成功扱いで戻す」までを先に用意する
     */
    public function store(SkillInquiryRequest $request, SkillListing $skill_listing, SkillInquiryService $service)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // 問い合わせ本文の必須チェックは FormRequest 側へ移動
        $validated = $request->validated();

        $service->store($user, $skill_listing, $validated['message']);

        return redirect()->route('skills.show', ['skill_listing' => $skill_listing->id]);
    }
}

