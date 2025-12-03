<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::as('vendor.')->prefix('vendor')->group(function (): void {
    require __DIR__.'/vendor/passport.php';
});

Route::prefix('v1')->group(function (): void {
    require __DIR__.'/src/auth.php';
    require __DIR__.'/src/password.php';
    require __DIR__.'/src/profile.php';
    require __DIR__.'/src/role.php';
    require __DIR__.'/src/user.php';
});
