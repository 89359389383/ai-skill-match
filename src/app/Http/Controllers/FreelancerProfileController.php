<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\FreelancerRegisterRequest;
use App\Http\Requests\FreelancerProfileUpdateRequest;
use App\Models\Thread;
use App\Services\FreelancerProfileService;
use App\Models\Skill;

class FreelancerProfileController extends Controller
{
    /**
     * 初回プロフィール登録画面を表示する
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'freelancer') {
            abort(403);
        }

        if ($user->freelancer()->exists()) {
            return redirect('/freelancer/jobs');
        }

        return view('freelancer.profile.create', [
            'user' => $user,
        ]);
    }

    /**
     * プロフィール登録
     */
    public function store(FreelancerRegisterRequest $request, FreelancerProfileService $freelancerProfileService)
    {
        $user = Auth::user();

        if ($user->role !== 'freelancer') {
            abort(403);
        }

        if ($user->freelancer()->exists()) {
            return redirect('/freelancer/jobs');
        }

        $validated = $request->validated();
        $validated['icon'] = $request->file('icon');

        $freelancerProfileService->register($user, $validated);

        return redirect('/freelancer/jobs')->with('success', 'プロフィール登録が完了しました');
    }

    /**
     * プロフィール設定画面
     */
    public function edit()
    {
        $user = Auth::user();

        if ($user->role !== 'freelancer') {
            abort(403);
        }

        $freelancer = $user->freelancer;
        if ($freelancer) {
            $freelancer->load(['skills', 'customSkills', 'portfolios']);
        }

        // 未読スカウト数を取得（ヘッダー用）
        $unreadScoutCount = 0;
        $unreadApplicationCount = 0;
        if ($freelancer !== null) {
            $unreadScoutCount = Thread::query()
                ->where('freelancer_id', $freelancer->id)
                ->whereNull('job_id')
                ->where('is_unread_for_freelancer', true)
                ->count();

            // 未読応募数を取得（ヘッダー用）
            $unreadApplicationCount = Thread::query()
                ->where('freelancer_id', $freelancer->id)
                ->whereNotNull('job_id')
                ->where('is_unread_for_freelancer', true)
                ->count();
        }

        // ユーザー名の最初の文字を取得（アバター表示用）
        $userInitial = 'U';
        if ($freelancer !== null && !empty($freelancer->display_name)) {
            $userInitial = mb_substr($freelancer->display_name, 0, 1);
        } elseif (!empty($user->email)) {
            $userInitial = mb_substr($user->email, 0, 1);
        }

        return view('freelancer.profile.settings', [
            'user' => $user,
            'freelancer' => $freelancer,
            // 全スキル一覧（プロフィール設定で複数選択させるため）
            'allSkills' => Skill::orderBy('name')->get(),
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }

    /**
     * プロフィール設定の更新
     */
    public function update(FreelancerProfileUpdateRequest $request, FreelancerProfileService $freelancerProfileService)
    {
        $user = Auth::user();

        if ($user->role !== 'freelancer') {
            abort(403);
        }

        if (!$user->freelancer()->exists()) {
            return redirect('/freelancer/profile')->with('error', '先にプロフィールを登録してください');
        }

        $validated = $request->validated();

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon');
        }

        $freelancerProfileService->update($user->freelancer, $validated);

        return redirect()->route('freelancer.profile.settings')->with('success', 'プロフィールを更新しました');
    }
}

