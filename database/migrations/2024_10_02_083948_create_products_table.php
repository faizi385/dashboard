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
            $table->string('product_name');
            $table->string('provincial_sku');
            $table->string('gtin');
            $table->string('province');
            $table->decimal('unit_cost', 10, 2);
            $table->string('category');
            $table->string('brand');
            $table->integer('case_quantity');
            $table->date('offer_start');
            $table->date('offer_end');
            $table->integer('product_size');
            $table->string('thc_range');
            $table->string('cbd_range');
            $table->text('comment')->nullable();
            $table->string('product_link')->nullable();
            $table->unsignedBigInteger('lp_id');
            $table->decimal('data_fee', 5, 2)->nullable();
            $table->unsignedBigInteger('retailer_id')->nullable();
            $table->date('offer_date');
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
