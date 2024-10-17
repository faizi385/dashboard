<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherPOSReport extends Model
{
    use HasFactory;
    protected $table = 'other_pos_reports';
    // Specify the fields that can be mass-assigned
    protected $fillable = [
        'report_id',
        'location',
        'pos',
        'date',
        'status',
        'submitted_by',
        'file_1',
        'file_2',
    ];

    // If you do not want the created_at and updated_at columns to be managed automatically
    public $timestamps = true;

    /**
     * Define relationships, e.g. relationship with Retailer
     */
    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
