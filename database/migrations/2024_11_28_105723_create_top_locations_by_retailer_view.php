<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            CREATE VIEW top_locations_by_retailer AS
            SELECT 
                retailer_id,
                location,
                reconciliation_date,
                SUM(purchase) AS total_purchase
            FROM clean_sheets
            WHERE purchase > 0
            GROUP BY retailer_id, location, reconciliation_date
            ORDER BY retailer_id, reconciliation_date, total_purchase DESC
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS top_locations_by_retailer');
    }
};
