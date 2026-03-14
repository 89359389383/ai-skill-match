<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
    ];

    /**
     * どの記事へのいいねか。
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 誰がいいねしたか。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

