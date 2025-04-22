<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/registermember',
        'api/payments/telebirr/callback',
        'api/telebirr-miniapp/initialize',
        'api/telebirr-miniapp/callback',
    ];
}