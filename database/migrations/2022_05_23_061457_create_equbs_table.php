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
        Schema::create('equbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('equb_type_id');
            $table->double('amount');
            $table->double('total_amount');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('lottery_date');
            $table->integer('total_members')->change();
            $table->enum('status', ['Active', 'Deactive',])->default('Active');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('equb_type_id')->references('id')->on('equb_types');
            $table->enum('notified', ['Yes', 'No'])->default('No');
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
        Schema::dropIfExists('equb_registrations');
    }
};
