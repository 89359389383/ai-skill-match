<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillListing extends Model
{
    use HasFactory;

    /**
     * ここに書いたカラムだけが一括代入（create/update）できる。
     * 「想定外の値が勝手に保存される事故」を防ぐために、意図的に絞る。
     */
    protected $fillable = [
        'freelancer_id',
        'title',
        'description',
        'price',
        'pricing_type',
        'thumbnail_url',
        'status',
        'delivery_days',
        'reviews_count',
        'rating_average',
    ];

    protected $casts = [
        'price' => 'integer',
        'status' => 'integer',
        'delivery_days' => 'integer',
        'reviews_count' => 'integer',
        'rating_average' => 'decimal:1',
    ];

    /**
     * 出品者（Freelancer）を取得する。
     *
     * 例:
     * - 一覧に「出品者名」を出したい
     * - 詳細で「出品者プロフィール」を表示したい
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }

    /**
     * 出品スキルに紐づく「共通スキル」一覧。
     *
     * なぜ中間テーブルが必要？
     * - 出品1件に対して複数スキルを付けたい
     * - スキル1件に対して複数出品がぶら下がる
     * → 多対多なので pivot（skill_listing_skill）で表現する
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'skill_listing_skill')
            ->withTimestamps();
    }

    /**
     * 出品の添付（画像/ファイル）一覧。
     *
     * - サムネイルとは別に、詳細で複数の画像を見せたい
     * - 「参考資料（pdfなど）」を添付したい
     */
    public function assets(): HasMany
    {
        return $this->hasMany(SkillListingAsset::class)->orderBy('sort_order');
    }

    /**
     * 購入（注文）一覧。
     *
     * - 購入履歴、売上集計、レビュー可否判定などに使う想定
     */
    public function orders(): HasMany
    {
        return $this->hasMany(SkillOrder::class);
    }

    /**
     * レビュー一覧。
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(SkillReview::class);
    }
}

