<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Constants\HttpStatus;
use App\Models\User;
use App\Traits\HandlesUserSessionCache;
use Dedoc\Scramble\Attributes\Group;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Laravel\Passport\AccessToken;

#[Group('Authentication')]
final class AuthenticatedSessionController extends Controller
{
    use HandlesUserSessionCache;

    private const int AUTHORIZED_USER_CACHE_TTL_SECONDS = 300;

    /**
     * Authorize token
     */
    public function authorize(Request $request): JsonResponse
    {
        $userIdentifierFromCookie = $request->cookie($this->userIdentifierCookieName());

        if (is_string($userIdentifierFromCookie) && $userIdentifierFromCookie !== '') {
            /** @var array<string, mixed>|User|null $cachedAuthorizedUser */
            $cachedAuthorizedUser = Cache::get($this->buildAuthorizedUserCacheKey($userIdentifierFromCookie));

            if ($cachedAuthorizedUser !== null) {
                return $this->respondWithAuthorizedUser($cachedAuthorizedUser, $userIdentifierFromCookie);
            }
        }

        $authenticatedUser = Auth::user();

        abort_if($authenticatedUser === null, HttpStatus::HTTP_UNAUTHORIZED, 'Unauthenticated.');

        $email = (string) $authenticatedUser->email;

        abort_if($email === '', HttpStatus::HTTP_UNAUTHORIZED, 'Unauthenticated.');

        $cacheKey = $this->buildAuthorizedUserCacheKey($email);
        /** @var User|null $cachedUser */
        $cachedUser = Cache::get($cacheKey);

        if ($cachedUser !== null) {
            return $this->respondWithAuthorizedUser($cachedUser, $email);
        }

        Cache::put($cacheKey, $authenticatedUser, now()->addSeconds(self::AUTHORIZED_USER_CACHE_TTL_SECONDS));

        return $this->respondWithAuthorizedUser($authenticatedUser, $email);
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

        abort_if($currentToken === null, HttpStatus::HTTP_UNAUTHORIZED, 'Unauthenticated.');

        try {
            $currentToken->revoke();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            abort(HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
        }
        $userEmail = $request->user()?->email;
        if (is_string($userEmail) && $userEmail !== '') {
            $this->forgetUserSession($userEmail);
        }

        Cookie::queue(Cookie::forget($this->userIdentifierCookieName()));

        return Response::json([
            'message' => 'Logout success',
        ]);
    }

    /**
     * Refresh
     */
    public function refresh(Request $request): JsonResponse
    {
        $userEmail = $request->cookie($this->userIdentifierCookieName());

        abort_if(! is_string($userEmail) || $userEmail === '', HttpStatus::HTTP_UNAUTHORIZED, 'Unauthorized.');

        $refreshToken = $request->bearerToken();

        abort_if(! is_string($refreshToken) || $refreshToken === '', HttpStatus::HTTP_UNAUTHORIZED, 'Unauthorized.');

        $cachedSession = $this->getCachedUserSession($userEmail);

        if ($cachedSession !== null) {
            $cachedRefreshToken = $cachedSession['refresh_token'] ?? null;

            abort_if(
                ! is_string($cachedRefreshToken)
                || $cachedRefreshToken === ''
                || ! hash_equals($cachedRefreshToken, $refreshToken),
                HttpStatus::HTTP_UNAUTHORIZED,
                'Unauthorized.'
            );

            return $this->respondWithUserSessionPayload($cachedSession, $userEmail);
        }

        $baseUrl = (string) config('passport.base_url');
        $clientId = (string) config('passport.password_client_id');
        $clientSecret = (string) config('passport.password_client_secret');

        $url = $baseUrl.'/vendor/passport/token';

        try {
            $response = Http::asForm()->post($url, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);
        } catch (ConnectionException $exception) {
            $exceptionMessage = 'Failed to refresh token:'.$exception->getMessage();
            Log::error($exceptionMessage);
            abort(HttpStatus::HTTP_INTERNAL_SERVER_ERROR, $exceptionMessage);
        }

        if ($response->failed()) {
            /** @var array<string, mixed> $responseBody */
            $responseBody = json_decode($response->body(), true);
            Log::error($response->getReasonPhrase(), $responseBody);
            abort(HttpStatus::HTTP_UNAUTHORIZED, 'Unauthorized.');
        }

        /** @var array<string, mixed>|null $jsonResponse */
        $jsonResponse = $response->json();

        if ($jsonResponse !== null) {
            $this->cacheUserSession($userEmail, $jsonResponse);
        }

        return $this->respondWithUserSessionPayload($jsonResponse ?? [], $userEmail);
    }

    private function buildAuthorizedUserCacheKey(string $email): string
    {
        return 'user:authorized:'.$email;
    }

    /**
     * @param  array<string, mixed>|User  $userPayload
     */
    private function respondWithAuthorizedUser(array|User $userPayload, string $email): JsonResponse
    {
        return Response::json($userPayload)
            ->withCookie($this->buildUserIdentifierCookie($email));
    }
}
