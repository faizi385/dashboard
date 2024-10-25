<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdealDiagnosticReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'sku',
        'description',
        'opening',
        'purchases',
        'returns',
        'trans_in',
        'trans_out',
        'unit_sold',
        'write_offs',
        'closing',
        'net_sales_ex',
    ];
}