<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferLog extends Model
{
    // Add offer_id to fillable array to allow mass assignment
    protected $fillable = [
        'offer_id',
        'user_id', // Ensure this is included as well
        'action',
        'description',
    ];

    // Define relationships if necessary
   // In OfferLog.php (your model)
public function user()
{
    return $this->belongsTo(User::class);
}


    public function offer()
    {
        return $this->belongsTo(Offer::class); // Adjust based on your offer model
    }
}
