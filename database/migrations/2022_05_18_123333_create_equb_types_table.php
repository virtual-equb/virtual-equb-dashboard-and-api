<?php

use App\Models\MainEqub;
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
            $table->foreignIdFor(MainEqub::class, 'main_equb');
            $table->string('image');
            $table->integer('round');
            $table->enum('status', ['Active', 'Deactive',])->default('Active');
            $table->boolean('active')->default(false);
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
