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
        Schema::table('cova_sales_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('cova_diagnostic_report_id')->nullable()->after('report_id');
            
            // Optional: Add a foreign key constraint if you have a `cova_diagnostic_reports` table
            $table->foreign('cova_diagnostic_report_id')
                  ->references('id')->on('cova_diagnostic_reports')
                  ->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('cova_sales_reports', function (Blueprint $table) {
            $table->dropForeign(['cova_diagnostic_report_id']);
            $table->dropColumn('cova_diagnostic_report_id');
        });
    }
    
};
