<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->nullable()->index(); // Make nullable and add index
            $table->string('provincial_sku')->nullable()->index(); // Make nullable and add index
            $table->string('gtin')->nullable()->index(); // Make nullable and add index
            $table->string('province')->nullable()->index(); // Make nullable and add index
            $table->decimal('unit_cost', 10, 2)->nullable()->index(); // Make nullable
            $table->string('category')->nullable()->index(); // Make nullable and add index
            $table->string('brand')->nullable()->index(); // Make nullable and add index
            $table->integer('case_quantity')->nullable(); // Make nullable
            $table->date('offer_start')->nullable(); // Make nullable
            $table->date('offer_end')->nullable(); // Make nullable
            $table->string('product_size')->nullable(); // Make nullable
            $table->string('thc_range')->nullable(); // Make nullable
            $table->string('cbd_range')->nullable(); // Make nullable
            $table->text('comment')->nullable(); // Keep nullable
            $table->string('product_link')->nullable(); // Keep nullable
            $table->unsignedBigInteger('lp_id')->index(); // Added index
            $table->decimal('data_fee', 5, 2)->nullable(); // Keep nullable
            $table->unsignedBigInteger('retailer_id')->nullable()->index(); // Keep nullable and add index
            $table->date('offer_date')->nullable(); // Make nullable
            $table->timestamps();

            // Foreign keys
            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('cascade');
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
