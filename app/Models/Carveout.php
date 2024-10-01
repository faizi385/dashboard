<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carveout extends Model
{
    use HasFactory;

    protected $fillable = [
        'retailer_id',
        'lp_id',
        'dba',
        'address',
        'carveout',
        'location',
        'sku',
        'date',
        'licence_producer',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function lp()
    {
        return $this->belongsTo(Lp::class);
    }
}
