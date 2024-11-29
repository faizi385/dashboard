<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleanSheet extends Model
{
    use HasFactory;

    // Specify the table if it differs from the plural form of the model name
    protected $table = 'clean_sheets';

    // Define the fillable fields to allow mass assignment
    protected $fillable = [
        'retailer_id',
        'lp_id',
        'report_id',
        'retailer_name',
        'lp_name',
        'thc_range',
        'cbd_range',
        'size_in_gram',
        'location',
        'province_id',
        'province_name',
        'province_slug',
        'sku',
        'product_name',
        'category',
        'brand',
        'sold',
        'purchase',
        'average_price',
        'average_cost',
        'report_price_og',
        'barcode',
        'transfer_in',
        'transfer_out',
        'pos',
        'reconciliation_date',
        'dqi_flag',
        'flag',
        'comment',
        'opening_inventory_unit',
        'closing_inventory_unit',
        'c_flag',
        'dqi_fee',
        'dqi_per',
        'offer_id',
        'pos_report_id',
        'product_variation_id',
        'carveout_id'
    ];

    // Optional: define relationships if needed
    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function lp()
    {
        return $this->belongsTo(LP::class); // LP stands for Licensed Producer
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function product_variations()
    {
        return $this->belongsTo(ProductVariation::class);
    }
   
}
