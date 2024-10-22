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
        $adminRole = Roles::create(['name' => 'admin']);
        $editorRole = Roles::create(['name' => 'editor']);
        $memberRole = Roles::create(['name' => 'member']);
        $generalManagerRole = Roles::create(['name' => 'general_manager']);
        $operationManagerRole = Roles::create(['name' => 'operation_manager']);
        $ItRole = Roles::create(['name' => 'it']);
        $customerService = Roles::create(['name' => 'customer_service']);
        $equbCollecterRole = Roles::create(['name' => 'equb_collector']);
        $assistant = Roles::create(['name' => 'assistant']);
        $callCenterRole = Roles::create(['name' => 'call_center']);
        $financeRole = Roles::create(['name' => 'finance']);

        // Create Permission
        $permissions = ['edit articles', 'delete articles', 'publish articles', 'read articles'];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign Permissions to Roles
        $adminRole->permissions()->sync(Permission::pluck('id'));
        $editorRole->permissions()->sync(Permission::where('name', 'edit articles')->pluck('id'));

    }
}
