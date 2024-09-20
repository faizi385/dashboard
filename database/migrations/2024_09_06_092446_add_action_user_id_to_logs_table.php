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
        Schema::table('logs', function (Blueprint $table) {
            $table->unsignedBigInteger('action_user_id')->nullable();
    
            // Assuming there is a users table
            $table->foreign('action_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign(['action_user_id']);
            $table->dropColumn('action_user_id');
        });
    }
    
};
