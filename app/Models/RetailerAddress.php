<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerAddress extends Model
{
    protected $fillable = [
        'retailer_id',
        'address',
        'street_no',
        'street_name',
        'province',
        'city',
        'location',
        'contact_person_name',
        'contact_person_phone',
        'postal_code', // Add postal_code here
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function retailerAddress()
    {
        return $this->hasOne(Carveout::class,'location','id');
    }

    public function getFullAddressAttribute()
    {
        return "{$this->street_no} {$this->street_name}, {$this->city}, {$this->province}, {$this->location}";
    }
   
    public function provinceDetails()
    {
        return $this->belongsTo(Province::class, 'province', 'id'); 
    }

}
