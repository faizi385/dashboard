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
        Schema::table('offers', function (Blueprint $table) {
            $table->string('lp_name')->after('lp_id')->nullable(); // Add lp_name column
            $table->unsignedBigInteger('province_id')->nullable()->after('gtin'); // Add province_id column
        });
    }
    
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('lp_name'); // Drop lp_name column if the migration is rolled back
            $table->dropColumn('province_id'); // Drop province_id column if needed
        });
    }
    
};
