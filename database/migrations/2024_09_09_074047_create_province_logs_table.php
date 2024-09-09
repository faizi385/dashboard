<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvinceLogsTable extends Migration
{
    public function up()
    {
        Schema::create('province_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // ID of the user who performed the action
            $table->unsignedBigInteger('province_id');         // ID of the affected province
            $table->string('action');                          // The action performed (created, updated, etc.)
            $table->text('description')->nullable();           // Description of changes
            $table->timestamps();                              // When the action occurred

            // Foreign key relationships
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('province_logs');
    }
}
