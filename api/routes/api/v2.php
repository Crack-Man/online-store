<?php

use App\Http\Controllers\Personal\AuthController;
use App\Http\Controllers\Personal\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('sign-up', [AuthController::class, 'register'])->middleware(['web']);
});

Route::prefix('profile')->group(function () {
    Route::get('me', [ProfileController::class, 'getProfile'])->middleware(['web']);
});