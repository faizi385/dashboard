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
       // database/migrations/create_carveout_logs_table.php
Schema::create('carveout_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('carveout_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Action user
    $table->string('action'); // e.g., 'created', 'updated'
    $table->json('description')->nullable(); // Detailed action data
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carveout_logs');
    }
};
