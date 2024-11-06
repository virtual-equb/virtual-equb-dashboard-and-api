<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionCheckAndLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $permission)
    {
        // Make sure we use the session-based 'web' guard
        // $user = Auth::guard('web')->user();

        // if (!$user || !$user->can($permission)) {
        //     // Log the user out if they don't have the permission
        //     Auth::guard('web')->logout();
        //     $request->session()->invalidate();
        //     $request->session()->regenerateToken();

        //     return response()->json([
        //         'message' => 'You do not have permission to access this resource.',
        //         'code' => 403,
        //     ], 403);
        // }
        $user = Auth::guard('web')->user();

        if (!$user || !$user->can($permission)) {
            // Redirect to an unauthorized page if the user doesn't have the required permission
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('unauthorized')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
