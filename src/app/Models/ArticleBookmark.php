<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleBookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
    ];

    /**
     * どの記事のブックマークか。
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 誰がブックマークしたか。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

