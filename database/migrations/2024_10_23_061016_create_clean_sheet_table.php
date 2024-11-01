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
            $table->string('size_in_gram'); 
            $table->string('location');
            $table->unsignedBigInteger('province_id');
            $table->string('province_slug');
            $table->string('sku');
            $table->string('product_name');
            $table->string('category');
            $table->string('brand');
            $table->integer('sold');
            $table->integer('purchase');
            $table->double('average_price'); 
            $table->double('average_cost');  
            $table->double('report_price_og'); 
            $table->string('barcode')->nullable();
            $table->integer('transfer_in')->nullable();
            $table->integer('transfer_out')->nullable();
            $table->string('pos'); 
            $table->date('reconciliation_date');
            $table->integer('dqi_flag')->default(0); 
            $table->integer('flag')->default(0); 
            $table->text('comment')->nullable();
            $table->integer('opening_inventory_unit');
            $table->integer('closing_inventory_unit');
            $table->string('c_flag')->default(''); 
            $table->double('dqi_fee')->nullable(); 
            $table->double('dqi_per')->nullable(); 
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
