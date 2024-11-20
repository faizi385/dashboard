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
    Schema::table('retailer_addresses', function (Blueprint $table) {
        // Add 'address' column to the 'retailer_addresses' table
        $table->string('address', 255)->nullable(); // Nullable if you don't want it to be required
    });
}

public function down()
{
    Schema::table('retailer_addresses', function (Blueprint $table) {
        // Drop the 'address' column if the migration is rolled back
        $table->dropColumn('address');
    });
}

};
