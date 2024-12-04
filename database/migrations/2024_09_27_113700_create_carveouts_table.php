<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarveoutsTable extends Migration
{
    public function up()
    {
        Schema::create('carveouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retailer_id')->constrained()->onDelete('cascade')->index(); // Foreign key for retailer with index
            $table->foreignId('lp_id')->constrained()->onDelete('cascade')->index(); // Foreign key for LP with index
            $table->string('dba')->nullable()->index(); // Make nullable and add index
            $table->string('address')->nullable(); // Make nullable
            $table->string('carveout')->nullable()->index(); // Make nullable and add index
            $table->string('location')->nullable()->index(); // Make nullable and add index
            $table->string('sku')->nullable()->index(); // Make nullable and add index
            $table->date('date')->nullable(); // Make nullable
            $table->string('licence_producer')->nullable(); // Make nullable
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carveouts');
    }
}
