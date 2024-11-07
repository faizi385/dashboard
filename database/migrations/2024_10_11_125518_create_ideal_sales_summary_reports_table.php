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
            $table->unsignedBigInteger('report_id')->index(); // Foreign key to link to the reports table, indexed
            $table->string('location')->nullable(); // Nullable
            $table->string('sku')->nullable()->index(); // Nullable and indexed
            $table->string('product_description')->nullable(); // Nullable
            $table->integer('quantity_purchased')->nullable(); // Nullable
            $table->decimal('purchase_amount', 10, 2)->nullable(); // Nullable
            $table->integer('return_quantity')->nullable(); // Nullable
            $table->decimal('amount_return', 10, 2)->nullable(); // Nullable

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ideal_sales_summary_reports');
    }
}
