<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdealSalesSummaryReportsTable extends Migration
{
    public function up()
    {
        Schema::create('ideal_sales_summary_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id'); // Foreign key to link to the reports table
            $table->string('location');
            $table->string('sku');
            $table->string('product_description');
            $table->integer('quantity_purchased');
            $table->decimal('purchase_amount', 10, 2);
            $table->integer('return_quantity')->nullable();
            $table->decimal('amount_return', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ideal_sales_summary_reports');
    }
}
