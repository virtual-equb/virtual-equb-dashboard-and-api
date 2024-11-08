<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Clear cache when a role is created
        Role::created(function ($role) {
            cache()->forget('spatie.permission.cache');
        });

        Role::updated(function ($role) {
            cache()->forget('spatie.permission.cache');
        });

        Role::deleted(function ($role) {
            cache()->forget('spatie.permission.cache');
        });

        // Clear cache when a permission is created
        Permission::created(function ($permission) {
            cache()->forget('spatie.permission.cache');
        });

        Permission::updated(function ($permission) {
            cache()->forget('spatie.permission.cache');
        });

        Permission::deleted(function ($permission) {
            cache()->forget('spatie.permission.cache');
        });
    }

    public function register()
    {
        //
    }
}