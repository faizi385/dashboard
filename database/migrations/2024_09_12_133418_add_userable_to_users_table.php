<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserableToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('userable_id')->nullable();
            $table->string('userable_type')->nullable();
            
            // If you want to add a foreign key constraint:
            // $table->foreign('userable_id')->references('id')->on('roles');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('userable_id');
            $table->dropColumn('userable_type');
            
            // If you added a foreign key constraint, drop it as well:
            // $table->dropForeign(['userable_id']);
        });
    }
}
