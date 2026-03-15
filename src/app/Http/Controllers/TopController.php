<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Freelancer;
use App\Models\Question;
use App\Models\SkillListing;
use Illuminate\Http\Request;

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
        $freelancers = Freelancer::query()
            ->with(['user', 'skills', 'customSkills'])
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
            ->orderByDesc('reviews_count')
            ->limit(6)
            ->get();

        $articles = Article::query()
            ->with(['user', 'tags'])
            ->where('status', 1)
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        return view('top.index', compact('freelancers', 'questions', 'listings', 'articles'));
    }
}

