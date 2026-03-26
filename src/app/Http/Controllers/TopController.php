<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Freelancer;
use App\Models\Question;
use App\Models\SkillListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TopController extends Controller
{
    /**
     * トップページ表示。
     *
     * 新着フリーランス、最新質問、人気スキル、注目記事を各セクションに渡す。
     * 記事・質問はまだない場合は空のコレクションで渡す。
     */
    public function index(Request $request)
    {
        Log::info('[TopController::index] トップページ表示 開始');

        $freelancers = Freelancer::query()
            ->with([
                'user',
                'skills',
                'customSkills',
                // 新着フリーランスの一覧では、プロフィール一覧と同じロジックで
                // ☆評価（weighted average）と件数を表示するために掲載中のスキルだけを取得
                'skillListings' => fn ($q) => $q
                    ->where('status', 1)
                    ->select('id', 'freelancer_id', 'rating_average', 'reviews_count', 'status'),
            ])
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $questions = Question::query()
            ->with(['user', 'tags'])
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $listings = SkillListing::query()
            ->with(['freelancer.user', 'skills'])
            ->where('status', 1)
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        Log::info('[TopController::index] トップページ用スキル一覧取得完了', [
            'listings_count' => $listings->count(),
            'query' => 'status=1, orderByDesc(id), limit(6)',
        ]);

        foreach ($listings as $i => $l) {
            Log::info("[TopController::index] トップ表示スキル #{$l->id}", [
                'index' => $i + 1,
                'id' => $l->id,
                'title' => $l->title,
                'status' => $l->status,
                'freelancer_id' => $l->freelancer_id,
                'price' => $l->price,
                'created_at' => $l->created_at?->toIso8601String(),
            ]);
        }

        Log::info('[TopController::index] トップページ表示 終了');

        $articles = Article::query()
            ->with(['user', 'tags'])
            ->where('status', 1)
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        return view('top.index', compact('freelancers', 'questions', 'listings', 'articles'));
    }
}

