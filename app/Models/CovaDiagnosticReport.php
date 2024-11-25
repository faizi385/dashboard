<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CovaDiagnosticReport extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow Laravel's naming convention
    protected $table = 'cova_diagnostic_reports';

    // Define the fillable fields
    protected $fillable = [
        'report_id',
        'product_name',
        'type',
        'aglc_sku',
        'new_brunswick_sku',
        'ocs_sku',
        'ylc_sku',
        'manitoba_barcodeupc',
        'ontario_barcodeupc',
        'saskatchewan_barcodeupc',
        'link_to_product',
        'opening_inventory_units',
        'quantity_purchased_units',
        'reductions_receiving_error_units',
        'returns_from_customers_units',
        'other_additions_units',
        'quantity_sold_units',
        'quantity_destroyed_units',
        'quantity_lost_theft_units',
        'returns_to_supplier_units',
        'other_reductions_units',
        'closing_inventory_units',
        'status',
        'retailer_id',
        'lp_id',
          'date'
    ];

    public function CovaSalesSummaryReport()
    {
        return $this->hasOne(CovaSalesReport::class, 'cova_diagnostic_report_id', 'id');
    }

    // Define relationships if necessary, for example, a report belongs to a parent Report
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
