<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillListingAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_listing_id',
        'type',
        'url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * 「この添付はどの出品に属するか」を辿るための関係。
     */
    public function skillListing(): BelongsTo
    {
        return $this->belongsTo(SkillListing::class);
    }
}

