<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lp_id')->constrained()->onDelete('cascade')->index(); // Foreign key for LP with index
            $table->string('product_name')->nullable()->index(); // Make nullable and add index
            $table->string('provincial_sku')->nullable()->index(); // Make nullable and add index
            $table->string('gtin')->nullable()->index(); // Make nullable and add index
            $table->string('province')->nullable()->index(); // Make nullable and add index
            $table->decimal('data_fee', 5, 2)->nullable(); // Make nullable
            $table->decimal('unit_cost', 10, 2)->nullable()->index(); // Make nullable
            $table->string('category')->nullable()->index(); // Make nullable and add index
            $table->string('brand')->nullable()->index(); // Make nullable and add index
            $table->integer('case_quantity')->nullable(); // Make nullable
            $table->date('offer_start')->nullable(); // Make nullable
            $table->date('offer_end')->nullable(); // Make nullable
            $table->integer('product_size')->nullable(); // Make nullable
            $table->string('thc_range')->nullable(); // Make nullable
            $table->string('cbd_range')->nullable(); // Make nullable
            $table->text('comment')->nullable(); // Make nullable
            $table->string('product_link')->nullable(); // Make nullable
            $table->date('offer_date')->nullable(); // Make nullable
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
