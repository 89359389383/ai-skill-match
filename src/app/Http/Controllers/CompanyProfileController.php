<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CompanyRegisterRequest;
use App\Http\Requests\CompanyProfileUpdateRequest;
use App\Models\Thread;
use App\Services\CompanyProfileService;

class CompanyProfileController extends Controller
{
    /**
     * 企業プロフィール登録画面を表示する（表示のみ）
     */
    public function create()
    {
        // 認証ユーザーを取得する（企業である前提）
        $user = Auth::user();

        // 企業ロール以外は拒否する
        if ($user->role !== 'company') {
            // 企業以外はアクセス禁止にする
            abort(403);
        }

        // すでに企業プロフィールがある場合は再登録を防ぐ
        if ($user->company()->exists()) {
            // 企業向けのトップ（フリーランス一覧）へ戻す
            return redirect('/company/freelancers');
        }

        // 登録フォームを返すだけ
        return view('company.profile.create');
    }

    /**
     * 企業プロフィールを登録する（store は Service へ委譲）
     */
    public function store(CompanyRegisterRequest $request, CompanyProfileService $companyProfileService)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        Log::info('[企業登録] プロフィール store 受信', [
            'user_id' => $user?->id,
            'role' => $user?->role,
            'url' => $request->fullUrl(),
            'referer' => $request->header('Referer'),
            'slot_query' => $request->query('slot'),
            'slot_post_present' => $request->has('slot'),
            'has_multipart_icon' => $request->hasFile('icon'),
            'session_cookie_name' => config('session.cookie'),
        ]);

        // 企業ロール以外は拒否する
        if ($user->role !== 'company') {
            Log::warning('[企業登録] プロフィール store 403（企業ロール以外）', [
                'user_id' => $user?->id,
                'role' => $user?->role,
            ]);
            // 企業以外は拒否する
            abort(403);
        }

        // すでに登録済みなら登録させない
        if ($user->company()->exists()) {
            Log::info('[企業登録] プロフィール store 既存あり→リダイレクト', [
                'user_id' => $user->id,
                'company_id' => $user->company?->id,
            ]);
            // 企業向け一覧へ戻す
            return redirect('/company/freelancers');
        }

        // バリデーションを行う（CompanyRegisterRequest に委譲）
        $validated = $request->validated();
        $validated['icon'] = $request->file('icon');

        // 実処理を Service へ委譲する（CompanyProfileService::register）
        $company = $companyProfileService->register($user, $validated);

        Log::info('[企業登録] プロフィール store 完了', [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'company_name' => $company->name,
        ]);

        // 企業向けフリーランス一覧へ遷移する
        return redirect('/company/freelancers')->with('success', '企業プロフィールの登録が完了しました');
    }

    /**
     * 企業プロフィール設定画面（routes 対応のため最低限）
     *
     * ※設計書にはないが、web.php に存在するため画面表示だけ用意する
     */
    public function edit()
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        Log::info('[企業登録/設定] プロフィール設定画面 edit', [
            'user_id' => $user?->id,
            'role' => $user?->role,
            'url' => request()->fullUrl(),
        ]);

        // 企業ロール以外は拒否する
        if ($user->role !== 'company') {
            Log::warning('[企業登録/設定] プロフィール設定画面 403', ['user_id' => $user?->id]);
            abort(403);
        }

        // 企業プロフィールが無い場合は先に登録へ誘導する
        if (!$user->company()->exists()) {
            Log::info('[企業登録/設定] プロフィール設定画面 企業なし→新規登録へ', ['user_id' => $user->id]);
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        $company = $user->company;

        // 応募に関連するthreadの未読数（企業側）
        $unreadApplicationCount = Thread::query()
            ->where('company_id', $company->id)
            ->whereNotNull('job_id') // 応募はjob_idが必須
            ->where('is_unread_for_company', true)
            ->count();

        // スカウトに関連するthreadの未読数（企業側、job_idがnullのもの）
        $unreadScoutCount = Thread::query()
            ->where('company_id', $company->id)
            ->whereNull('job_id') // スカウトはjob_idがnull
            ->where('is_unread_for_company', true)
            ->count();

        // ユーザー名の最初の文字を取得（アバター表示用）
        $userInitial = '企';
        if ($company !== null && !empty($company->name)) {
            $userInitial = mb_substr($company->name, 0, 1);
        } elseif (!empty($user->email)) {
            $userInitial = mb_substr($user->email, 0, 1);
        }

        // 設定画面のビューを返す
        return view('company.profile.settings', [
            // 企業プロフィールを渡す
            'company' => $company,
            // ヘッダー用未読数
            'unreadApplicationCount' => $unreadApplicationCount,
            'unreadScoutCount' => $unreadScoutCount,
            // ユーザー情報
            'userInitial' => $userInitial,
        ]);
    }

    /**
     * 企業プロフィール設定を更新する（routes 対応のため最低限）
     *
     * ※設計書にはないが、web.php に存在するため最低限を用意する
     */
    public function update(CompanyProfileUpdateRequest $request, CompanyProfileService $companyProfileService)
    {
        // 認証ユーザーを取得する
        $user = Auth::user();

        Log::info('[企業登録/設定] プロフィール update 受信', [
            'user_id' => $user?->id,
            'company_id' => $user?->company?->id,
            'url' => $request->fullUrl(),
            'has_icon_file' => $request->hasFile('icon'),
        ]);

        // 企業ロール以外は拒否する
        if ($user->role !== 'company') {
            Log::warning('[企業登録/設定] プロフィール update 403', ['user_id' => $user?->id, 'role' => $user?->role]);
            abort(403);
        }

        // 企業プロフィールが無い場合は先に登録へ誘導する
        if (!$user->company()->exists()) {
            Log::info('[企業登録/設定] プロフィール update 企業レコードなし→登録へ', ['user_id' => $user->id]);
            return redirect('/company/profile')->with('error', '先に企業プロフィールを登録してください');
        }

        // 更新用のバリデーションを行う（最低限 / FormRequest に委譲）
        $validated = $request->validated();

        // アイコンがある場合だけ icon_path を差し替える（フォーム側は必須化する想定）
        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon');
        }

        // 企業プロフィールを更新する
        $companyProfileService->update($user->company, $validated);

        Log::info('[企業登録/設定] プロフィール update 完了', [
            'user_id' => $user->id,
            'company_id' => $user->company->id,
        ]);

        // 設定画面へ戻す
        return redirect()->route('company.profile.settings')->with('success', '企業プロフィールを更新しました');
    }
}