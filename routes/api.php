<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::as('vendor.')->prefix('vendor')->group(function (): void {
    require __DIR__.'/vendor/passport.php';
});

Route::as('auth.')->prefix('auth')->group(function (): void {
    require __DIR__.'/auth.php';
});
