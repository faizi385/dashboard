<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToCovaSalesReportsTable extends Migration
{
    public function up()
    {
        Schema::table('cova_sales_reports', function (Blueprint $table) {
            $table->date('date')->nullable(); // Add the date column with default
        });
    }

    public function down()
    {
        Schema::table('cova_sales_reports', function (Blueprint $table) {
            $table->dropColumn('date'); // Drop the date column if rolling back
        });
    }
}
