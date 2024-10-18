<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('members', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('subcity')->nullable();
            $table->string('woreda')->nullable();
            $table->string('house_number')->nullable();
            $table->string('specific_location')->nullable();
            // $table->json('address')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->boolean('verified')->default(false);
            $table->string('approved_by')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            //
        });
    }
};
