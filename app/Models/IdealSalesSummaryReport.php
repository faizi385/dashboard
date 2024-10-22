<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdealSalesSummaryReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'location',
        'sku',
        'product_description',
        'quantity_purchased',
        'purchase_amount',
        'return_quantity',
        'amount_return',
        'status',  
    ];
}
