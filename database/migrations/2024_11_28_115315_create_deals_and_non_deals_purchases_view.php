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
            CREATE OR REPLACE VIEW `deals_and_non_deals_purchases` AS
            SELECT
                `clean_sheets`.`retailer_id` AS `retailer_id`,
                `clean_sheets`.`reconciliation_date` AS `reconciliation_date`,
                SUM(CASE WHEN `clean_sheets`.`offer_id` IS NOT NULL THEN `clean_sheets`.`purchase` ELSE 0 END) AS `total_deals_purchase`,
                SUM(CASE WHEN `clean_sheets`.`offer_id` IS NULL THEN `clean_sheets`.`purchase` ELSE 0 END) AS `total_non_deals_purchase`
            FROM `clean_sheets`
            WHERE `clean_sheets`.`purchase` > 0
            GROUP BY `clean_sheets`.`retailer_id`, `clean_sheets`.`reconciliation_date`
        ');
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS `deals_and_non_deals_purchases`');
    }
};





