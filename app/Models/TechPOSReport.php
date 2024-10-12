<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechPOSReport extends Model
{
    use HasFactory;

    protected $table = 'tech_pos_reports'; // Ensure this matches your table name
    protected $fillable = [
        'branchname', 'sku', 'productname', 'category', 'categoryparent', 'brand',
        'costperunit', 'openinventoryunits', 'openinventorycost', 'openinventoryvalue',
        'quantitypurchasedunits', 'quantitypurchasedcost', 'quantitypurchasedvalue',
        'quantitytransferinunits', 'quantitytransferincost', 'quantitytransferinvalue',
        'returnsfromcustomersunits', 'returnsfromcustomerscost', 'returnsfromcustomersvalue',
        'otheradditionsunits', 'otheradditionscost', 'otheradditionsvalue',
        'quantitysoldinstoreunits', 'quantitysoldinstorecost', 'quantitysoldinstorevalue',
        'quantitysoldonlineunits', 'quantitysoldonlinecost', 'quantitysoldonlinevalue',
        'quantitytransferoutunits', 'quantitytransferoutcost', 'quantitytransferoutvalue',
        'quantitydestroyedunits', 'quantitydestroyedcost', 'quantitydestroyedvalue',
        'quantitylosttheftunits', 'quantitylosttheftcost', 'quantitylosttheftvalue',
        'returnstodistributorunits', 'returnstodistributorcost', 'returnstodistributorvalue',
        'otherreductionsunits', 'otherreductionscost', 'otherreductionsvalue',
        'closinginventoryunits', 'closinginventorycost', 'closinginventoryvalue', 'report_id'
    ];
    // Define any relationships if needed
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
