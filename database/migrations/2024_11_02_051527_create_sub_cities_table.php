<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_cities', function (Blueprint $table) {
            $table->id(); // Unique identifier
            $table->unsignedBigInteger('city_id'); // Foreign key for cities, without constraint
            $table->unsignedBigInteger('created_by'); // Foreign key for users, without constraint
            $table->boolean('active')->default(true); // Status of the sub-city
            $table->string('remark')->nullable(); // Description of the sub-city
            $table->string('name')->unique(); // Name of the sub-city, ensuring uniqueness
            $table->timestamps(); // Created at and updated at fields

  
        //    $table->foreign('city_id')->references('id')->on('cities');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_cities');
    }
}