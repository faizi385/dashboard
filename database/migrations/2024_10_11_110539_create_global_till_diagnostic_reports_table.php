<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalTillDiagnosticReportsTable extends Migration
{
    public function up()
    {
        Schema::create('global_till_diagnostic_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id')->index(); // Foreign key to reports table with index
            $table->string('storelocation')->nullable()->index();
            $table->string('store_sku')->nullable()->index();
            $table->string('product')->nullable();
            $table->string('compliance_code')->nullable();
            $table->string('supplier_sku')->nullable();
            $table->decimal('pos_equivalent_grams', 10, 2)->nullable();
            $table->decimal('compliance_weight', 10, 2)->nullable();
            $table->integer('opening_inventory')->nullable();
            $table->integer('purchases_from_suppliers_additions')->nullable();
            $table->integer('returns_from_customers_additions')->nullable();
            $table->integer('other_additions_additions')->nullable();
            $table->integer('sales_reductions')->nullable();
            $table->integer('destruction_reductions')->nullable();
            $table->integer('theft_reductions')->nullable();
            $table->integer('returns_to_suppliers_reductions')->nullable();
            $table->integer('other_reductions_reductions')->nullable();
            $table->integer('closing_inventory')->nullable();
            $table->string('product_url')->nullable();
            $table->string('inventory_transactions_url')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('global_till_diagnostic_reports');
    }
}
