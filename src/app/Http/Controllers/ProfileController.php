<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * プロフィール一覧（ログイン不要）。
     *
     * このアプリの「プロフィール一覧」は、現状はフリーランスを中心に表示する想定。
     * （企業プロフィール一覧が必要になったら別途追加する）
     */
    public function index(Request $request)
    {
        $query = Freelancer::query()
            ->with(['user', 'skills', 'customSkills']);

        // スキル名で検索
        if ($request->filled('skill')) {
            $skillName = $request->input('skill');
            $query->where(function ($q) use ($skillName) {
                $q->whereHas('skills', function ($sq) use ($skillName) {
                    $sq->where('name', 'like', '%' . $skillName . '%');
                })->orWhereHas('customSkills', function ($sq) use ($skillName) {
                    $sq->where('name', 'like', '%' . $skillName . '%');
                });
            });
        }

        $freelancers = $query
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        if (view()->exists('profiles.index')) {
            return view('profiles.index', compact('freelancers'));
        }

        return view('welcome');
    }

    /**
     * プロフィール詳細（ログイン不要）。
     *
     * ルートは `/profiles/{user}` なので、User から辿って freelancer 情報を表示する。
     */
    public function show(Request $request, User $user)
    {
        // freelancer が無い場合（企業ユーザー等）は 404 扱いにする（仕様が固まったら調整）
        $freelancer = $user->freelancer()
            ->with(['skills', 'customSkills', 'portfolios', 'skillListings' => fn($q) => $q->where('status', 1)])
            ->first();

        if (!$freelancer) {
            abort(404, 'プロフィールが見つかりません。');
        }

        $articles = $user->articles()
            ->where('status', 1)
            ->orderByDesc('published_at')
            ->take(10)
            ->get();

        if (view()->exists('profiles.show')) {
            return view('profiles.show', compact('user', 'freelancer', 'articles'));
        }

        return view('welcome');
    }
}

