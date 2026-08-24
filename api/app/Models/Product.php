<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasSlug;
    
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

    public function scopeActive($query): Builder
    {
        return $query->where('is_active', true)
            ->where('price', '>', 0)
            ->whereHas('productGroup', function ($query) {
                $query->active();
            });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
