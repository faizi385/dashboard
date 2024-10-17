<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToRetailersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('retailers', 'status')) {
            Schema::table('retailers', function (Blueprint $table) {
                $table->string('status')->default('requested'); // Add the status column
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('retailers', 'status')) {
            Schema::table('retailers', function (Blueprint $table) {
                $table->dropColumn('status'); // Drop the status column if it exists
            });
        }
    }
}
