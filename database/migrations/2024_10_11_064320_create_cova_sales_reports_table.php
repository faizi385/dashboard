<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovaSalesReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cova_sales_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade'); // Reference to the Report table
            $table->string('product')->index();
            $table->string('sku')->index();
            $table->string('classification')->nullable()->index();
            $table->integer('items_sold')->nullable();
            $table->integer('items_ref')->nullable();
            $table->decimal('net_sold', 10, 2)->nullable();
            $table->decimal('gross_sales', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->decimal('gross_profit', 10, 2)->nullable();
            $table->decimal('gross_margin', 10, 2)->nullable();
            $table->decimal('total_discount', 10, 2)->nullable();
            $table->decimal('markdown_percent', 10, 2)->nullable();
            $table->decimal('avg_regular_price', 10, 2)->nullable();
            $table->decimal('avg_sold_at_price', 10, 2)->nullable();
            $table->string('unit_type')->nullable();
            $table->decimal('net_weight', 10, 2)->nullable();
            $table->decimal('total_net_weight', 10, 2)->nullable();
            $table->string('brand')->nullable()->index();
            $table->string('supplier')->nullable()->index();
            $table->string('supplier_skus')->nullable();
            $table->decimal('total_tax', 10, 2)->nullable();
            $table->decimal('hst_13', 10, 2)->nullable();
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
        Schema::dropIfExists('cova_sales_reports');
    }
}
