<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

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
        'trial_ends_at' => 'datetime',
    ];

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function freelancer(): HasOne
    {
        return $this->hasOne(Freelancer::class);
    }

    public function buyer(): HasOne
    {
        return $this->hasOne(Buyer::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function answerComments(): HasMany
    {
        return $this->hasMany(AnswerComment::class);
    }

    public function skillOrders(): HasMany
    {
        return $this->hasMany(SkillOrder::class, 'buyer_user_id');
    }

    public function skillOrderMessages(): HasMany
    {
        return $this->hasMany(SkillOrderMessage::class, 'sender_user_id');
    }
}
