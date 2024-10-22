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
        'report_id',  // Include report_id in the fillable fields
    ];
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
