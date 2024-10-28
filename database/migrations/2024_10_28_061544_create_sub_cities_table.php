<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User; // Import User model
use App\Models\City; // Import City model

class CreateSubCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('sub_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'created_by')->constrained('users')->onDelete('cascade'); // Reference to users table
            $table->foreignIdFor(City::class, 'city_id')->constrained('cities')->onDelete('cascade'); // Reference to cities table
            $table->string('name', 255);
            $table->string('active')->default('false'); // Store as string
            $table->longText('remark')->nullable(); // Make remark nullable
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_cities');
    }
}