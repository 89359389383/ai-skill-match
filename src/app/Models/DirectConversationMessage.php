<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DirectConversationMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'direct_conversation_id',
        'sender_type',
        'sender_id',
        'body',
        'sent_at',
    ];

    protected $casts = [
        'direct_conversation_id' => 'integer',
        'sender_id' => 'integer',
        'sent_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(DirectConversation::class, 'direct_conversation_id');
    }

    public function senderCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'sender_id');
    }

    public function senderFreelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class, 'sender_id');
    }
}
