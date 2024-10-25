<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('retailers', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable(); // Add user_id column
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Create foreign key relationship
    });
}

public function down()
{
    Schema::table('retailers', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}

};
