<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'overview',
        'contact_name',
        'department',
        'introduction',
        'icon_path',
    ];

    /**
     * 伝業㝫紝㝥㝝ユーザーアカウント情報を坖徝E     * 使用場面: ログイン誝証ゝE��ーザー惝E��㝮坖得時㝪㝩
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 伝業㝌投稿㝗㝟求人一覧を坖徝E     * 使用場面: 伝業ダポE��ュボ�Eド㝧自社㝮求人一覧を表示㝙る際㝪㝩
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * 伝業㝌逝信㝗㝟スカウト一覧を坖徝E     * 使用場面: スカウト逝信履歴㝮確誝や管睝E��面㝧㝮表示㝪㝩
     */
    public function scouts(): HasMany
    {
        return $this->hasMany(Scout::class);
    }

    /**
     * 伝業㝌坂加㝗㝦㝝E��メポE��ージスレポE��一覧を坖徝E     * 使用場面: メポE��ージ一覧画面㝧伝業㝮スレポE��を表示㝙る際㝪㝩
     */
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }
}