<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permission::create(['name' => 'manage_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'create', 'guard_name' => 'api']);
    }
}
