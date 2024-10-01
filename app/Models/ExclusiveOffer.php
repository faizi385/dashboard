<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExclusiveOffer extends Model
{
    protected $fillable = [
        'retailer_id',
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
        'product_link',
        'comment',
    ];
}
