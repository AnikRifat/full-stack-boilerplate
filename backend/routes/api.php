<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Models\ApplicationSetting;
use App\Http\Controllers\Api\V1\MediaController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

Route::prefix('v1')->name('api.v1.')->middleware('throttle:api')->group(function (): void {
    Route::get('/health', fn () => response()->json(['data' => ['status' => 'ok']]));
    Route::get('/configuration', fn () => response()->json(['data' => ApplicationSetting::values()]));
    Route::middleware('throttle:auth')->group(function (): void {
        Route::post('/auth/register', [AuthController::class, 'register'])->name('register');
        Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
    });
    Route::middleware(['auth:sanctum', 'active'])->group(function (): void {
        Route::apiResource('media', MediaController::class)->only(['index', 'store', 'show', 'destroy']);
        Route::post('/media/{media}/replace', [MediaController::class, 'replace']);
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [ProfileController::class, 'show'])->middleware(CheckAbilities::class.':profile:read')->name('me');
        Route::patch('/me', [ProfileController::class, 'update'])->middleware(CheckAbilities::class.':profile:write')->name('me.update');
    });
});
