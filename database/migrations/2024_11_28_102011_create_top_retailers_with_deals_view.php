<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE VIEW top_retailers_with_deals AS
            SELECT 
                retailer_id, 
                lp_id, 
                reconciliation_date, 
                COUNT(DISTINCT offer_id) AS offer_count
            FROM clean_sheets
            WHERE 
                purchase > 0  -- Only include rows where purchase is greater than 0
                AND offer_id IS NOT NULL  -- Only include rows with an offer_id (indicating a deal)
            GROUP BY retailer_id, lp_id, reconciliation_date  -- Group by retailer_id, lp_id, and reconciliation_date
            ORDER BY offer_count DESC  -- Order by offer_count in descending order
            LIMIT 5  -- Limit the results to the top 5
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS top_retailers_with_deals');
    }
};
