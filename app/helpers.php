<?php
use Illuminate\Support\Facades\Auth;
if (!function_exists('get_label')) {
    function get_label($key, $default) {
        // Example logic for getting labels
        return session()->get('labels.' . $key, $default);
    }
}
if (!function_exists('getAuthenticatedUser')) {

    function getAuthenticatedUser()
    {
        // Check the 'web' guard (users)
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }

        // Check the 'clients' guard (clients)
        if (Auth::guard('api')->check()) {
            return Auth::guard('api')->user();
        }

        // No user is authenticated
        return null;
    }
}