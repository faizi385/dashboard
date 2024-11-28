<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateTopRetailersView extends Migration
{
    public function up()
    {
        DB::statement("
            CREATE VIEW top_retailers AS
            SELECT retailer_id, lp_id, reconciliation_date, SUM(purchase) as total_purchase
            FROM clean_sheets
            WHERE purchase > 0 AND offer_id IS NULL
            GROUP BY retailer_id, lp_id, reconciliation_date
            ORDER BY total_purchase DESC
            LIMIT 5
        ");
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS top_retailers');
    }
}
