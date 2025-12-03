<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::as('passport.')->prefix('passport')->group(function (): void {
    Route::post('/token', [AccessTokenController::class, 'issueToken'])
        ->name('token')
        ->middleware('throttle');
});
