<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarnetPosReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'store', 'product_sku', 'description', 'uom', 'category', 
        'opening_inventory_units', 'opening_inventory_value', 
        'quantity_purchased_units', 'quantity_purchased_value', 
        'returns_from_customers_units', 'returns_from_customers_value', 
        'other_additions_units', 'other_additions_value', 
        'quantity_sold_units', 'quantity_sold_value', 
        'transfer_units', 'transfer_value', 
        'returns_to_vendor_units', 'returns_to_vendor_value', 
        'inventory_adjustment_units', 'inventory_adjustment_value', 
        'destroyed_units', 'destroyed_value', 
        'closing_inventory_units', 'closing_inventory_value', 
        'min_stock', 'low_inv', 'report_id','status',  
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
