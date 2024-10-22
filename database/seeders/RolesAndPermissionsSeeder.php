<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Roles

        $roles = ['admin', 'editor', 'member', 'general_manager', 'operation_manager', 'it', 'customer_service', 'equb_collector', 'assistant', 'call_center', 'finance'];
        foreach ($roles as $role) {
            Roles::create(['name' => $role]);
        }

        // Create Permission
        $permissions = ['manage_users', 'manage_roles', 'view_dashboard', 'create_equb', 'manage_equb'];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign Permissions to Roles
        $admin = Roles::where('name', 'admin')->first();
        $admin->permissions()->sync(Permission::pluck('id'));

    }
}
