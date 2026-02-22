<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductGroup extends Model
{
    protected $fillable = [
        'name',
        'brand_id',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereHas('brand', function ($query) {
                $query->where('is_active', true);
            });
    }
}
