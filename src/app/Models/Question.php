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

    public const STATUS_OPEN = 'open';

    public const STATUS_RESOLVED = 'resolved';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'status',
        'ai_answer',
        'views_count',
        'answers_count',
        'best_answer_id',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'answers_count' => 'integer',
        'best_answer_id' => 'integer',
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
     * ベストアンサー（answers.id）。
     */
    public function bestAnswer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'best_answer_id');
    }

    /**
     * 質問投稿者の表示名を取得（企業は担当者名を優先）。
     */
    public function getAuthorNameAttribute(): string
    {
        $freelancer = $this->user?->freelancer;
        $company = $this->user?->company;

        return $freelancer?->display_name
            ?? $company?->contact_name
            ?? $company?->name
            ?? $this->user?->email
            ?? '匿名';
    }

    /**
     * 質問投稿者のアイコンURLを取得。
     */
    public function getAuthorIconUrlAttribute(): ?string
    {
        $freelancer = $this->user?->freelancer;
        $company = $this->user?->company;
        $iconPath = $freelancer?->icon_path ?? $company?->icon_path;

        return $iconPath ? asset('storage/' . $iconPath) : null;
    }
}

