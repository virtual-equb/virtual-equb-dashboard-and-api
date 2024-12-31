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
        Schema::create('callback_data', function (Blueprint $table) {
            $table->id();
            $table->string('paidAmount');
            $table->string('paidByNumber');
            $table->string('transactionId');
            $table->string('transactionTime');
            $table->string('tillCode');
            $table->string('token');
            $table->string('signature');
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
        Schema::dropIfExists('callback_data');
    }
};
