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
        Schema::table('clean_sheets', function (Blueprint $table) {
            if (!Schema::hasColumn('clean_sheets', 'province')) {
                $table->string('province')->nullable()->after('province_id');
            }
        });
    }
    
    public function down()
    {
        Schema::table('clean_sheets', function (Blueprint $table) {
            if (Schema::hasColumn('clean_sheets', 'province')) {
                $table->dropColumn('province');
            }
        });
    }
    
    
};
