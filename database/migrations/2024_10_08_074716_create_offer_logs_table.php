<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferLogsTable extends Migration
{
    public function up()
    {
        Schema::create('offer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->json('description'); // Store changes in JSON format
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offer_logs');
    }
}
