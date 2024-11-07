<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdealDiagnosticReportsTable extends Migration
{
    public function up()
    {
        Schema::create('ideal_diagnostic_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id')->index(); // Foreign key to link with report table, indexed
            $table->string('sku')->nullable()->index(); // Nullable and indexed
            $table->string('description')->nullable(); // Nullable
            $table->integer('opening')->nullable(); // Nullable
            $table->integer('purchases')->nullable(); // Nullable
            $table->integer('returns')->nullable(); // Nullable
            $table->integer('trans_in')->nullable(); // Nullable
            $table->integer('trans_out')->nullable(); // Nullable
            $table->integer('unit_sold')->nullable(); // Nullable
            $table->integer('write_offs')->nullable(); // Nullable
            $table->integer('closing')->nullable(); // Nullable
            $table->decimal('net_sales_ex', 10, 2)->nullable(); // Nullable

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ideal_diagnostic_reports');
    }
}
