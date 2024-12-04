<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToOtherPosReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('other_pos_reports', function (Blueprint $table) {
            $table->date('date')->nullable(); // Adds the 'date' column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('other_pos_reports', function (Blueprint $table) {
            $table->dropColumn('date'); // Drops the 'date' column if the migration is rolled back
        });
    }
}
