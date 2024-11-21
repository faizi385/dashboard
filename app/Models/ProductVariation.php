<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $table = 'product_variations';

    protected $fillable = [
        'product_name',
        'provincial_sku',
        'gtin',
        'province',
        'category',
        'brand',
        'lp_id',
        'product_size',
        'thc_range',
        'cbd_range',
        'comment',
        'product_link',
        'price_per_unit',
        'province_id',
        'product_id'
    ];

    // Define relationship to LP model
    public function lp()
    {
        return $this->belongsTo(Lp::class, 'lp_id');
    }

    // Define relationship to CleanSheet model, if applicable
    public function cleanSheets()
    {
        return $this->hasMany(CleanSheet::class, 'product_variation_id');
    }

    // Define relationship to Product model, if applicable
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id'); // Adjust foreign key if necessary
    }
    
    // Additional example: A retailer relationship if ProductVariation is linked to Retailer
    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'retailer_id'); // Adjust as necessary
    }
}
