<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Freelancer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'job_title',
        'bio',
        'min_hours_per_week',
        'max_hours_per_week',
        'hours_per_day',
        'days_per_week',
        'work_style_text',
        'work_availability_status',
        'services_offered',
        'industry_specialties',
        'prefecture',
        'min_rate',
        'max_rate',
        'experience_companies',
        'certifications',
        'icon_path',
        'phone',
        'line_id',
        'twitter_url',
    ];

    protected $casts = [
        'min_hours_per_week' => 'integer',
        'max_hours_per_week' => 'integer',
        'hours_per_day' => 'integer',
        'days_per_week' => 'integer',
        'min_rate' => 'integer',
        'max_rate' => 'integer',
    ];

    /**
     * フリーランサー㝫紝㝥㝝ユーザーアカウント情報を坖徝E     * 使用場面: ログイン誝証ゝE��ーザー惝E��㝮坖得時㝪㝩
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * フリーランサー㝌応募㝗㝟求人一覧を坖徝E     * 使用場面: マイペ�Eジ㝧応募履歴を表示㝙る際㝪㝩
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * フリーランサー㝌块㝑坖㝣㝟スカウト一覧を坖徝E     * 使用場面: スカウト块信箱㝧块信㝗㝟スカウトを表示㝙る際㝪㝩
     */
    public function scouts(): HasMany
    {
        return $this->hasMany(Scout::class);
    }

    /**
     * フリーランサー㝌坂加㝗㝦㝝E��メポE��ージスレポE��一覧を坖徝E     * 使用場面: メポE��ージ一覧画面㝧フリーランサー㝮スレポE��を表示㝙る際㝪㝩
     */
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * フリーランサー㝌挝㝤スキル�E��Eスタスキル�E�一覧を坖徝E     * 使用場面: プロフィール表示ゝE��キル検索時�Eマッポング㝪㝩
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'freelancer_skill')
            ->using(FreelancerSkill::class)
            ->withTimestamps();
    }

    /**
     * フリーランサー㝌独自㝫登録㝗㝟カスタムスキル一覧を坖徝E     * 使用場面: プロフィール編雝E��面ゝE��示画面㝧カスタムスキルを表示㝙る際㝪㝩
     */
    public function customSkills(): HasMany
    {
        return $this->hasMany(FreelancerCustomSkill::class)->orderBy('sort_order');
    }

    /**
     * フリーランサー㝮ポ�EトフォリオURL一覧を坖徝E     * 使用場面: プロフィール表示画面㝧ポ�Eトフォリオリンクを表示㝙る際㝪㝩
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(FreelancerPortfolio::class)->orderBy('sort_order');
    }

    /**
     * ???????????????????
     *
     * ?????:
     * - ?????????
     * - ????????????????????
     */
    public function skillListings(): HasMany
    {
        return $this->hasMany(SkillListing::class);
    }
}