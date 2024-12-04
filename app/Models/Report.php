<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory,SoftDeletes;

    // Specify the fillable fields for mass assignment
    protected $fillable = [
        'retailer_id',
        'location',
        'pos',
        'status',         // Newly added
        'submitted_by',   // Newly added
        'file_1',         // Newly added
        'file_2',
        'province_id',
        'province',
        'province_slug',
        'lp_id',
        'address_id',
        'date'
    ];
    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

}
