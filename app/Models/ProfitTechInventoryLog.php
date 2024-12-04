<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitTechInventoryLog extends Model
{
    use HasFactory;

    protected $table = 'profittech_pos_reports'; // Specify the new table name

    // Fillable fields for mass assignment
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
        'quantity_losttheft_units',
        'quantity_losttheft_value',
        'returns_to_aglc_units',
        'returns_to_aglc_value',
        'other_reductions_units',
        'other_reductions_value',
        'closing_inventory_units',
        'closing_inventory_value',
        'status',  
        'retailer_id',
        'lp_id',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
