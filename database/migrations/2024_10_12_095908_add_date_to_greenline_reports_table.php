<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToGreenlineReportsTable extends Migration
{
    public function up()
    {
        Schema::table('greenline_reports', function (Blueprint $table) {
            $table->date('date')->nullable(); 
        });
    }

    public function down()
    {
        Schema::table('greenline_reports', function (Blueprint $table) {
            $table->dropColumn('date'); // Drop the date column if the migration is rolled back
        });
    }
}
