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
            $table->foreignId('lp_id')->constrained()->onDelete('cascade'); // Foreign key for LP
            $table->string('product_name');
            $table->string('provincial_sku');
            $table->string('gtin');
            $table->string('province');
            $table->decimal('data_fee', 5, 2);
            $table->decimal('unit_cost', 10, 2);
            $table->string('category');
            $table->string('brand');
            $table->integer('case_quantity');
            $table->date('offer_start');
            $table->date('offer_end');
            $table->integer('product_size'); // Assuming size is in grams
            $table->string('thc_range');
            $table->string('cbd_range');
            $table->text('comment');
            $table->string('product_link');
            $table->date('offer_date'); // New field for offer date
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
