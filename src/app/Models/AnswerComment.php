<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnswerComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_id',
        'user_id',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * どの回答へのコメントか
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }

    /**
     * 誰がコメントしたか
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * コメント投稿者の表示名を取得
     */
    public function getAuthorNameAttribute(): string
    {
        $freelancer = $this->user?->freelancer;
        $company = $this->user?->company;
        return $freelancer?->display_name ?? $company?->contact_name ?? $company?->name ?? $this->user?->email ?? '匿名';
    }

    /**
     * コメント投稿者のアイコンURLを取得
     */
    public function getAuthorIconUrlAttribute(): ?string
    {
        $freelancer = $this->user?->freelancer;
        $company = $this->user?->company;
        $iconPath = $freelancer?->icon_path ?? $company?->icon_path;
        return $iconPath ? asset('storage/' . $iconPath) : null;
    }
}
