<?php

use App\Models\Cities;
use App\Models\Sub_city;
use App\Models\User;
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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone');
            $table->string('gender');
            $table->foreignIdFor(Cities::class, 'city');
            $table->foreignIdFor(Sub_city::class, 'subcity');
            $table->foreignIdFor(User::class, 'approved_by');
            $table->string('specific_location');
            $table->date('approved_date');
            $table->integer('rating');
            $table->string('woreda');
            $table->string('house_number');
            $table->string('email');
            $table->string('profile_photo_path');
            $table->enum('status', ['Active', 'Pending', 'Deactive',])->default('Active');
            $table->date('date_of_birth');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
};