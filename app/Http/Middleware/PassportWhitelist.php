<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PassportWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $whitelist = config('passport.whitelist');

        if ($whitelist !== null) {
            $allowedIps = explode(',', $whitelist);
            abort_unless(in_array($request->ip(), $allowedIps), 404);
        }

        return $next($request);
    }
}
