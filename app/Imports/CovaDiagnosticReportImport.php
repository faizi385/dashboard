<?php


namespace App\Imports;

use App\Models\CovaDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class CovaDiagnosticReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    public function __construct($location, $reportId)
    {
        $this->location = $location;
        $this->reportId = $reportId;
    }

    public function model(array $row)
    {
        // List of required headers
        $requiredHeaders = [
            'product_name', 
            'type', 
            'aglc_sku', 
            'new_brunswick_sku', 
            'ocs_sku', 
            'ylc_sku', 
            'manitoba_barcodeupc', 
            'ontario_barcodeupc', 
            'saskatchewan_barcodeupc', 
            'link_to_product', 
            'opening_inventory_units', 
            'quantity_purchased_units', 
            'reductions_receiving_error_units', 
            'returns_from_customers_units', 
            'other_additions_units', 
            'quantity_sold_units', 
            'quantity_destroyed_units', 
            'quantity_lost_theft_units', 
            'returns_to_supplier_units', 
            'other_reductions_units', 
            'closing_inventory_units'
        ];

        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Format the missing headers for better readability (replace underscores with spaces)
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Format headers for display
                }, $missingHeaders);
        
                // Log the error with the raw header names
                Log::error('Missing headers in CovaDiagnosticReport import: ' . implode(', ', $missingHeaders));
        
                // Store the formatted headers in the errors array
                $this->errors[] = 'Missing header: ' . implode(', ', $formattedHeaders);
        
                // Throw an exception with the collected errors
                throw new \Exception('' . implode(', ', $this->errors));
        
                // Set the flag to true so we don't re-check headers on every row
                $this->hasCheckedHeaders = true;
                return null; // Stop processing this row
            }
        }
        

        // Proceed with creating the model if headers are valid
        return new CovaDiagnosticReport([
            'product_name' => $row['product_name'] ?? null,
            'type' => $row['type'] ?? null,
            'aglc_sku' => $row['aglc_sku'] ?? null,
            'new_brunswick_sku' => $row['new_brunswick_sku'] ?? null,
            'ocs_sku' => $row['ocs_sku'] ?? null,
            'ylc_sku' => $row['ylc_sku'] ?? null,
            'manitoba_barcodeupc' => $row['manitoba_barcodeupc'] ?? null,
            'ontario_barcodeupc' => $row['ontario_barcodeupc'] ?? null,
            'saskatchewan_barcodeupc' => $row['saskatchewan_barcodeupc'] ?? null,
            'link_to_product' => $row['link_to_product'] ?? null,
            'opening_inventory_units' => $row['opening_inventory_units'] ?? null,
            'quantity_purchased_units' => $row['quantity_purchased_units'] ?? null,
            'reductions_receiving_error_units' => $row['reductions_receiving_error_units'] ?? null,
            'returns_from_customers_units' => $row['returns_from_customers_units'] ?? null,
            'other_additions_units' => $row['other_additions_units'] ?? null,
            'quantity_sold_units' => $row['quantity_sold_units'] ?? null,
            'quantity_destroyed_units' => $row['quantity_destroyed_units'] ?? null,
            'quantity_lost_theft_units' => $row['quantity_lost_theft_units'] ?? null,
            'returns_to_supplier_units' => $row['returns_to_supplier_units'] ?? null,
            'other_reductions_units' => $row['other_reductions_units'] ?? null,
            'closing_inventory_units' => $row['closing_inventory_units'] ?? null,
            'report_id' => $this->reportId,
            'location' => $this->location,
        ]);
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
