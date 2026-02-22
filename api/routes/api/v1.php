<?php

use App\Http\Controllers\Catalog\BrandController;
use Illuminate\Support\Facades\Route;

Route::prefix('catalog')->group(function () {
    Route::get('brands', [BrandController::class, 'index']);
});