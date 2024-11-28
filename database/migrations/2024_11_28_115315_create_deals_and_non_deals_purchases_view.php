<?php

use Illuminate\Support\Facades\DB;
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
            CREATE VIEW deals_and_non_deals_purchases AS
            SELECT 
                retailer_id,
                reconciliation_date,
                SUM(CASE WHEN offer_id IS NOT NULL THEN purchase ELSE 0 END) AS total_deals_purchase,
                SUM(CASE WHEN offer_id IS NULL THEN purchase ELSE 0 END) AS total_non_deals_purchase
            FROM clean_sheets
            WHERE purchase > 0
            GROUP BY retailer_id, reconciliation_date
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS deals_and_non_deals_purchases');
    }
};
