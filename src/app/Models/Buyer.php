<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'icon_path',
        'age_group',
        'prefecture',
        'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // buyerが参加しているダイレクト会話（sender/receiver両方を含む）
    public function directConversations(): HasMany
    {
        return $this->hasMany(DirectConversation::class, 'buyer_id');
    }
}

