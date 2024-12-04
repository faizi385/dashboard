<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'lp_id',
        'street_number',
        'street_name',
        'postal_code',
        'city',
        'province_id',
        'address'
    ];

    public function lp()
    {
        return $this->belongsTo(Lp::class, 'lp_id');
    }
    public function province()
{
    return $this->belongsTo(Province::class, 'province_id');
}
}
