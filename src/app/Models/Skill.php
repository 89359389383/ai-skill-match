<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * 㝓�Eスキルを挝㝤フリーランサー一覧を坖徝E     * 使用場面: スキル検索㝧該当㝙るフリーランサーを探㝙際㝪㝩
     */
    public function freelancers(): BelongsToMany
    {
        return $this->belongsToMany(Freelancer::class, 'freelancer_skill')
            ->using(FreelancerSkill::class)
            ->withTimestamps();
    }

    /**
     * ??????????????????????
     *
     * - ??????????????????????????
     */
    public function skillListings(): BelongsToMany
    {
        return $this->belongsToMany(SkillListing::class, 'skill_listing_skill')->withTimestamps();
    }
}