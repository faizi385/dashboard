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
        Schema::table('offers', function (Blueprint $table) {
            // Add retailer_id column and set foreign key constraint
            $table->unsignedBigInteger('retailer_id')->nullable()->after('id');
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            // Remove foreign key and column
            $table->dropForeign(['retailer_id']);
            $table->dropColumn('retailer_id');
        });
    }
};
