<?php

namespace App\Imports;

use App\Models\OtherPOSReport;
use App\Models\Report;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class OtherPOSReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $retailerId; // New property for retailer ID
    protected $lpId;
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    public function __construct($location, $reportId, $retailerId, $lpId = null)
    {
        $this->location = $location;
        $this->reportId = $reportId;
        $this->retailerId = $retailerId; // Assign retailer ID
        $this->lpId = $lpId;
    }

    public function model(array $row)
    {
        // List of required headers
        $requiredHeaders = ['sku', 'name', 'barcode', 'brand', 'compliance_category', 'opening', 'sold', 'purchased', 'closing', 'average_price', 'average_cost'];

        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Remove underscores from missing headers
                $formattedHeaders = array_map(function($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);

                // Log an error and store the message
                Log::error('Missing headers: ' . implode(', ', $formattedHeaders));
                $this->errors[] = 'Missing headers: ' . implode(', ', $formattedHeaders);
                $this->hasCheckedHeaders = true; // Set the flag to prevent further checks

                // Throw an exception for missing headers
                throw new \Exception('Missing headers: ' . implode(', ', $formattedHeaders));
            }
        }

        if(!empty($row['sku']) || !empty($row['barcode']) || $row['name']) {
            // Proceed with creating the model if headers are valid
            return new OtherPOSReport([
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
                'lp_id' => $this->lpId,
            ]);
        }
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }

    // Helper function to convert string to decimal
    private function convertToDecimal($value)
    {
        // Remove dollar signs and convert to float
        return floatval(str_replace('$', '', $value));
    }
}

