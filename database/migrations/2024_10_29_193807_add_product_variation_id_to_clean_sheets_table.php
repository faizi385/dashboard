<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductVariationIdToCleanSheetsTable extends Migration
{
    public function up()
    {
        Schema::table('clean_sheets', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variation_id')->nullable()->after('pos_report_id'); // Add product_variation_id column
        });
    }

    public function down()
    {
        Schema::table('clean_sheets', function (Blueprint $table) {
            $table->dropColumn('product_variation_id'); // Drop product_variation_id column if needed
        });
    }
}
