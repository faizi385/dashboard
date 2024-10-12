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
        Schema::create('barnet_pos_reports', function (Blueprint $table) {
            $table->id();
            $table->string('store');
            $table->string('product_sku');
            $table->string('description');
            $table->string('uom');
            $table->string('category');
            $table->integer('opening_inventory_units');
            $table->decimal('opening_inventory_value', 8, 2);
            $table->integer('quantity_purchased_units');
            $table->decimal('quantity_purchased_value', 8, 2);
            $table->integer('returns_from_customers_units');
            $table->decimal('returns_from_customers_value', 8, 2);
            $table->integer('other_additions_units');
            $table->decimal('other_additions_value', 8, 2);
            $table->integer('quantity_sold_units');
            $table->decimal('quantity_sold_value', 8, 2);
            $table->integer('transfer_units');
            $table->decimal('transfer_value', 8, 2);
            $table->integer('returns_to_vendor_units');
            $table->decimal('returns_to_vendor_value', 8, 2);
            $table->integer('inventory_adjustment_units');
            $table->decimal('inventory_adjustment_value', 8, 2);
            $table->integer('destroyed_units');
            $table->decimal('destroyed_value', 8, 2);
            $table->integer('closing_inventory_units');
            $table->decimal('closing_inventory_value', 8, 2);
            $table->integer('min_stock');
            $table->integer('low_inv');
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barnet_pos_reports');
    }
};
