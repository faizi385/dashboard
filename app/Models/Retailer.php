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
        'status',
        'postal_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->hasMany(RetailerAddress::class, 'retailer_id');
    }

    // Override the boot method to handle email modification on deletion
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($retailer) {
            // Modify the email before deletion
            $originalEmail = $retailer->email;
            $retailer->email = 'deleted_' . time() . '_' . $originalEmail; // Append a timestamp to avoid conflicts
            $retailer->save();
        });
    }
}
