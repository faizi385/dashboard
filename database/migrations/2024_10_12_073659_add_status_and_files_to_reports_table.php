<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndFilesToReportsTable extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('status')->default('pending'); // Add status with default value
            $table->unsignedBigInteger('submitted_by')->nullable(); // Add submitted_by for user who submitted
            $table->string('file_1')->nullable(); // Add file_1 for first file upload
            $table->string('file_2')->nullable(); // Add file_2 for second file upload

            // Optional: If you have a users table and want to add foreign key constraint for submitted_by
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['status', 'submitted_by', 'file_1', 'file_2']);
        });
    }
}
