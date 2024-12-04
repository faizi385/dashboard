<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailerLpsTable extends Migration
{
    public function up()
    {
        Schema::create('retailer_lps', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->unsignedBigInteger('retailer_id'); // Foreign key for retailer
            $table->unsignedBigInteger('lp_id'); // Foreign key for LP
            $table->string('first_name'); // First name
            $table->string('last_name'); // Last name
            $table->string('email'); // Email

            // Add foreign key constraints
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('cascade');

            $table->timestamps(); // For created_at and updated_at columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('retailer_lps');
    }
}
