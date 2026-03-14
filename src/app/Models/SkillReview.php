<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_listing_id',
        'user_id',
        'rating',
        'body',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * レビュー対象の出品（スキル）。
     */
    public function skillListing(): BelongsTo
    {
        return $this->belongsTo(SkillListing::class);
    }

    /**
     * レビュー投稿者（users）。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

