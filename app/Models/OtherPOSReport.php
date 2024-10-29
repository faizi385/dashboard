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
        'report_id',          // Foreign key for the report
        'sku',                // SKU of the product
        'name',               // Name of the product
        'barcode',            // Barcode for the product
        'brand',              // Brand of the product
        'compliance_category', // Compliance category of the product
        'opening',            // Opening inventory
        'sold',               // Quantity sold
        'purchased',          // Quantity purchased
        'closing',            // Closing inventory
        'average_price',      // Average selling price
        'average_cost',       // Average cost of the product
        'status',             // Status field to track report status
    ];

    // If you do not want the created_at and updated_at columns to be managed automatically
    public $timestamps = true;

    /**
     * Define relationships, e.g., relationship with Retailer
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
