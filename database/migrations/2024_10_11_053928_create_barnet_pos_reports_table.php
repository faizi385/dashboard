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
            $table->string('store')->nullable()->index();
            $table->string('product_sku')->nullable()->index();
            $table->string('description')->nullable()->index();
            $table->string('uom')->nullable();
            $table->string('category')->nullable()->index();
            $table->integer('opening_inventory_units')->nullable();
            $table->decimal('opening_inventory_value', 8, 2)->nullable();
            $table->integer('quantity_purchased_units')->nullable();
            $table->decimal('quantity_purchased_value', 8, 2)->nullable();
            $table->integer('returns_from_customers_units')->nullable();
            $table->decimal('returns_from_customers_value', 8, 2)->nullable();
            $table->integer('other_additions_units')->nullable();
            $table->decimal('other_additions_value', 8, 2)->nullable();
            $table->integer('quantity_sold_units')->nullable();
            $table->decimal('quantity_sold_value', 8, 2)->nullable();
            $table->integer('transfer_units')->nullable();
            $table->decimal('transfer_value', 8, 2)->nullable();
            $table->integer('returns_to_vendor_units')->nullable();
            $table->decimal('returns_to_vendor_value', 8, 2)->nullable();
            $table->integer('inventory_adjustment_units')->nullable();
            $table->decimal('inventory_adjustment_value', 8, 2)->nullable();
            $table->integer('destroyed_units')->nullable();
            $table->decimal('destroyed_value', 8, 2)->nullable();
            $table->integer('closing_inventory_units')->nullable();
            $table->decimal('closing_inventory_value', 8, 2)->nullable();
            $table->integer('min_stock')->nullable();
            $table->integer('low_inv')->nullable();
            $table->foreignId('report_id')->nullable()->constrained()->onDelete('cascade'); // Foreign key with nullable and cascading delete
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
