<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function (): void {

    Route::get('/authorize', fn (Request $request) => $request->user());
    Route::post('/logout', function (Request $request) {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Logged out successfully']);
    });
});

Route::middleware(['guest'])->group(function (): void {
    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $response = Http::asForm()->post(config('services.passport.base_url').'/api/vendor/passport/token', [
            'grant_type' => 'password',
            'client_id' => config('services.passport.password_client_id'),
            'client_secret' => config('services.passport.password_client_secret'),
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '*',
        ]);

        return $response->json();
    });
});
