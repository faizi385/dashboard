<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Specify the fillable fields for mass assignment
    protected $fillable = [
        'retailer_id',
        'location',
        'pos',
        'status',         // Newly added
        'submitted_by',   // Newly added
        'file_1',         // Newly added
        'file_2',         // Newly added
    ];
}
