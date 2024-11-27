<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateNoDealProductsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW no_deal_products_view AS
            SELECT 
                lp_id,
                product_name,
                reconciliation_date,
                SUM(purchase) as total_purchase
            FROM 
                clean_sheets
            WHERE 
                offer_id IS NULL
            GROUP BY 
                lp_id, product_name, reconciliation_date
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS no_deal_products_view");
    }
}
