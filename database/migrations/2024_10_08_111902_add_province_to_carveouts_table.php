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
        Schema::table('carveouts', function (Blueprint $table) {
            $table->string('province')->nullable(); // or use $table->string('province'); if you don't want it to be nullable
        });
    }
    
    public function down()
    {
        Schema::table('carveouts', function (Blueprint $table) {
            $table->dropColumn('province');
        });
    }
    
};
