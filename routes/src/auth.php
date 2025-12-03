<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware(['auth:api'])->group(function (): void {
        Route::get('/authorize', [AuthenticatedSessionController::class, 'authorize']);
        Route::post('/logout', [AuthenticatedSessionController::class, 'logout']);
    });

    Route::post('/refresh', [AuthenticatedSessionController::class, 'refresh']);
    Route::post('/login', [LoginController::class, 'login']);
});
