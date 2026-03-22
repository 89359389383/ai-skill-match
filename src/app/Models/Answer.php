<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'user_id',
        'content',
        'is_accepted',
        'reactions_naruhodo',
        'reactions_soudane',
        'reactions_arigatou',
        'likes_count',
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
        'reactions_naruhodo' => 'integer',
        'reactions_soudane' => 'integer',
        'reactions_arigatou' => 'integer',
        'likes_count' => 'integer',
    ];

    /**
     * どの質問への回答か。
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * 誰が回答したか（users）。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この回答に対するコメント一覧
     */
    public function comments(): HasMany
    {
        return $this->hasMany(AnswerComment::class)->orderBy('created_at', 'asc');
    }

    /**
     * 回答投稿者の表示名を取得
     */
    public function getAuthorNameAttribute(): string
    {
        $freelancer = $this->user?->freelancer;
        $company = $this->user?->company;
        return $freelancer?->display_name ?? $company?->contact_name ?? $company?->name ?? $this->user?->email ?? '匿名';
    }

    /**
     * 回答投稿者のアイコンURLを取得
     */
    public function getAuthorIconUrlAttribute(): ?string
    {
        $freelancer = $this->user?->freelancer;
        $company = $this->user?->company;
        $iconPath = $freelancer?->icon_path ?? $company?->icon_path;
        return $iconPath ? asset('storage/' . $iconPath) : null;
    }
}

