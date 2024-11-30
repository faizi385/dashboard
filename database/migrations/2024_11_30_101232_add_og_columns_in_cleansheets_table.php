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
        Schema::table('clean_sheets', function (Blueprint $table) {
            $table->enum('offer_sku_matched',['0','1'])->nullable();
            $table->enum('offer_gtin_matched',['0','1'])->nullable();
            $table->int('address_id')->nullable()->index();
            $table->double('product_price')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clean_sheets', function (Blueprint $table) {
            $table->dropColumn('offer_sku_matched');
            $table->dropColumn('offer_gtin_matched');
            $table->dropColumn('product_price');
            $table->dropColumn('address_id');
        });
    }
};
