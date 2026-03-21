<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * 記事一覧（ログイン不要）。
     *
     * やること:
     * - 公開記事を新着順で取得
     * - 将来: 検索/ソート/ページングを仕様に合わせて追加
     */
    public function index(Request $request)
    {
        $articles = Article::query()
            ->with(['user', 'tags', 'user.freelancer', 'user.company'])
            ->where('status', 1)
            ->when($request->filled('user'), function ($q) use ($request) {
                $q->where('user_id', (int) $request->query('user'));
            })
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        if (view()->exists('articles.index')) {
            return view('articles.index', compact('articles'));
        }

        return view('welcome');
    }

    /**
     * 記事詳細（ログイン不要）。
     */
    public function show(Article $article)
    {
        $article->load(['user', 'tags', 'user.freelancer', 'user.company']);

        // 将来: views_count を increment する（bot対策/重複対策を入れてから）

        if (view()->exists('articles.show')) {
            return view('articles.show', compact('article'));
        }

        return view('welcome');
    }

    /**
     * 記事投稿画面（ログイン必須）。
     *
     * 注意:
     * - 認証は routes 側で auth.any を付ける
     * - ここでは「フォーム表示」に徹する（保存は store）
     */
    public function create()
    {
        if (view()->exists('articles.create')) {
            return view('articles.create');
        }

        return view('welcome');
    }

    /**
     * 記事投稿（ログイン必須）。
     *
     * やること:
     * - 入力を validate
     * - Service に渡して保存（タグも含む）
     * - 完了後、一覧へ戻す（仕様が固まったら詳細へ）
     */
    public function store(StoreArticleRequest $request, ArticleService $service)
    {
        // auth.any ミドルウェアで request()->user() が取れる前提
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // 仕様（記事投稿画面）に沿った入力チェックは FormRequest 側へ移動。
        // Controller では “validated 済みの安全な配列” だけを受け取る。
        $validated = $request->validated();

        // アイキャッチ画像がアップロードされていれば保存して URL を validated にセットする
        if ($request->hasFile('eyecatch_image')) {
            try {
                $file = $request->file('eyecatch_image');
                $path = $file->store('eyecatches', 'public');
                $validated['eyecatch_image_url'] = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
            } catch (\Throwable $e) {
                // 保存に失敗しても投稿処理自体は継続（バリデーションで弾きたい場合は別途対応）
                \Illuminate\Support\Facades\Log::warning('[ArticleController@store] eyecatch upload failed: ' . $e->getMessage());
            }
        }

        $article = $service->store($user, $validated);

        return redirect()->route('articles.index')->with('status', '記事を投稿しました。');
    }
}

