<?php

use App\Models\CountryCode;
use App\Models\User;
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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(CountryCode::class, 'code');
            $table->string('active')->default(false);
            $table->longText('remark');
            $table->foreignIdFor(User::class, 'created_by');
            $table->enum('status', ['Approved', 'Deactive',])->default('Approved');
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
        Schema::dropIfExists('countries');
    }
};
