<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retailer extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'corporate_name',
        'dba',
        'user_id',
        // Address fields are not included here
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
// In Retailer.php model
public function address()
{
    return $this->hasMany(RetailerAddress::class, 'retailer_id');
}


}
