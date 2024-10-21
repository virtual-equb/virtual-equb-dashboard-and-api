<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('phone_number')->unique();
            $table->string('gender');
            $table->string('role');
            $table->boolean('enabled')->default(true);
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        DB::table('users')->insert(
            array(
                'id' => '1',
                'name' => 'Hirpha Fayisa',
                // 'email' => 'tamiratekub@virtualequb.com',
                // 'password' => Hash::make('tame@ekub'),
                // 'phone_number' => '+251910898626',
                'email' => 'hirphafayisa88@gmail.com',
                'password' => Hash::make('hirpha@1'),
                'phone_number' => '+251930605974',
                'gender' => 'male',
                'role' => 'admin',
                'enabled' => '1',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
