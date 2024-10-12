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
            $table->unsignedBigInteger('report_id');  // Foreign key to reports table
            $table->string('compliance_code');
            $table->string('supplier_sku');
            $table->integer('opening_inventory');
            $table->decimal('opening_inventory_value', 10, 2);
            $table->integer('purchases_from_suppliers_additions');
            $table->decimal('purchases_from_suppliers_value', 10, 2);
            $table->integer('returns_from_customers_additions');
            $table->decimal('customer_returns_retail_value', 10, 2);
            $table->integer('other_additions_additions');
            $table->decimal('other_additions_value', 10, 2);
            $table->integer('sales_reductions');
            $table->decimal('sold_retail_value', 10, 2);
            $table->integer('destruction_reductions');
            $table->decimal('destruction_value', 10, 2);
            $table->integer('theft_reductions');
            $table->decimal('theft_value', 10, 2);
            $table->integer('returns_to_suppliers_reductions');
            $table->decimal('supplier_return_value', 10, 2);
            $table->integer('other_reductions_reductions');
            $table->decimal('other_reductions_value', 10, 2);
            $table->integer('closing_inventory');
            $table->decimal('closing_inventory_value', 10, 2);

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
