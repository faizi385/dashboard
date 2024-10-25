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
        Schema::table('tendy_sales_summary_reports', function (Blueprint $table) {
            // Add report_id column
            $table->unsignedBigInteger('report_id')->nullable();

            // Add foreign key constraint
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tendy_sales_summary_reports', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['report_id']);
            // Then drop the column
            $table->dropColumn('report_id');
        });
    }
};
