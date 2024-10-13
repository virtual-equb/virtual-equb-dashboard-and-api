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
        Schema::table('equb_types', function (Blueprint $table) {
            $table->string('remark')->nullable();
            $table->enum('rote', ['Daily', 'Weekly', 'Biweekly', 'Monthly']);
            $table->enum('type', ['Automatic', 'Manual']);
            $table->string('lottery_date')->nullable();
            $table->text('terms')->nullable();
            $table->string('quota')->nullable();
            $table->string('remaining_quota')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equb_types', function (Blueprint $table) {
            //
        });
    }
};
