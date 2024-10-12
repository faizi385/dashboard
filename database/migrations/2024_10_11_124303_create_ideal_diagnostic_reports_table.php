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
            $table->unsignedBigInteger('report_id'); // Foreign key to link with report table
            $table->string('sku');
            $table->string('description');
            $table->integer('opening');
            $table->integer('purchases');
            $table->integer('returns');
            $table->integer('trans_in');
            $table->integer('trans_out');
            $table->integer('unit_sold');
            $table->integer('write_offs');
            $table->integer('closing');
            $table->decimal('net_sales_ex', 10, 2); // Adjust precision and scale as needed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ideal_diagnostic_reports');
    }
}
