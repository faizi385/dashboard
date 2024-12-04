<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLpIdToReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('lp_id')->nullable()->after('id'); // Add lp_id column after the 'id' column

            // Optional: Add foreign key constraint if you have an `lps` table
            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['lp_id']); // Drop foreign key constraint if added
            $table->dropColumn('lp_id');
        });
    }
}
