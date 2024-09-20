<?php

// database/migrations/xxxx_xx_xx_create_lp_logs_table.php

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
            $table->string('action_user');
            $table->string('dba');
            $table->timestamp('time');
            $table->string('action');
            $table->text('description');
            $table->timestamps();

            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lp_logs');
    }
}
