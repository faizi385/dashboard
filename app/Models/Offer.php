<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    // Define fillable properties for mass assignment
    protected $fillable = [
        'offer_name',
        'product_name',
        'provincial_sku',
        'gtin',
        'province',
        'data_fee',
        'unit_cost',
        'category',
        'brand',
        'case_quantity',
        'offer_start',
        'offer_end',
        'product_size',
        'thc_range',
        'cbd_range',
        'comment',
        'product_link',
        'lp_id',
        'offer_date', 
        'retailer_id',// Include this field in fillable
    ];

    // Define the relationship with the Lp model
    public function lp()
    {
        return $this->belongsTo(Lp::class);
    }
}
