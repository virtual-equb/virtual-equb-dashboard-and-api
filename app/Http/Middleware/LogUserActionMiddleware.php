<?php

namespace App\Http\Middleware;

use App\Models\UserActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogUserActionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // only log authenticated users
        if (Auth::check()) {
            Log::channel('usage')->info('User action', [
                'user_id' => Auth::id(),
                'action' => $request->method() . ' ' . $request->path(),
                'page' => $request->path(),
                'details' => $request->all(),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);
            UserActivity::create([
                'user_id' => Auth::id(),
                'page' => $request->path(),
                'action' => $request->method()
            ]);
        }
        return $next($request);
    }
}
