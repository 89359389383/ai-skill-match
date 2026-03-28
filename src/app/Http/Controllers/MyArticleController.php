<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MyArticleController extends Controller
{
    /**
     * 投稿記事一覧（ログイン必須）。
     *
     * “自分の投稿だけ” を出すので、user_id で絞る。
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        $articles = Article::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        if (view()->exists('my-articles.index')) {
            return view('my-articles.index', compact('articles'));
        }

        return view('welcome');
    }

    /**
     * 投稿記事詳細（ログイン必須、投稿者本人のみ）。
     */
    public function show(Request $request, Article $article)
    {
        $this->ensureOwner($request, $article);

        $article->load(['tags']);

        if (view()->exists('my-articles.show')) {
            return view('my-articles.show', compact('article'));
        }

        return view('welcome');
    }

    /**
     * 投稿記事編集画面（ログイン必須、投稿者本人のみ）。
     */
    public function edit(Request $request, Article $article)
    {
        $this->ensureOwner($request, $article);

        $article->load(['tags']);

        if (view()->exists('my-articles.edit')) {
            return view('my-articles.edit', compact('article'));
        }

        return view('welcome');
    }

    /**
     * 投稿記事更新（ログイン必須、投稿者本人のみ）。
     */
    public function update(UpdateArticleRequest $request, Article $article, ArticleService $service)
    {
        $this->ensureOwner($request, $article);

        // 更新の入力チェックは FormRequest 側へ移動。
        $validated = $request->validated();

        if (! array_key_exists('structure', $validated)) {
            $validated['structure'] = $article->structure;
        }

        // 既存アイキャッチ画像を「×」で削除した場合
        // - 新しいファイルが選ばれていないときのみ DB の URL を null にする
        if ((string) $request->input('eyecatch_image_remove') === '1' && ! $request->hasFile('eyecatch_image')) {
            $validated['eyecatch_image_url'] = null;
        }

        if ($request->hasFile('eyecatch_image')) {
            try {
                $path = $request->file('eyecatch_image')->store('eyecatches', 'public');
                $validated['eyecatch_image_url'] = Storage::disk('public')->url($path);
            } catch (\Throwable $e) {
                Log::warning('[MyArticleController@update] eyecatch upload failed: '.$e->getMessage());
            }
        }

        $service->update($article, $validated);

        return redirect()->route('my-articles.index')->with('status', '記事を更新しました。');
    }

    /**
     * 記事削除（投稿者本人のみ）。
     */
    public function destroy(Request $request, Article $article, ArticleService $service)
    {
        $this->ensureOwner($request, $article);
        $service->delete($article);

        return redirect()->route('my-articles.index')->with('status', '記事を削除しました。');
    }

    /**
     * “投稿者本人しか編集できない” を守るための共通チェック。
     *
     * やっていること:
     * - request()->user() の id と、記事の user_id を比較
     * - 一致しなければ 403（権限なし）
     *
     * なぜ Controller でやる？
     * - 仕様上の「アクセス制御（本人のみ）」に直結するため
     * - View を返す前に必ず弾きたい
     */
    private function ensureOwner(Request $request, Article $article): void
    {
        $user = $request->user();
        // このコントローラーは routes 側で auth.any を付けているため、基本的に null にはならない。
        // それでも念のため、万一外れていたら 401（未認証）として扱う。
        if (!$user) {
            abort(401, 'ログインが必要です。');
        }

        if ((int) $article->user_id !== (int) $user->id) {
            abort(403, 'この記事を操作する権限がありません。');
        }
    }
}

