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
        Schema::create('equb_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // $table->dateTime('start_date');
            // $table->dateTime('end_date');
            $table->integer('round');
            $table->enum('status', ['Active', 'Deactive',])->default('Active');
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
        Schema::dropIfExists('equb_types');
    }
};
