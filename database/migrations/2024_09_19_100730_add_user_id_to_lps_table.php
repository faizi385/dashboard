<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_user_id_to_lps_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToLpsTable extends Migration
{
    public function up()
    {
        Schema::table('lps', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id'); // Adjust position if necessary
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('lps', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
