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
        'ideal_diagnostic_report_id',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id'); // Adjust 'report_id' if your foreign key is named differently
    }
}



