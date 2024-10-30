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
        Schema::create('c_b_e_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('enc_val');
            $table->string('transaction_id')->nullable();
            $table->string('state')->nullable();
            $table->string('tnd_date')->nullable();
            $table->string('signature')->nullable();
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
        Schema::dropIfExists('c_b_e_transactions');
    }
};
