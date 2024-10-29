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
        Schema::table('globaltill_sales_summary_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('gb_diagnostic_report_id')->nullable()->after('report_id');
    
            // Set the foreign key constraint to global_till_diagnostic_reports
            $table->foreign('gb_diagnostic_report_id')
                  ->references('id')
                  ->on('global_till_diagnostic_reports')
                  ->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('globaltill_sales_summary_reports', function (Blueprint $table) {
            $table->dropForeign(['gb_diagnostic_report_id']);
            $table->dropColumn('gb_diagnostic_report_id');
        });
    }
    
};
