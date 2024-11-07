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
        Schema::create('cova_diagnostic_reports', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->index();
            $table->string('type')->index();
            $table->string('aglc_sku')->nullable()->index();
            $table->string('new_brunswick_sku')->nullable()->index();
            $table->string('ocs_sku')->nullable()->index();
            $table->string('ylc_sku')->nullable()->index();
            $table->string('manitoba_barcodeupc')->nullable()->index();
            $table->string('ontario_barcodeupc')->nullable()->index();
            $table->string('saskatchewan_barcodeupc')->nullable()->index();
            $table->string('link_to_product')->nullable();
            $table->integer('opening_inventory_units')->nullable();
            $table->integer('quantity_purchased_units')->nullable();
            $table->integer('reductions_receiving_error_units')->nullable();
            $table->integer('returns_from_customers_units')->nullable();
            $table->integer('other_additions_units')->nullable();
            $table->integer('quantity_sold_units')->nullable();
            $table->integer('quantity_destroyed_units')->nullable();
            $table->integer('quantity_lost_theft_units')->nullable();
            $table->integer('returns_to_supplier_units')->nullable();
            $table->integer('other_reductions_units')->nullable();
            $table->integer('closing_inventory_units')->nullable();
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
        Schema::dropIfExists('cova_diagnostic_reports');
    }
};
