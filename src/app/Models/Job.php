<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;
    public const STATUS_STOPPED = 2;

    protected $fillable = [
        'company_id',
        'title',
        'subtitle',
        'description',
        'desired_persona',
        'required_skills_text',
        'reward_type',
        'min_rate',
        'max_rate',
        'work_time_text',
        'work_start_date',
        'publish_end_date',
        'status',
    ];

    protected $casts = [
        'min_rate' => 'integer',
        'max_rate' => 'integer',
        'work_start_date' => 'date',
        'publish_end_date' => 'date',
        'status' => 'integer',
    ];

    /**
     * 㝓�E求人を投稿㝗㝟伝業惝E��を坖徝E     * 使用場面: 求人詳細画面㝧伝業惝E��を表示㝙る際㝪㝩
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * 㝓�E求人㝸㝮応募一覧を坖徝E     * 使用場面: 伝業㝌応募耝E��覧を確誝㝙る際㝪㝩
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * 㝓�E求人㝫関連㝙るスカウト一覧を坖徝E     * 使用場面: 求人㝫関連㝙るスカウト逝信履歴を確誝㝙る際㝪㝩
     */
    public function scouts(): HasMany
    {
        return $this->hasMany(Scout::class);
    }

    /**
     * 㝓�E求人㝫関連㝙るメポE��ージスレポE��一覧を坖徝E     * 使用場面: 求人㝫関連㝙るメポE��ージ履歴を表示㝙る際㝪㝩
     */
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }
}