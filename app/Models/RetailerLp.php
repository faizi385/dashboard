<?php

// app/Models/RetailerLp.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerLp extends Model
{
    use HasFactory;

    protected $table = 'retailer_lps';

    protected $fillable = [
        'retailer_id',
        'lp_id',
        'first_name',
        'last_name',
        'email',
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
