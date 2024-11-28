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
            WITH ranked_products AS (
                SELECT 
                    lp_id,
                    product_name,
                    reconciliation_date,
                    SUM(purchase) AS total_purchase,
                    ROW_NUMBER() OVER (PARTITION BY lp_id ORDER BY SUM(purchase) DESC) AS rank
                FROM 
                    clean_sheets
                WHERE 
                    offer_id IS NULL
                    AND purchase > 0  
                GROUP BY 
                    lp_id, 
                    product_name, 
                    reconciliation_date
            )
            SELECT 
                lp_id,
                product_name,
                reconciliation_date,
                total_purchase
            FROM 
                ranked_products
            WHERE 
                rank <= 5
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
