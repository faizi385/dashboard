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
            $table->string('primary_contact_phone')->nullable();
            $table->string('primary_contact_position')->nullable();
            $table->string('password')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('lps', function (Blueprint $table) {
            $table->dropColumn(['primary_contact_phone', 'primary_contact_position', 'password']);
        });
    }
    
};
