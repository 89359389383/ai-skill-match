<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'excerpt',
        'category',
        'eyecatch_image_url',
        'body_html',
        'structure',
        'status',
        'published_at',
        'views_count',
        'likes_count',
    ];

    protected $casts = [
        'structure' => 'array',
        'status' => 'integer',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'likes_count' => 'integer',
    ];

    /**
     * 投稿者（users）。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 記事に付いているタグ一覧。
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')->withTimestamps();
    }

    /**
     * いいね（レコード）一覧。
     *
     * 補足:
     * - likes_count は「集計カラム」
     * - こちらは「実データ（誰がいいねしたか）」を辿るための関係
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ArticleLike::class);
    }

    /**
     * ブックマーク（レコード）一覧。
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(ArticleBookmark::class);
    }

    /**
     * Quill 編集画面の初期 HTML（body_html が無い旧データは structure から組み立て）。
     */
    public function editorInitialHtml(): string
    {
        if (filled($this->body_html)) {
            return $this->body_html;
        }

        $chunks = [];
        foreach ($this->structure ?? [] as $section) {
            if (! is_array($section)) {
                continue;
            }
            $chunks[] = '<h2>'.e($section['title'] ?? '').'</h2>';
            foreach ($section['subsections'] ?? [] as $sub) {
                if (! is_array($sub)) {
                    continue;
                }
                $chunks[] = '<h3>'.e($sub['title'] ?? '').'</h3>';
                $content = $sub['content'] ?? '';
                $chunks[] = '<p>'.e($content).'</p>';
            }
        }

        return implode('', $chunks);
    }
}

