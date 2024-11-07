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
            $table->string('product_name')->nullable()->index(); // Make nullable and add index
            $table->string('provincial_sku')->unique(); // Ensure SKU is unique
            $table->string('gtin')->nullable()->index(); // Make nullable and add index
            $table->string('province')->nullable()->index(); // Make nullable and add index
            $table->string('category')->nullable()->index(); // Make nullable and add index
            $table->string('brand')->nullable()->index(); // Make nullable and add index
            $table->foreignId('lp_id')->constrained()->onDelete('cascade')->index(); // Added index
            $table->integer('product_size')->nullable(); // Make nullable
            $table->string('thc_range')->nullable(); // Make nullable
            $table->string('cbd_range')->nullable(); // Make nullable
            $table->string('comment')->nullable(); // Keep nullable
            $table->string('product_link')->nullable(); // Keep nullable
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variations');
    }
}
