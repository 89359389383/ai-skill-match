<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillOrder extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    public const PAYMENT_TYPE_ESCROW = 'escrow';
    public const PAYMENT_TYPE_INSTANT = 'instant';

    public const TX_WAITING_PAYMENT = 'waiting_payment';
    public const TX_IN_PROGRESS = 'in_progress';
    public const TX_DELIVERED = 'delivered';
    public const TX_COMPLETED = 'completed';

    public const PAYOUT_NOT_TRANSFERRED = 'not_transferred';
    public const PAYOUT_TRANSFERRED = 'transferred';
    public const PAYOUT_FAILED = 'failed';

    protected $fillable = [
        'skill_listing_id',
        'buyer_user_id',
        'amount',
        'status',
        'payment_type',
        'transaction_status',
        'payout_status',
        'purchased_at',
        'paid_at',
        'delivered_at',
        'completed_at',
        'checkout_cancelled_at',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_webhook_event_id',
        'last_webhook_type',
        'last_webhook_received_at',
        'stripe_transfer_id',
        'transferred_at',
        'transfer_attempts',
        'last_transfer_error',
    ];

    protected $casts = [
        'amount' => 'integer',
        'transfer_attempts' => 'integer',
        'purchased_at' => 'datetime',
        'paid_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'checkout_cancelled_at' => 'datetime',
        'last_webhook_received_at' => 'datetime',
        'transferred_at' => 'datetime',
    ];

    public function skillListing(): BelongsTo
    {
        return $this->belongsTo(SkillListing::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SkillOrderMessage::class)->orderBy('sent_at');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isEscrow(): bool
    {
        return $this->payment_type === self::PAYMENT_TYPE_ESCROW;
    }

    public function canDeliver(): bool
    {
        return $this->isPaid() && $this->transaction_status === self::TX_IN_PROGRESS;
    }

    public function canCompleteEscrow(): bool
    {
        return $this->isPaid()
            && $this->isEscrow()
            && $this->transaction_status === self::TX_DELIVERED;
    }

    public function alreadyTransferred(): bool
    {
        return !empty($this->stripe_transfer_id)
            || $this->payout_status === self::PAYOUT_TRANSFERRED;
    }
}
