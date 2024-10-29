<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    // Specify the table name if it does not follow Laravel's naming convention
    protected $table = 'product_variations';

    // Define fillable attributes to allow mass assignment
    protected $fillable = [
        'product_name',
        'provincial_sku',
        'gtin',
        'province',
        'category',
        'brand',
        'lp_id',
        'product_size',
        'thc_range',
        'cbd_range',
        'comment',
        'product_link',
        'price_per_unit'
    ];

    public function lp()
    {
        return $this->belongsTo(Lp::class, 'lp_id'); // Adjust the foreign key if it's different
    }
    
}
