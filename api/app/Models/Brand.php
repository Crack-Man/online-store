<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Brand extends Model
{
    use HasSlug;
    
    protected $fillable = [
        'name',
        'slug',
    ];

    public function productGroups(): HasMany
    {
        return $this->hasMany(ProductGroup::class);
    }

    public function scopeActive($query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
