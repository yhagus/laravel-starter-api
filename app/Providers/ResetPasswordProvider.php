<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

final class ResetPasswordProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(fn (User $user, string $token): string => config('app.frontend_url').'/reset-password?token='.$token.'&email='.$user->email);
    }
}
