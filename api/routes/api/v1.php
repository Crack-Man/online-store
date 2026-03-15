<?php

use App\Http\Controllers\Catalog\BrandController;
use App\Http\Controllers\Catalog\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('catalog')->group(function () {
    Route::get('brands', [BrandController::class, 'index']);

    Route::get('{slug}', [ProductController::class, 'index']);

    Route::get('{slug}/filters', [ProductController::class, 'getFilters']);
});