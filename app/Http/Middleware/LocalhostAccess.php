<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class LocalhostAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //        $allowedIps = ['127.0.0.1', '::1'];
        //
        //        if (! in_array($request->ip(), $allowedIps)) {
        //            abort(404);
        //        }

        return $next($request);
    }
}
