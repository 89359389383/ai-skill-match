<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * ユーザー㝌伝業アカウント�E場坈�E伝業惝E��を坖徝E     * 使用場面: ログイン後�Eユーザータイプ判定や伝業惝E��㝮坖得時㝪㝩
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    /**
     * ユーザー㝌フリーランサーアカウント�E場坈�Eフリーランサー惝E��を坖徝E     * 使用場面: ログイン後�Eユーザータイプ判定やフリーランサー惝E��㝮坖得時㝪㝩
     */
    public function freelancer(): HasOne
    {
        return $this->hasOne(Freelancer::class);
    }

    /**
     * ??????????????
     *
     * - freelancer / company ???????????????????????
     * - ??? User ???????????role ????????
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * ??????????????
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * ??????????????
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}