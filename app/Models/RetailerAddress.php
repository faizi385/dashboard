<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerAddress extends Model
{
    protected $fillable = [
        'retailer_id',
        'street_no',
        'street_name',
        'province',
        'city',
        'location',
        'contact_person_name',
        'contact_person_phone',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }
}
