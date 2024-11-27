<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerStatement extends Model
{
    use HasFactory;

    protected $table = 'retailer_statements';

    public $timestamps = false; // Disable automatic timestamps

    protected $fillable = [
        'lp_id', 
        'province_id', 
        'province', 
        'province_slug', 
        'retailer_id', 
        'product_name', 
        'sku', 
        'barcode', 
        'quantity', 
        'quantity_sold', 
        'unit_cost', 
        'cs_unit_cost', 
        'total_purchase_cost', 
        'fee_per', 
        'fee_in_dollar', 
        'ircc_per', 
        'ircc_dollar', 
        'total_fee', 
        'report_id', 
        'carev_out', 
        'average_price', 
        'opening_inventory_unit', 
        'closing_inventory_unit', 
        'category', 
        'brand', 
        'flag', 
        'product_variation_id', 
        'clean_sheet_id', 
        'reconciliation_date',
        'offer_id',
        'created_at',
        'updated_at',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
    public function product_variations()
    {
        return $this->belongsTo(ProductVariation::class);
    }
    public function Lp()
    {
        return $this->belongsTo(Lp::class);
    }
}
