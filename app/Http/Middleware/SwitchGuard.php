<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see https://dannyherran.com/2022/01/laravel-sanctum-optional-route-authentication-guard/
 */
class SwitchGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $guard = null): Response
    {
        if (in_array($guard, array_keys(config('auth.guards')))) {
            config(['auth.defaults.guard' => $guard]);
        }

        return $next($request);
    }
}
