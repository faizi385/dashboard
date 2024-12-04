<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GreenlineReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'barcode',
        'brand',
        'compliance_category',
        'opening',
        'sold',
        'purchased',
        'closing',
        'average_price',
        'average_cost',
      'status',  
        'report_id', 
        'retailer_id', 
        'lp_id',
        'date'
    ];
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
    public function retailer()
    {
        return $this->belongsTo(Retailer::class, 'retailer_id');
    }

public function lp()
{   
    return $this->belongsTo(Lp::class, 'lp_id');
}
}
