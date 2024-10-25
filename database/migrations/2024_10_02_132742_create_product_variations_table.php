<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationsTable extends Migration
{
    public function up()
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('provincial_sku')->unique(); // Ensure SKU is unique
            $table->string('gtin');
            $table->string('province');
            $table->string('category');
            $table->string('brand');
            $table->foreignId('lp_id')->constrained()->onDelete('cascade');
            $table->integer('product_size');
            $table->string('thc_range');
            $table->string('cbd_range');
            $table->string('comment')->nullable();
            $table->string('product_link')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variations');
    }
}
