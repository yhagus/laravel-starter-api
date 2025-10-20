<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

#[Group('Authentication')]
final class AuthenticatedSessionController extends Controller
{
    /**
     * Authorize token
     */
    public function authorize(Request $request): JsonResponse
    {
        $authenticatedUser = $request->user();

        return response()->json($authenticatedUser);

        if ($authenticatedUser === null) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        /** @var User $user */
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
    }

    /**
     * Logout
     */
    public function logout(): JsonResponse
    {
        //
    }

    /**
     * Refresh token session
     */
    public function refresh(Request $request): JsonResponse
    {
        /** @var string $baseUrl */
        $baseUrl = config('passport.base_url');
        /** @var string $clientId */
        $clientId = config('passport.password_client_id');
        /** @var string $clientSecret */
        $clientSecret = config('passport.password_client_secret');

        $response = Http::asForm()->post($baseUrl.'/api/vendor/passport/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->bearerToken(),
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => '*',
        ]);

        if ($response->failed()) {
            return response()->json($response->body(), $response->status());
        }

        /** @var array<string, mixed>|null $jsonResponse */
        $jsonResponse = $response->json();

        return response()->json($jsonResponse);
    }
}
