<?php

namespace Database\Seeders;

use App\Models\Member;
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
        
        // Seed 100 Demo users
        for ($i = 0; $i < 100; $i++) {
                // Create a new user with the factory
                $user = User::factory()->create();
                
                // Assign roles to the user
                $user->assignRole($roleWeb);
                $user->assignRole($roleApi);

                // Create a new corresponding Member record
                $member = Member::create([
                    'full_name' => $user->name,
                    'phone' => $user->phone_number,
                    'email' => $user->email,
                    // 'address' => $this->faker->address
                ]);
            }
        }
}
