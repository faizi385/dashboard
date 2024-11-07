<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechPosReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tech_pos_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id')->index(); // Foreign key for reports table with index
            $table->string('branchname')->nullable()->index();
            $table->string('sku')->nullable()->index();
            $table->string('productname')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->string('categoryparent')->nullable();
            $table->string('brand')->nullable()->index();
            $table->decimal('costperunit', 10, 2)->nullable();
            $table->integer('openinventoryunits')->nullable();
            $table->decimal('openinventorycost', 10, 2)->nullable();
            $table->decimal('openinventoryvalue', 10, 2)->nullable();
            $table->integer('quantitypurchasedunits')->nullable();
            $table->decimal('quantitypurchasedcost', 10, 2)->nullable();
            $table->decimal('quantitypurchasedvalue', 10, 2)->nullable();
            $table->integer('quantitytransferinunits')->nullable();
            $table->decimal('quantitytransferincost', 10, 2)->nullable();
            $table->decimal('quantitytransferinvalue', 10, 2)->nullable();
            $table->integer('returnsfromcustomersunits')->nullable();
            $table->decimal('returnsfromcustomerscost', 10, 2)->nullable();
            $table->decimal('returnsfromcustomersvalue', 10, 2)->nullable();
            $table->integer('otheradditionsunits')->nullable();
            $table->decimal('otheradditionscost', 10, 2)->nullable();
            $table->decimal('otheradditionsvalue', 10, 2)->nullable();
            $table->integer('quantitysoldinstoreunits')->nullable();
            $table->decimal('quantitysoldinstorecost', 10, 2)->nullable();
            $table->decimal('quantitysoldinstorevalue', 10, 2)->nullable();
            $table->integer('quantitysoldonlineunits')->nullable();
            $table->decimal('quantitysoldonlinecost', 10, 2)->nullable();
            $table->decimal('quantitysoldonlinevalue', 10, 2)->nullable();
            $table->integer('quantitytransferoutunits')->nullable();
            $table->decimal('quantitytransferoutcost', 10, 2)->nullable();
            $table->decimal('quantitytransferoutvalue', 10, 2)->nullable();
            $table->integer('quantitydestroyedunits')->nullable();
            $table->decimal('quantitydestroyedcost', 10, 2)->nullable();
            $table->decimal('quantitydestroyedvalue', 10, 2)->nullable();
            $table->integer('quantitylosttheftunits')->nullable();
            $table->decimal('quantitylosttheftcost', 10, 2)->nullable();
            $table->decimal('quantitylosttheftvalue', 10, 2)->nullable();
            $table->integer('returnstodistributorunits')->nullable();
            $table->decimal('returnstodistributorcost', 10, 2)->nullable();
            $table->decimal('returnstodistributorvalue', 10, 2)->nullable();
            $table->integer('otherreductionsunits')->nullable();
            $table->decimal('otherreductionscost', 10, 2)->nullable();
            $table->decimal('otherreductionsvalue', 10, 2)->nullable();
            $table->integer('closinginventoryunits')->nullable();
            $table->decimal('closinginventorycost', 10, 2)->nullable();
            $table->decimal('closinginventoryvalue', 10, 2)->nullable();
            $table->timestamps();

            // Foreign key constraint (if needed)
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tech_pos_reports');
    }
}
