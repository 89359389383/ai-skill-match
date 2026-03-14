<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_listing_id',
        'buyer_user_id',
        'amount',
        'status',
        'purchased_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'purchased_at' => 'datetime',
    ];

    /**
     * 購入対象の出品（スキル）を取得する。
     */
    public function skillListing(): BelongsTo
    {
        return $this->belongsTo(SkillListing::class);
    }

    /**
     * 購入者（users）を取得する。
     *
     * 注意:
     * - buyer_user_id は users.id を指すため、belongsTo(User::class, 'buyer_user_id') を指定する。
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }
}

