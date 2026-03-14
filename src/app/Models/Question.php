<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'is_resolved',
        'ai_answer',
        'views_count',
        'answers_count',
        'accepted_answer_id',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'views_count' => 'integer',
        'answers_count' => 'integer',
        'accepted_answer_id' => 'integer',
    ];

    /**
     * 投稿者（users）。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 質問に付いているタグ一覧。
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'question_tag')->withTimestamps();
    }

    /**
     * 回答一覧。
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)->latest();
    }

    /**
     * 採用された回答（ベストアンサー）。
     *
     * 注意:
     * - accepted_answer_id は answers.id を指す
     * - 制約（外部キー）は現時点では貼っていない（将来の拡張ポイント）
     */
    public function acceptedAnswer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'accepted_answer_id');
    }
}

