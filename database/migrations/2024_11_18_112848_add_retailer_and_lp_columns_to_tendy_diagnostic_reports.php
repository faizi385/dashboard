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
        Schema::table('tendy_diagnostic_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('retailer_id')->after('id')->nullable();
            $table->unsignedBigInteger('lp_id')->after('retailer_id')->nullable();

            // If foreign keys are required, uncomment these lines:
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
        Schema::table('tendy_diagnostic_reports', function (Blueprint $table) {
            $table->dropColumn(['retailer_id', 'lp_id']);
        });
    }
};
