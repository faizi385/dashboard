<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTendySalesSummaryReportsTable extends Migration
{
    public function up()
    {
        Schema::create('tendy_sales_summary_reports', function (Blueprint $table) {
            // Define your table columns here
            $table->id();
            $table->foreignId('diagnostic_report_id')->constrained('tendy_diagnostic_reports')->onDelete('cascade');
            $table->string('location');
            $table->string('category');
            $table->string('brand');
            $table->string('product');
            $table->string('sku');
            $table->integer('items_sold');
            $table->integer('items_refunded');
            $table->integer('net_qty_sold');
            $table->decimal('gross_sales', 10, 2);
            $table->decimal('net_sales', 10, 2);
            $table->decimal('total_discounts', 10, 2);
            $table->decimal('markdown', 10, 2);
            $table->decimal('reward_tiers', 10, 2);
            $table->decimal('total_tax', 10, 2);
            $table->decimal('cost_of_goods_sold', 10, 2);
            $table->decimal('gross_profit', 10, 2);
            $table->decimal('avg_retail_price', 10, 2);
            $table->decimal('gross_margin', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tendy_sales_summary_reports');
    }
}
