<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToGlobalTillDiagnosticReportsTable extends Migration
{
    public function up()
    {
        Schema::table('global_till_diagnostic_reports', function (Blueprint $table) {
            $table->date('date')->default(now()->startOfMonth()); // Add the date column with default
        });
    }

    public function down()
    {
        Schema::table('global_till_diagnostic_reports', function (Blueprint $table) {
            $table->dropColumn('date'); // Drop the date column if rolling back
        });
    }
}