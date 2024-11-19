<?php
namespace App\Imports;

use App\Models\GreenLineReport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GreenLineReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $retailerId; // New property for retailer ID
    protected $lpId;      // New property for LP ID (optional)
    protected $errors = []; 
    protected $hasCheckedHeaders = false; 
    protected $requiredHeaders = [
        'sku', 'name', 'barcode', 'brand', 'compliance_category',
        'opening', 'sold', 'purchased', 'closing', 'average_price', 'average_cost'
    ];

    public function __construct($location, $reportId, $retailerId, $lpId = null)
    {
        $this->location = $location;
        $this->reportId = $reportId;
        $this->retailerId = $retailerId; // Assign retailer ID
        $this->lpId = $lpId;             // Assign LP ID if provided
    }

    public function model(array $row)
    {
        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($this->requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Format and log missing headers
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);

                Log::error('Missing headers: ' . implode(', ', $formattedHeaders));
                $this->errors[] = 'Missing headers: ' . implode(', ', $formattedHeaders);
                $this->hasCheckedHeaders = true; // Prevent further checks

                // Stop import with an exception
                throw new \Exception('Import failed. ' . implode(', ', $this->errors));
            }
        }

        // Proceed with creating the model
        return new GreenLineReport([
            'sku' => $row['sku'] ?? null,
            'name' => $row['name'] ?? null,
            'barcode' => $row['barcode'] ?? null,
            'brand' => $row['brand'] ?? null,
            'compliance_category' => $row['compliance_category'] ?? null,
            'opening' => $row['opening'] ?? null,
            'sold' => $row['sold'] ?? null,
            'purchased' => $row['purchased'] ?? null,
            'closing' => $row['closing'] ?? null,
            'average_price' => $this->convertToDecimal($row['average_price'] ?? null),
            'average_cost' => $this->convertToDecimal($row['average_cost'] ?? null),
            'report_id' => $this->reportId,
            'retailer_id' => $this->retailerId, // Include retailer ID
            'lp_id' => $this->lpId,             // Include LP ID if provided
        ]);
    }

    public function getErrors()
    {
        return $this->errors; 
    }

    private function convertToDecimal($value)
    {
        return floatval(str_replace('$', '', $value));
    }
}
