<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCleanSheetTable extends Migration
{
    public function up()
    {
        Schema::create('clean_sheets', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('retailer_id');
            $table->unsignedBigInteger('lp_id');
            $table->unsignedBigInteger('report_id');
            $table->string('retailer_name');
            $table->string('lp_name');
            $table->string('thc_range');
            $table->string('cbd_range');
            $table->double('size_in_gram'); // Double without precision/scale
            $table->string('location');
            $table->unsignedBigInteger('province_id'); // Province ID as integer instead of string
            $table->string('province_slug');
            $table->string('sku');
            $table->string('product_name');
            $table->string('category');
            $table->string('brand');
            $table->integer('sold');
            $table->integer('purchase');
            $table->double('average_price'); // Double without precision/scale
            $table->double('average_cost');  // Double without precision/scale
            $table->double('report_price_og'); // Double without precision/scale
            $table->string('barcode')->nullable();
            $table->integer('transfer_in')->nullable();
            $table->integer('transfer_out')->nullable();
            $table->string('pos'); // Point of Sale system
            $table->date('reconciliation_date');
            $table->integer('dqi_flag')->default(0); // Integer for dqi_flag
            $table->integer('flag')->default(0); // Integer for flag
            $table->text('comment')->nullable();
            $table->integer('opening_inventory_unit');
            $table->integer('closing_inventory_unit');
            $table->integer('c_flag')->default(0); // Integer for c_flag
            $table->double('dqi_fee')->nullable(); // Double without precision/scale
            $table->double('dqi_per')->nullable(); // Double without precision/scale
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->unsignedBigInteger('pos_report_id')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clean_sheets');
    }
}
