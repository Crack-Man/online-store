<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
