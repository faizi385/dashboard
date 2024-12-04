<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLpLogsTable extends Migration
{
    public function up()
    {
        Schema::create('lp_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lp_id');
            $table->string('action_user')->nullable(); // Made nullable
            $table->string('dba')->nullable();         // Made nullable
            $table->timestamp('time')->nullable();     // Made nullable
            $table->string('action')->nullable();      // Made nullable
            $table->text('description')->nullable();   // Made nullable
            $table->timestamps();

            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lp_logs');
    }
}
