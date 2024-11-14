<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpIdToRetailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->unsignedBigInteger('lp_id')->nullable()->after('id'); // Adjust 'after' as needed
            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropForeign(['lp_id']);
            $table->dropColumn('lp_id');
        });
    }
}
