<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGreenlineReportsTable extends Migration
{
    public function up()
    {
        Schema::create('greenline_reports', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('report_id')->nullable()->index(); // Foreign key for reports table with index
            $table->string('sku')->nullable()->index(); // SKU column with index and nullable
            $table->string('name')->nullable(); // Name column with nullable
            $table->string('barcode')->nullable()->index(); // Barcode column with index and nullable
            $table->string('brand')->nullable()->index(); // Brand column with index and nullable
            $table->string('compliance_category')->nullable()->index(); // Compliance category column with index and nullable
            $table->integer('opening')->nullable(); // Opening column with nullable
            $table->integer('sold')->nullable(); // Sold column with nullable
            $table->integer('purchased')->nullable(); // Purchased column with nullable
            $table->integer('closing')->nullable(); // Closing column with nullable
            $table->decimal('average_price', 10, 2)->nullable(); // Average price column with nullable
            $table->decimal('average_cost', 10, 2)->nullable(); // Average cost column with nullable
            $table->timestamps(); // Created and Updated timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('greenline_reports');
    }
}
