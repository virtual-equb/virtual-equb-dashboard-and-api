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
        Schema::create('equb_takers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('equb_id');
            $table->string('payment_type');
            $table->double('amount');
            $table->double('remaining_amount');
            $table->enum('status', ['paid','unpaid','pending','partially_paid','void'])->default('unpaid');
            $table->string('paid_by');
            $table->double('total_payment');
            $table->double('remaining_payment');
            $table->double('cheque_amount')->nullable();
            $table->string('cheque_bank_name')->nullable();
            $table->longText('cheque_description')->nullable();
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
        Schema::dropIfExists('equb_takers');
    }
};
