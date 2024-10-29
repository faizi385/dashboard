<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable()->after('location');
            $table->string('province')->nullable()->after('province_id');
            $table->string('province_slug')->nullable()->after('province');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['province_id', 'province', 'province_slug']);
        });
    }
};
