<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailerStatementsTable extends Migration
{
    public function up()
    {
        Schema::create('retailer_statements', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('lp_id')->nullable()->index(); // LP ID
            $table->unsignedBigInteger('province_id')->nullable()->index(); // Province ID
            $table->string('province')->nullable()->index(); // Province name
            $table->string('province_slug')->nullable(); // Province slug
            $table->unsignedBigInteger('retailer_id')->nullable()->index(); // Retailer ID
            $table->string('product_name')->nullable()->index(); // Product name
            $table->string('sku')->nullable()->index(); // SKU
            $table->string('barcode')->nullable()->index(); // Barcode
            $table->integer('quantity')->nullable(); // Quantity
            $table->integer('quantity_sold')->nullable(); // Quantity sold
            $table->double('unit_cost')->nullable(); // Unit cost
            $table->double('cs_unit_cost')->nullable(); // Cost of goods sold unit cost
            $table->double('total_purchase_cost')->nullable(); // Total purchase cost
            $table->double('fee_per')->nullable(); // Fee per unit
            $table->double('fee_in_dollar')->nullable(); // Fee in dollars
            $table->double('ircc_per')->nullable(); // IRCC fee per unit
            $table->double('ircc_dollar')->nullable(); // IRCC fee in dollars
            $table->double('total_fee')->nullable(); // Total fees
            $table->unsignedBigInteger('report_id')->nullable()->index(); // Report ID
            $table->string('carve_out')->nullable(); // Carve-out
            $table->double('average_price')->nullable(); // Average price
            $table->integer('opening_inventory_unit')->nullable(); // Opening inventory units
            $table->integer('closing_inventory_unit')->nullable(); // Closing inventory units
            $table->string('category')->nullable()->index(); // Category
            $table->string('brand')->nullable()->index(); // Brand
            $table->integer('flag')->nullable(); // Flag
            $table->unsignedBigInteger('product_variation_id')->nullable()->index(); // Product variation ID
            $table->unsignedBigInteger('clean_sheet_id')->nullable()->index(); // Clean sheet ID
            $table->date('reconciliation_date')->nullable(); // Reconciliation date
            $table->timestamps(); // Created at and Updated at timestamps

            // Foreign key constraints (if needed)
            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('cascade');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
            $table->foreign('product_variation_id')->references('id')->on('product_variations')->onDelete('cascade');
            $table->foreign('clean_sheet_id')->references('id')->on('clean_sheets')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('retailer_statements');
    }
}
