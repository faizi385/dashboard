<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'action',
        'description',
    ];

    // Define the relationship with the Report model
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class); // Ensure this points to the correct User model namespace
    }
    public function retailer()
    {
        return $this->hasOneThrough(Retailer::class, Report::class, 'id', 'id', 'report_id', 'retailer_id');
    }
}
