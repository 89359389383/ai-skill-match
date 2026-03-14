<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}

