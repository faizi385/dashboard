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
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id')->nullable()->after('location'); // Replace 'column_name' with the appropriate column to place the new column after, if necessary.
    
            // If the address table exists, you can also create a foreign key constraint:
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
        });
    }
    
};
