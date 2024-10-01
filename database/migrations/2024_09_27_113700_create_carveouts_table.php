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
            $table->foreignId('retailer_id')->constrained()->onDelete('cascade');
            $table->foreignId('lp_id')->constrained()->onDelete('cascade');
            $table->string('dba')->nullable();
            $table->string('address')->nullable();
            $table->string('carveout')->nullable();
            $table->string('location')->nullable();
            $table->string('sku')->nullable();
            $table->date('date')->nullable();
            $table->string('licence_producer')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carveouts');
    }
}
