<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ArticleService
{
    /**
     * 記事を新規作成する。
     *
     * 入力の考え方:
     * - controller 側では「画面から来た値を validate したもの」を受け取る
     * - service 側では「保存に必要な形」へまとめて、複数テーブル更新を transaction で守る
     */
    public function store(User $user, array $data): Article
    {
        return DB::transaction(function () use ($user, $data): Article {
            $article = Article::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'excerpt' => $data['excerpt'],
                'category' => $data['category'],
                'eyecatch_image_url' => $data['eyecatch_image_url'] ?? null,
                'body_html' => $this->sanitizeBodyHtml($data['body_html'] ?? null),
                // 旧フォーマット互換のため残す（Quill 本文は body_html を優先して表示）
                'structure' => $data['structure'] ?? null,
                // まずは公開（仕様が固まったら下書き導線を追加）
                'status' => 1,
                'published_at' => Carbon::now(),
            ]);

            // タグは「存在すれば使う、なければ作る」方式にしておくと入力が楽
            $tagNames = $data['tags'] ?? [];
            $tagIds = $this->resolveTagIds($tagNames);
            if (count($tagIds) > 0) {
                $article->tags()->sync($tagIds);
            }

            return $article;
        });
    }

    /**
     * 記事を更新する（投稿者本人チェックは Controller 側で行う想定）。
     */
    public function update(Article $article, array $data): Article
    {
        return DB::transaction(function () use ($article, $data): Article {
            $article->fill([
                'title' => $data['title'],
                'excerpt' => $data['excerpt'],
                'category' => $data['category'],
                'body_html' => $this->sanitizeBodyHtml($data['body_html'] ?? null),
                'structure' => array_key_exists('structure', $data) ? $data['structure'] : $article->structure,
            ]);

            // フォームから eyecatch_image_url を送らない更新（ファイル未選択）では既存 URL を維持する
            if (array_key_exists('eyecatch_image_url', $data)) {
                $article->eyecatch_image_url = $data['eyecatch_image_url'];
            }

            $article->save();

            $tagNames = $data['tags'] ?? [];
            $tagIds = $this->resolveTagIds($tagNames);
            $article->tags()->sync($tagIds);

            return $article;
        });
    }

    /**
     * 記事を削除する（タグの中間テーブルのみ明示的に外す。他はDBの制約に任せる）。
     */
    public function delete(Article $article): void
    {
        DB::transaction(function () use ($article): void {
            $article->tags()->detach();
            $article->delete();
        });
    }

    /**
     * タグ名の配列 -> tags.id の配列へ変換する。
     *
     * 例:
     * - ["ChatGPT", "業務効率化"] が来たら
     * - tags に無ければ作り
     * - [1, 5] のような ID 配列にして返す
     */
    private function resolveTagIds(array $tagNames): array
    {
        $tagIds = [];

        foreach ($tagNames as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            $tag = Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        // 重複除去（UI側の入力ミスでも安全に）
        return array_values(array_unique($tagIds));
    }

    /**
     * 保存前の簡易サニタイズ（スクリプト系・イベントハンドラを除去）。
     */
    private function sanitizeBodyHtml(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = trim($html);
        if ($html === '') {
            return null;
        }

        $html = preg_replace('#<(script|iframe|object|embed|form|input|button|meta|link|style)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $html = preg_replace('#<(script|iframe|object|embed|form|input|button|meta|link|style)\b[^>]*/>#is', '', $html) ?? $html;
        $html = preg_replace('#\s(on[a-z]+\s*=\s*("|\').*?\2)#i', '', $html) ?? $html;
        $html = str_ireplace('javascript:', '', $html);

        return $html;
    }
}

