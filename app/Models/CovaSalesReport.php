<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CovaSalesReport extends Model
{
    use HasFactory;

    // Specify the fillable fields
    protected $fillable = [
        'report_id',
        'product',
        'sku',
        'classification',
        'items_sold',
        'items_ref',
        'net_sold',
        'gross_sales',
        'subtotal',
        'total_cost',
        'gross_profit',
        'gross_margin',
        'total_discount',
        'markdown_percent',
        'avg_regular_price',
        'avg_sold_at_price',
        'unit_type',
        'net_weight',
        'total_net_weight',
        'brand',
        'supplier',
        'supplier_skus',
        'total_tax',
        'hst_13',
        'status',  
        'cova_diagnostic_report_id',
    ];
}
