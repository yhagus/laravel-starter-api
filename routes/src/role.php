<?php

declare(strict_types=1);

use App\Http\Controllers\Role\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->prefix('role')->group(function (): void {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{role}', [RoleController::class, 'show']);
    Route::put('/{role}', [RoleController::class, 'update']);
    Route::delete('/{role}', [RoleController::class, 'destroy']);
});
