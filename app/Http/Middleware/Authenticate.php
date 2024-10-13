<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
 protected function redirectTo($request)
    {
        // Give detailed stacktrace error info if APP_DEBUG is true in the .env
        if ($request->is('api/*')) {
            return route('api-fallback');
        }
      return route('login');
    }
}
