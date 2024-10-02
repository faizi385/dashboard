<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 'provincial_sku', 'gtin', 'province', 
        'category', 'brand', 'lp_id', 'product_size', 
        'thc_range', 'cbd_range', 'comment', 'product_link'
    ];
}
