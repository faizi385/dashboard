<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name', 'slug', 'timezone_1', 'timezone_2', 'tax_value', 'status'
    ];
}
