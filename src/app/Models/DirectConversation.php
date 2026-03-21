<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DirectConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'freelancer_id',
        'initiator_type',
        'initiator_id',
        'latest_sender_type',
        'latest_sender_id',
        'latest_message_at',
        'is_unread_for_company',
        'is_unread_for_freelancer',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'freelancer_id' => 'integer',
        'initiator_id' => 'integer',
        'latest_sender_id' => 'integer',
        'latest_message_at' => 'datetime',
        'is_unread_for_company' => 'boolean',
        'is_unread_for_freelancer' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DirectConversationMessage::class)->orderBy('sent_at');
    }
}
