<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToLpsTable extends Migration
{
    public function up()
    {
        Schema::table('lps', function (Blueprint $table) {
            $table->softDeletes(); // This will create a deleted_at column
        });
    }

    public function down()
    {
        Schema::table('lps', function (Blueprint $table) {
            $table->dropSoftDeletes(); // This will drop the deleted_at column
        });
    }
}
