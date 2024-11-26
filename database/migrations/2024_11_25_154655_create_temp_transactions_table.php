<?php

use App\Models\Equb;
use App\Models\Member;
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
        Schema::create('temp_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignIdFor(Member::class, 'member_id');
            $table->foreignIdFor(Equb::class, 'equb_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_type')->nullable();
            $table->decimal('balance', 10, 2)->nullable();
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
        Schema::dropIfExists('temp_transactions');
    }
};
