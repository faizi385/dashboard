<?php

// app/Models/CarveoutLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarveoutLog extends Model
{
    use HasFactory;

    protected $fillable = ['carveout_id', 'user_id', 'action', 'description'];

    // Relationship with User
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relationship with Carveout
    public function carveout() {
        return $this->belongsTo(Carveout::class);
    }
}
