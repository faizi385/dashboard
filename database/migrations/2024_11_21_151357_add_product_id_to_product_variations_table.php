<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_variations', function (Blueprint $table) {
            // Adding the product_id column as an unsigned integer (foreign key)
            $table->unsignedBigInteger('product_id')->nullable()->after('id');

          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_variations', function (Blueprint $table) {
            // Drop the product_id column and foreign key
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
}
