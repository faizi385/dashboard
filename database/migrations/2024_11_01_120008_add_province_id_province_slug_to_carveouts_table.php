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
            $table->unsignedBigInteger('province_id')->nullable()->after('province'); // Adjust 'existing_column' to specify placement
            $table->string('province_slug')->nullable()->after('province_id');

            // Add foreign key constraint if there's a provinces table
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carveouts', function (Blueprint $table) {
            $table->dropForeign(['province_id']); // Drops the foreign key constraint
            $table->dropColumn(['province_id', 'province_slug']);
        });
    }
};
