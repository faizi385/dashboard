<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherPosReportsTable extends Migration
{
    public function up()
    {
        Schema::create('other_pos_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('sku');
            $table->string('name');
            $table->string('barcode')->nullable();
            $table->string('brand');
            $table->string('compliance_category')->nullable();
            $table->decimal('opening', 10, 2)->default(0);
            $table->decimal('sold', 10, 2)->default(0);
            $table->decimal('purchased', 10, 2)->default(0);
            $table->decimal('closing', 10, 2)->default(0);
            $table->decimal('average_price', 10, 2)->default(0);
            $table->decimal('average_cost', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('other_pos_reports');
    }
}
