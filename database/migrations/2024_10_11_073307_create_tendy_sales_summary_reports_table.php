<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTendySalesSummaryReportsTable extends Migration
{
    public function up()
    {
        Schema::create('tendy_sales_summary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_report_id')->constrained('tendy_diagnostic_reports')->onDelete('cascade'); // Reference to the diagnostic report table
            
            // Defining columns with nullable and indexing where appropriate
            $table->string('location')->nullable()->index();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->string('product')->nullable();
            $table->string('sku')->nullable()->index();
            $table->integer('items_sold')->nullable();
            $table->integer('items_refunded')->nullable();
            $table->integer('net_qty_sold')->nullable();
            $table->decimal('gross_sales', 10, 2)->nullable();
            $table->decimal('net_sales', 10, 2)->nullable();
            $table->decimal('total_discounts', 10, 2)->nullable();
            $table->decimal('markdown', 10, 2)->nullable();
            $table->decimal('reward_tiers', 10, 2)->nullable();
            $table->decimal('total_tax', 10, 2)->nullable();
            $table->decimal('cost_of_goods_sold', 10, 2)->nullable();
            $table->decimal('gross_profit', 10, 2)->nullable();
            $table->decimal('avg_retail_price', 10, 2)->nullable();
            $table->decimal('gross_margin', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tendy_sales_summary_reports');
    }
}
