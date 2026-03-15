<?php

namespace App\Services\Catalog;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function filterProducts(array $filters, ?string $brandSlug = null)
    {
        $query = Product::query();

        if ($brandSlug) {
            $query->whereHas('productGroup.brand', function ($query) use ($brandSlug) {
                $query->where('slug', $brandSlug);
            });
        }

        return $query;
    }
}