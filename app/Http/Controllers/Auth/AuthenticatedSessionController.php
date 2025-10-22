<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use ArrayAccess;
use Dedoc\Scramble\Attributes\Group;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\AccessToken;
use Laravel\Passport\Contracts\ScopeAuthorizable;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Log;
use Symfony\Component\HttpFoundation\Response;

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
    public function logout(Request $request): JsonResponse
    {
        /**
         * @var AccessToken<mixed>|null $currentToken
         */
        $currentToken = $request->user()?->token();

        if ($currentToken === null) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
        }

        try {
            $currentToken->revoke();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'message' => 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Logout success',
        ]);
    }

    /**
     * Refresh
     */
    public function refresh(Request $request): JsonResponse
    {
        /** @var string $baseUrl */
        $baseUrl = config('passport.base_url');
        /** @var string $clientId */
        $clientId = config('passport.password_client_id');
        /** @var string $clientSecret */
        $clientSecret = config('passport.password_client_secret');

        $url = $baseUrl.'/api/vendor/passport/token';
        $response = Http::asForm()->post($url, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->bearerToken(),
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => '*',
        ]);

        if ($response->failed()) {
            Log::error($response->getReasonPhrase());
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized.');
        }

        /** @var array<string, mixed>|null $jsonResponse */
        $jsonResponse = $response->json();

        return response()->json($jsonResponse);
    }
}
