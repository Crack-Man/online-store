<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Property extends Model
{
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'measure',
        'type',
        'is_active',
    ];

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
