<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRetailerAndLpColumnsToTechPosReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tech_pos_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('retailer_id')->nullable(); // Add retailer_id column
            $table->unsignedBigInteger('lp_id')->nullable();       // Add lp_id column

            // Add foreign key constraints
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('cascade'); // Referencing lps table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tech_pos_reports', function (Blueprint $table) {
            $table->dropForeign(['retailer_id']);
            $table->dropForeign(['lp_id']);
            $table->dropColumn('retailer_id');
            $table->dropColumn('lp_id');
        });
    }
}
