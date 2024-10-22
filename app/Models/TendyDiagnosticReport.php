<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TendyDiagnosticReport extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'tendy_diagnostic_reports';

    protected $fillable = [
        'report_id',
        'product_sku',
        'opening_inventory_units',
        'opening_inventory_value',
        'quantity_purchased_units',
        'quantity_purchased_value',
        'quantity_purchased_units_transfer',
        'quantity_purchased_value_transfer',
        'returns_from_customers_units',
        'returns_from_customers_value',
        'other_additions_units',
        'other_additions_value',
        'quantity_sold_instore_units',
        'quantity_sold_instore_value',
        'quantity_sold_online_units',
        'quantity_sold_online_value',
        'quantity_sold_units_transfer',
        'quantity_sold_value_transfer',
        'quantity_destroyed_units',
        'quantity_destroyed_value',
        'quantity_lost_units', // Updated
        'quantity_lost_value', // Updated
        'returns_to_aglc_units',
        'returns_to_aglc_value',
        'other_reductions_units',
        'other_reductions_value',
        'closing_inventory_units',
        'closing_inventory_value',
        'status',  
    ];
    
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
