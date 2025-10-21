<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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
        $authenticatedUser = Auth::user();
        /** @var ?string $authenticatedUserId */
        $authenticatedUserId = $authenticatedUser?->id;

        $cachedUser = Cache::remember(
            'auth.'.$authenticatedUserId,
            ttl: 3600, // Cache for 1 hour (in seconds)
            callback: fn () => $authenticatedUser
        );

        return response()->json($cachedUser);
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
