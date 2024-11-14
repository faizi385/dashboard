<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToRetailersTable extends Migration
{
    public function up()
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->string('type')->nullable()->after('phone'); // Adjust the position as needed
        });
    }

    public function down()
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
