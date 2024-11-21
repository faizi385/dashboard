<?php
namespace App\Imports;

use App\Models\TechPOSReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class TechPOSReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $errors = []; // To store missing header errors
    protected $retailerId; // New property for retailer ID
    protected $lpId;
    public function __construct($location, $reportId, $retailerId, $lpId = null)
    {
        $this->location = $location;
        $this->reportId = $reportId;
        $this->retailerId = $retailerId; // Assign retailer ID
        $this->lpId = $lpId;
    }

    public function model(array $row)
    {


       //'branchname',
        $requiredHeaders = [
            'sku', 'productname', 'category', 'categoryparent', 'brand',
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
            'closinginventoryunits', 'closinginventorycost', 'closinginventoryvalue'
        ];

        // Check for missing headers
        $missingHeaders = array_diff($requiredHeaders, array_keys($row));
        if (!empty($missingHeaders)) {
            // Format missing headers for logging and user feedback
            $formattedHeaders = array_map(function ($header) {
                return str_replace('_', ' ', $header); // Replace underscores with spaces
            }, $missingHeaders);

            // Log missing headers
            Log::error('Missing headers: ' . implode(', ', $formattedHeaders));

            // Throw an exception with a formatted error message
            throw new \Exception('Missing headers: ' . implode(', ', $formattedHeaders));
        }

        if(!empty($row['sku']) || !empty($row['productname'])) {
            // If no headers are missing, proceed with creating the model
            return new TechPOSReport([
                // 'branchname' => $row['branchname'],
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
                'retailer_id' => $this->retailerId, // Include retailer ID
                'lp_id' => $this->lpId,
            ]);
        }
    }

    // Return any collected errors
    public function getErrors()
    {
        return $this->errors;
    }
}
