<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportLogsTable extends Migration
{
    public function up()
    {
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->json('description'); // Store the description as JSON
            $table->timestamps();

            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_logs');
    }
}
