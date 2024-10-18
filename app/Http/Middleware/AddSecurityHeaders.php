<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddSecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request)->header('X-Frame-Options', 'SAMEORIGIN')->header('X-XSS-Protection', '1; mode=block')->header('Referrer-Policy', 'strict-origin-when-cross-origin')->header('Expect-CT', 'max-age=3600, enforce');
    }
}
