<?php

declare(strict_types=1);

use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->prefix('profile')->group(function (): void {
    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'update']);
});
