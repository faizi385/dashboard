<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRetailerAndLpColumnsToProfittechPosReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profittech_pos_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('retailer_id')->nullable()->after('id'); // Add retailer_id column
            $table->unsignedBigInteger('lp_id')->nullable()->after('retailer_id'); // Add lp_id column

            // Add foreign key constraints
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profittech_pos_reports', function (Blueprint $table) {
            $table->dropForeign(['retailer_id']); // Drop foreign key for retailer_id
            $table->dropForeign(['lp_id']);      // Drop foreign key for lp_id
            $table->dropColumn('retailer_id');    // Remove retailer_id column
            $table->dropColumn('lp_id');          // Remove lp_id column
        });
    }
}
