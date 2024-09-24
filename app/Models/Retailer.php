<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'corporate_name',
        'dba',
        // Address fields are not included here
    ];

    // In Retailer.php model
// In Retailer.php model
public function address()
{
    return $this->hasMany(RetailerAddress::class, 'retailer_id');
}


}