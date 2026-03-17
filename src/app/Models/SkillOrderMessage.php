<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillOrderMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_order_id',
        'sender_user_id',
        'message_type',
        'body',
        'file_name',
        'file_path',
        'sent_at',
    ];

    protected $casts = [
        'skill_order_id' => 'integer',
        'sender_user_id' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function skillOrder(): BelongsTo
    {
        return $this->belongsTo(SkillOrder::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}

