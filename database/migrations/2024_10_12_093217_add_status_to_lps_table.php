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
        Schema::table('lps', function (Blueprint $table) {
            $table->string('status')->default('pending'); // Default status can be 'pending', 'approved', etc.
        });
    }
    
    public function down()
    {
        Schema::table('lps', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
    
};
