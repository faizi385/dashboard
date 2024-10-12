<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovaSalesReportsTable extends Migration
{
    public function up()
    {
        Schema::create('cova_sales_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade'); // Reference to the Report table
            $table->string('product');
            $table->string('sku');
            $table->string('classification');
            $table->integer('items_sold');
            $table->integer('items_ref');
            $table->decimal('net_sold', 10, 2);
            $table->decimal('gross_sales', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->decimal('gross_profit', 10, 2);
            $table->decimal('gross_margin', 10, 2);
            $table->decimal('total_discount', 10, 2);
            $table->decimal('markdown_percent', 10, 2);
            $table->decimal('avg_regular_price', 10, 2);
            $table->decimal('avg_sold_at_price', 10, 2);
            $table->string('unit_type');
            $table->decimal('net_weight', 10, 2);
            $table->decimal('total_net_weight', 10, 2);
            $table->string('brand');
            $table->string('supplier');
            $table->string('supplier_skus');
            $table->decimal('total_tax', 10, 2);
            $table->decimal('hst_13', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cova_sales_reports');
    }
}
