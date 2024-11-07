<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobaltillSalesSummaryReportsTable extends Migration
{
    public function up()
    {
        Schema::create('globaltill_sales_summary_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id')->index(); // Foreign key to reports table with index
            $table->string('compliance_code')->nullable()->index();
            $table->string('supplier_sku')->nullable()->index();
            $table->integer('opening_inventory')->nullable();
            $table->decimal('opening_inventory_value', 10, 2)->nullable();
            $table->integer('purchases_from_suppliers_additions')->nullable();
            $table->decimal('purchases_from_suppliers_value', 10, 2)->nullable();
            $table->integer('returns_from_customers_additions')->nullable();
            $table->decimal('customer_returns_retail_value', 10, 2)->nullable();
            $table->integer('other_additions_additions')->nullable();
            $table->decimal('other_additions_value', 10, 2)->nullable();
            $table->integer('sales_reductions')->nullable();
            $table->decimal('sold_retail_value', 10, 2)->nullable();
            $table->integer('destruction_reductions')->nullable();
            $table->decimal('destruction_value', 10, 2)->nullable();
            $table->integer('theft_reductions')->nullable();
            $table->decimal('theft_value', 10, 2)->nullable();
            $table->integer('returns_to_suppliers_reductions')->nullable();
            $table->decimal('supplier_return_value', 10, 2)->nullable();
            $table->integer('other_reductions_reductions')->nullable();
            $table->decimal('other_reductions_value', 10, 2)->nullable();
            $table->integer('closing_inventory')->nullable();
            $table->decimal('closing_inventory_value', 10, 2)->nullable();

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('globaltill_sales_summary_reports');
    }
}
