<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuyerRegisterRequest;
use App\Http\Requests\BuyerProfileUpdateRequest;
use App\Models\User;
use App\Services\BuyerProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerProfileController extends Controller
{
    /**
     * 購入者（buyer）プロフィール登録画面（初回）
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'buyer') {
            abort(403);
        }

        if ($user->buyer()->exists()) {
            // 既に登録済みならスキル一覧へ
            return redirect('/skills');
        }

        return view('buyer.profile.create', [
            'user' => $user,
        ]);
    }

    /**
     * 購入者（buyer）プロフィール登録（2段階目）
     */
    public function store(BuyerRegisterRequest $request, BuyerProfileService $buyerProfileService)
    {
        $user = Auth::user();

        if ($user->role !== 'buyer') {
            abort(403);
        }

        if ($user->buyer()->exists()) {
            return redirect('/skills');
        }

        $validated = $request->validated();

        // icon file を service に渡す
        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon');
        }

        $buyerProfileService->register($user, $validated);

        return redirect('/skills')->with('success', '購入者プロフィールの登録が完了しました');
    }

    /**
     * 購入者（buyer）プロフィール設定画面
     */
    public function edit()
    {
        $user = Auth::user();

        if ($user->role !== 'buyer') {
            abort(403);
        }

        if (!$user->buyer()->exists()) {
            return redirect('/buyer/profile')->with('error', '先に購入者プロフィールを登録してください');
        }

        return view('buyer.profile.settings', [
            'user' => $user,
            'buyer' => $user->buyer,
        ]);
    }

    /**
     * 購入者（buyer）プロフィール設定更新
     */
    public function update(BuyerProfileUpdateRequest $request, BuyerProfileService $buyerProfileService)
    {
        $user = Auth::user();

        if ($user->role !== 'buyer') {
            abort(403);
        }

        if (!$user->buyer()->exists()) {
            return redirect('/buyer/profile')->with('error', '先に購入者プロフィールを登録してください');
        }

        $validated = $request->validated();

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon');
        }

        $buyerProfileService->update($user->buyer, $validated);

        return redirect()->route('buyer.profile.settings')->with('success', '購入者プロフィールを更新しました');
    }
}

