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
        // Add 'address' column to 'addresses' table
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('address', 255)->nullable(); // Add the address column
        });
    }
    
    public function down()
    {
        // Remove 'address' column if the migration is rolled back
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
    
};
