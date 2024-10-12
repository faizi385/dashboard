<?php

namespace App\Imports;

use App\Models\TechPOSReport; // Change to your actual model
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TechPOSReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;

    public function __construct($location, $reportId)
    {
        $this->location = $location;
        $this->reportId = $reportId;
    }

    public function model(array $row)
    {
    
        return new TechPOSReport([
            'branchname' => $row['branchname'],
            'sku' => $row['sku'],
            'productname' => $row['productname'],
            'category' => $row['category'],
            'categoryparent' => $row['categoryparent'],
            'brand' => $row['brand'],
            'costperunit' => $row['costperunit'],
            'openinventoryunits' => $row['openinventoryunits'],
            'openinventorycost' => $row['openinventorycost'],
            'openinventoryvalue' => $row['openinventoryvalue'],
            'quantitypurchasedunits' => $row['quantitypurchasedunits'],
            'quantitypurchasedcost' => $row['quantitypurchasedcost'],
            'quantitypurchasedvalue' => $row['quantitypurchasedvalue'],
            'quantitytransferinunits' => $row['quantitytransferinunits'],
            'quantitytransferincost' => $row['quantitytransferincost'],
            'quantitytransferinvalue' => $row['quantitytransferinvalue'],
            'returnsfromcustomersunits' => $row['returnsfromcustomersunits'],
            'returnsfromcustomerscost' => $row['returnsfromcustomerscost'],
            'returnsfromcustomersvalue' => $row['returnsfromcustomersvalue'],
            'otheradditionsunits' => $row['otheradditionsunits'],
            'otheradditionscost' => $row['otheradditionscost'],
            'otheradditionsvalue' => $row['otheradditionsvalue'],
            'quantitysoldinstoreunits' => $row['quantitysoldinstoreunits'],
            'quantitysoldinstorecost' => $row['quantitysoldinstorecost'],
            'quantitysoldinstorevalue' => $row['quantitysoldinstorevalue'],
            'quantitysoldonlineunits' => $row['quantitysoldonlineunits'],
            'quantitysoldonlinecost' => $row['quantitysoldonlinecost'],
            'quantitysoldonlinevalue' => $row['quantitysoldonlinevalue'],
            'quantitytransferoutunits' => $row['quantitytransferoutunits'],
            'quantitytransferoutcost' => $row['quantitytransferoutcost'],
            'quantitytransferoutvalue' => $row['quantitytransferoutvalue'],
            'quantitydestroyedunits' => $row['quantitydestroyedunits'],
            'quantitydestroyedcost' => $row['quantitydestroyedcost'],
            'quantitydestroyedvalue' => $row['quantitydestroyedvalue'],
            'quantitylosttheftunits' => $row['quantitylosttheftunits'],
            'quantitylosttheftcost' => $row['quantitylosttheftcost'],
            'quantitylosttheftvalue' => $row['quantitylosttheftvalue'],
            'returnstodistributorunits' => $row['returnstodistributorunits'],
            'returnstodistributorcost' => $row['returnstodistributorcost'],
            'returnstodistributorvalue' => $row['returnstodistributorvalue'],
            'otherreductionsunits' => $row['otherreductionsunits'],
            'otherreductionscost' => $row['otherreductionscost'],
            'otherreductionsvalue' => $row['otherreductionsvalue'],
            'closinginventoryunits' => $row['closinginventoryunits'],
            'closinginventorycost' => $row['closinginventorycost'],
            'closinginventoryvalue' => $row['closinginventoryvalue'],
            'report_id' => $this->reportId,
            'location' => $this->location,
        ]);
    }
}
