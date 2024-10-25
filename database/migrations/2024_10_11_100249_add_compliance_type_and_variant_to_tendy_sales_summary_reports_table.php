<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComplianceTypeAndVariantToTendySalesSummaryReportsTable extends Migration
{
    public function up()
    {
        Schema::table('tendy_sales_summary_reports', function (Blueprint $table) {
            $table->string('compliance_type')->nullable(); // Add compliance_type
            $table->string('variant')->nullable(); // Add variant
        });
    }

    public function down()
    {
        Schema::table('tendy_sales_summary_reports', function (Blueprint $table) {
            $table->dropColumn(['compliance_type', 'variant']);
        });
    }
}
