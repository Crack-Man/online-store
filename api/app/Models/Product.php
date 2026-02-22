<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'is_active',
        'product_group_id',
        'properties',
        'popularity',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereHas('productGroup', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('brand', function ($query) {
                        $query->where('is_active', true);
                    });
            });
    }
}
