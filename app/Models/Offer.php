<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory,SoftDeletes; 

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
        'retailer_id',
        'province_slug',
        'source',
        'lp_name',
        'province_id'
    ];

    // Define the relationship with the Lp model
    public function lp()
    {
        return $this->belongsTo(Lp::class);
    }
    public function retailer() {
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }
    
}
