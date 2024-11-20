<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carveout extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'retailer_id',
        'lp_id',
        'province_id',       // This will store the ID of the province
        'province_slug',     // This will store the slug of the province
        'province',
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

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
