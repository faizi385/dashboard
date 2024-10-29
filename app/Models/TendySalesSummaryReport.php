<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TendySalesSummaryReport extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'tendy_sales_summary_reports';

    protected $fillable = [
        'report_id',
        'location',
        'category',
        'compliance_type', // Added compliance_type
        'brand',
        'product',
        'variant', // Added variant
        'sku',
        'items_sold',
        'items_refunded',
        'net_qty_sold',
        'gross_sales',
        'net_sales',
        'total_discounts',
        'markdown',
        'reward_tiers',
        'total_tax',
        'cost_of_goods_sold',
        'gross_profit',
        'avg_retail_price',
        'gross_margin',
        'status',  
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
