<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function productGroups(): HasMany
    {
        return $this->hasMany(ProductGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
