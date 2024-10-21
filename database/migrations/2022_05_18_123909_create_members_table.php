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
            // if (!Schema::hasColumn('members', 'city')) {
            //     $table->string('city', 191)->nullable();
            // }
            // if (!Schema::hasColumn('members', 'subcity')) {
            //     $table->string('subcity', 191)->nullable();
            // }
            // if (!Schema::hasColumn('members', 'approved_by')) {
            //     $table->string('approved_by', 191)->nullable();
            // }
            $table->id();
            $table->string('full_name');
            $table->string('phone');
            $table->string('gender');
            // $table->foreignIdFor(Cities::class, 'city');
            // $table->foreignIdFor(Sub_city::class, 'subcity');
            // $table->foreignIdFor(User::class, 'approved_by');
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