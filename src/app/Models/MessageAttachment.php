<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'attachment_name',
        'attachment_path',
        'attachment_mime',
        'attachment_size',
    ];

    protected $casts = [
        'attachment_size' => 'integer',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}

