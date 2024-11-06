<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Dawit',
            'email' => 'dawitgotechh@gmail.com',
            'password' => Hash::make('password'),
            'phone_number' => '+2519013145679',
            'gender' => 'male',
            // 'role' => 'admin',
            'enabled' => '1'
        ]);
    }
}
