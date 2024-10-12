<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTendyDiagnosticReportsTable extends Migration
{
    public function up()
    {
        Schema::create('tendy_diagnostic_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade'); // Assuming a report ID exists
            $table->string('product_sku')->nullable();
            $table->integer('opening_inventory_units')->nullable();
            $table->decimal('opening_inventory_value', 10, 2)->nullable();
            $table->integer('quantity_purchased_units')->nullable();
            $table->decimal('quantity_purchased_value', 10, 2)->nullable();
            $table->integer('quantity_purchased_units_transfer')->nullable();
            $table->decimal('quantity_purchased_value_transfer', 10, 2)->nullable();
            $table->integer('returns_from_customers_units')->nullable();
            $table->decimal('returns_from_customers_value', 10, 2)->nullable();
            $table->integer('other_additions_units')->nullable();
            $table->decimal('other_additions_value', 10, 2)->nullable();
            $table->integer('quantity_sold_instore_units')->nullable();
            $table->decimal('quantity_sold_instore_value', 10, 2)->nullable();
            $table->integer('quantity_sold_online_units')->nullable();
            $table->decimal('quantity_sold_online_value', 10, 2)->nullable();
            $table->integer('quantity_sold_units_transfer')->nullable();
            $table->decimal('quantity_sold_value_transfer', 10, 2)->nullable();
            $table->integer('quantity_destroyed_units')->nullable();
            $table->decimal('quantity_destroyed_value', 10, 2)->nullable();
            $table->integer('quantity_losttheft_units')->nullable();
            $table->decimal('quantity_losttheft_value', 10, 2)->nullable();
            $table->integer('returns_to_aglc_units')->nullable();
            $table->decimal('returns_to_aglc_value', 10, 2)->nullable();
            $table->integer('other_reductions_units')->nullable();
            $table->decimal('other_reductions_value', 10, 2)->nullable();
            $table->integer('closing_inventory_units')->nullable();
            $table->decimal('closing_inventory_value', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tendy_diagnostic_reports');
    }
}
