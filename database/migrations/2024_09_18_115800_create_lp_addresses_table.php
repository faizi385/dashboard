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
        Schema::create('lp_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lp_id')->constrained('lps')->onDelete('cascade');
            $table->string('street_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('postal_code')->nullable();
            $table->foreignId('province_id')->constrained('provinces'); // Assuming you have a 'provinces' table
            $table->string('city')->nullable();
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
        Schema::dropIfExists('lp_addresses');
    }
};
