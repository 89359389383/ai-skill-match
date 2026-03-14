<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * このタグが付いている記事一覧。
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag')->withTimestamps();
    }

    /**
     * このタグが付いている質問一覧。
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_tag')->withTimestamps();
    }
}

