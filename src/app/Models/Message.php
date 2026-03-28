<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'thread_id',
        'sender_type',
        'sender_id',
        'body',
        'sent_at',
    ];

    protected $casts = [
        'sender_id' => 'integer',
        'sent_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * 㝓�EメポE��ージ㝌属㝙るスレポE��惝E��を坖徝E     * 使用場面: メポE��ージ一覧表示時㝫スレポE��惝E��を坂照㝙る際㝪㝩
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * メポE��ージ逝信耝E��伝業㝮場坈�E伝業惝E��を坖徝E     * 使用場面: メポE��ージ表示時㝫逝信耝E��ゝE��イコンを表示㝙る際㝪㝩
     */
    public function senderCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'sender_id');
    }

    /**
     * メポE��ージ逝信耝E��フリーランサー㝮場坈�Eフリーランサー惝E��を坖徝E     * 使用場面: メポE��ージ表示時㝫逝信耝E��ゝE��イコンを表示㝙る際㝪㝩
     */
    public function senderFreelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class, 'sender_id');
    }

    /**
     * thread messages?messages?????????????
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class, 'message_id')
            ->orderBy('id');
    }
}