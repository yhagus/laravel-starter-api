<?php

declare(strict_types=1);

use App\Http\Controllers\Fortify\ConfirmablePasswordController;
use App\Http\Controllers\Fortify\ConfirmedPasswordStatusController;
use App\Http\Controllers\Fortify\ConfirmedTwoFactorAuthenticationController;
use App\Http\Controllers\Fortify\EmailVerificationNotificationController;
use App\Http\Controllers\Fortify\NewPasswordController;
use App\Http\Controllers\Fortify\PasswordController;
use App\Http\Controllers\Fortify\PasswordResetLinkController;
use App\Http\Controllers\Fortify\ProfileInformationController;
use App\Http\Controllers\Fortify\RecoveryCodeController;
use App\Http\Controllers\Fortify\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\Fortify\TwoFactorAuthenticationController;
use App\Http\Controllers\Fortify\TwoFactorQrCodeController;
use App\Http\Controllers\Fortify\TwoFactorSecretKeyController;
use App\Http\Controllers\Fortify\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/** @var array<int, string> $middleware */
$middleware = config('fortify.middleware');

Route::middleware($middleware)->prefix('fortify')->group(function (): void {
    /** @var string|null $twoFactorLimiter */
    $twoFactorLimiter = config('fortify.limiters.two-factor');
    /** @var string $verificationLimiter */
    $verificationLimiter = config('fortify.limiters.verification', '6,1');
    /** @var string $guard */
    $guard = config('fortify.guard');
    /** @var string $authMiddleware */
    $authMiddleware = config('fortify.auth_middleware', 'auth');

    $guestGuard = 'guest:'.$guard;
    $authGuard = $authMiddleware.':'.$guard;

    Route::middleware([$guestGuard])->group(function (): void {
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
    });

    // Email Verification...
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware([$authGuard, 'signed', 'throttle:'.$verificationLimiter])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware([$authGuard, 'throttle:'.$verificationLimiter])
        ->name('verification.send');

    Route::middleware([$authGuard])->group(function (): void {
        Route::as('user-profile-information.')->group(function (): void {
            Route::get('/user/profile-information', [ProfileInformationController::class, 'show'])->name('show');
            Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])->name('update');
        });
        // Passwords...
        Route::put('/user/password', [PasswordController::class, 'update'])->name('user-password.update');
        Route::as('password.')->group(function (): void {
            // Password Confirmation...
            Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])->name('confirmation');
            Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])->name('confirm.store');
        });
    });

    // Two Factor Authentication...
    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            $guestGuard,
            $twoFactorLimiter !== null ? 'throttle:'.$twoFactorLimiter : null,
        ]))->name('two-factor.login.store');

    /** @var array<int, string> $twoFactorMiddleware */
    $twoFactorMiddleware = [$authGuard, 'password.confirm'];

    Route::middleware($twoFactorMiddleware)->group(function (): void {
        Route::as('two-factor.')->group(function (): void {
            Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])->name('enable');
            Route::post('/user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])->name('confirm');
            Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])->name('disable');
            Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])->name('qr-code');
            Route::get('/user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])->name('secret-key');
            Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])->name('recovery-codes');
        });

        Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store']);
    });
});
