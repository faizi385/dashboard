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
            $table->unsignedBigInteger('report_id')->nullable(); // Foreign key for reports table if needed
            $table->string('sku'); // SKU Column
            $table->string('name'); // Name Column
            $table->string('barcode')->nullable(); // Barcode Column
            $table->string('brand')->nullable(); // Brand Column
            $table->string('compliance_category')->nullable(); // Compliance Category Column
            $table->integer('opening')->nullable(); // Opening Column
            $table->integer('sold')->nullable(); // Sold Column
            $table->integer('purchased')->nullable(); // Purchased Column
            $table->integer('closing')->nullable(); // Closing Column
            $table->decimal('average_price', 10, 2)->nullable(); // Average Price Column
            $table->decimal('average_cost', 10, 2)->nullable(); // Average Cost Column
            $table->timestamps(); // Created and Updated Timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('greenline_reports');
    }
}
