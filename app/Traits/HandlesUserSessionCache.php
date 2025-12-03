<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

trait HandlesUserSessionCache
{
    private const int USER_SESSION_CACHE_BUFFER_SECONDS = 120;

    private const string USER_IDENTIFIER_COOKIE = 'username';

    /**
     * @return array<string, mixed>|null
     */
    protected function getCachedUserSession(string $identifier): ?array
    {
        /** @var array<string, mixed>|null $session */
        $session = Cache::get($this->buildUserSessionCacheKey($identifier));

        return $session;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function cacheUserSession(string $identifier, array $payload): void
    {
        $expiresIn = (int) ($payload['expires_in'] ?? 0);
        $cacheTtlInSeconds = $this->resolveUserSessionCacheTtl($expiresIn);

        if ($cacheTtlInSeconds <= 0) {
            return;
        }

        Cache::put(
            $this->buildUserSessionCacheKey($identifier),
            $payload,
            now()->addSeconds($cacheTtlInSeconds)
        );
    }

    protected function forgetUserSession(string $identifier): void
    {
        Cache::forget($this->buildUserSessionCacheKey($identifier));
    }

    protected function buildUserSessionCacheKey(string $identifier): string
    {
        return 'user:session:'.$identifier;
    }

    protected function resolveUserSessionCacheTtl(float|int $expiresIn): int
    {
        return max($expiresIn - self::USER_SESSION_CACHE_BUFFER_SECONDS, 0);
    }

    protected function respondWithUserSessionPayload(array $payload, string $identifier): JsonResponse
    {
        return Response::json($payload)
            ->withCookie($this->buildUserIdentifierCookie($identifier));
    }

    protected function buildUserIdentifierCookie(string $identifier): SymfonyCookie
    {
        $lifetimeInMinutes = (int) config('session.lifetime', 120);

        return cookie(
            $this->userIdentifierCookieName(),
            $identifier,
            $lifetimeInMinutes,
            config('session.path', '/'),
            config('session.domain'),
            (bool) config('session.secure', false),
            true,
            false,
            config('session.same_site')
        );
    }

    protected function userIdentifierCookieName(): string
    {
        return self::USER_IDENTIFIER_COOKIE;
    }
}
