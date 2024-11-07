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
            $table->unsignedBigInteger('retailer_id')->index(); // Added index
            $table->unsignedBigInteger('lp_id')->index(); // Added index
            $table->unsignedBigInteger('report_id')->index(); // Added index
            $table->string('retailer_name');
            $table->string('lp_name');
            $table->string('thc_range')->nullable(); // Make nullable
            $table->string('cbd_range')->nullable(); // Make nullable
            $table->string('size_in_gram')->nullable(); // Make nullable
            $table->string('location')->nullable(); // Make nullable
            $table->unsignedBigInteger('province_id')->index(); // Added index
            $table->string('province_slug')->nullable(); // Make nullable
            $table->string('sku')->index(); // Added index
            $table->string('product_name')->nullable()->index(); // Make nullable
            $table->string('category')->nullable(); // Make nullable
            $table->string('brand')->nullable(); // Make nullable
            $table->integer('sold')->nullable(); // Make nullable
            $table->integer('purchase')->nullable()->index(); // Make nullable
            $table->double('average_price')->nullable(); // Make nullable
            $table->double('average_cost')->nullable()->index(); // Make nullable
            $table->double('report_price_og')->nullable(); // Make nullable
            $table->string('barcode')->nullable(); // Keep nullable
            $table->integer('transfer_in')->nullable(); // Keep nullable
            $table->integer('transfer_out')->nullable(); // Keep nullable
            $table->string('pos')->nullable(); // Make nullable
            $table->date('reconciliation_date')->nullable(); // Make nullable
            $table->integer('dqi_flag')->default(0)->index(); // Added index
            $table->integer('flag')->default(0)->index(); // Added index
            $table->text('comment')->nullable(); // Keep nullable
            $table->integer('opening_inventory_unit')->nullable(); // Make nullable
            $table->integer('closing_inventory_unit')->nullable(); // Make nullable
            $table->string('c_flag')->default('');
            $table->double('dqi_fee')->nullable(); // Keep nullable
            $table->double('dqi_per')->nullable(); // Keep nullable
            $table->unsignedBigInteger('offer_id')->nullable(); // Keep nullable
            $table->unsignedBigInteger('pos_report_id')->nullable(); // Keep nullable

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clean_sheets');
    }
}
