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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('equb_id');
            $table->string('payment_type');
            $table->double('amount');
            $table->double('creadit');
            $table->double('balance');
            $table->string('collecter');
            $table->enum('status', ['paid','unpaid','pending'])->default('paid');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('equb_id')->references('id')->on('equbs');
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
        Schema::dropIfExists('payments');
    }
};
