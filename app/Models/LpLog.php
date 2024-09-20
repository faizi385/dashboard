<?php
// app/Models/LpLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lp_id', 'user_id', 'dba', 'time', 'action', 'description'
    ];
    protected $casts = [
        'time' => 'datetime', // Cast 'time' to Carbon instance
    ];
// In LpLog.php


public function user()
{
    return $this->belongsTo(User::class);
}
    public function lp()
    {
        return $this->belongsTo(Lp::class);
    }
}
