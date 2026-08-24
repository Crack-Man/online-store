<?php

namespace App\Models;

use App\Support\Enums\PropertyType;
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
        'unit_id',
        'type',
        'is_active',
    ];

    protected $casts = [
        'type' => PropertyType::class,
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
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
