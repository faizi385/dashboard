<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ideal_sales_summary_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('ideal_diagnostic_report_id')->nullable()->after('report_id');
        });
    }

    public function down()
    {
        Schema::table('ideal_sales_summary_reports', function (Blueprint $table) {
            $table->dropColumn('ideal_diagnostic_report_id');
        });
    }
};
