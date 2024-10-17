<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostalCodeToRetailerAddressesTable extends Migration
{
    public function up()
    {
        Schema::table('retailer_addresses', function (Blueprint $table) {
            $table->string('postal_code')->nullable(); // Add the postal_code column
        });
    }

    public function down()
    {
        Schema::table('retailer_addresses', function (Blueprint $table) {
            $table->dropColumn('postal_code'); // Drop the postal_code column if rolling back
        });
    }
}
