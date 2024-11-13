<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure 'Demo user' role exists
        $roleWeb = Role::firstOrCreate(['name' => 'Demo user', 'guard_name' => 'web']);
        $roleApi = Role::firstOrCreate(['name' => 'Demo user', 'guard_name' => 'api']);
        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            $user->assignRole($roleWeb);
            $user->assignRole($roleApi);
        }
    }
}
