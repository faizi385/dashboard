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
        // Add other fields you want to allow mass assignment for
    ];
}
