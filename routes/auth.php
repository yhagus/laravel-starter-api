<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function (): void {

    Route::get('/authorize', function (Request $request): JsonResponse {
        $authenticatedUser = $request->user();

        if ($authenticatedUser === null) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        /** @var App\Models\User $user */
        $user = $authenticatedUser->select(['id', 'email'])->first();

        if (! $user->exists()) {
            return response()->json(['error' => 'User not found'], 404);
        }

        /** @var string $userId */
        $userId = $user->getAuthIdentifier();
        $key = sprintf('auth:user:%s', $userId);
        $ttlSeconds = 60;

        /** @var array<string, mixed>|null $cached */
        $cached = Cache::get($key);

        if ($cached !== null) {
            return response()->json($cached);
        }

        $payload = $user->toArray();
        Cache::put($key, $payload, now()->addSeconds($ttlSeconds));

        return response()->json($payload);
    });
});

Route::middleware(['guest'])->group(function (): void {
    Route::post('/login', function (Request $request): JsonResponse {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        /** @var string $baseUrl */
        $baseUrl = config('services.passport.base_url');
        /** @var string $clientId */
        $clientId = config('services.passport.password_client_id');
        /** @var string $clientSecret */
        $clientSecret = config('services.passport.password_client_secret');

        /** @var string $email */
        $email = $request->input('email');
        /** @var string $password */
        $password = $request->input('password');

        $response = Http::asForm()->post($baseUrl.'/api/vendor/passport/token', [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ]);

        /** @var array<string, mixed>|null $jsonResponse */
        $jsonResponse = $response->json();

        return response()->json($jsonResponse ?? []);
    });
});
