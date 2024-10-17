<?php

namespace App\Imports;

use App\Models\GlobalTillSalesSummaryReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Exception; // Ensure to import the Exception class

class GlobalTillSalesSummaryReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    public function __construct($location, $reportId)
    {
        $this->location = $location; // Store location
        $this->reportId = $reportId; // Store report_id
    }

    public function model(array $row)
    {
        // List of required headers
        $requiredHeaders = [
            'compliance_code',
            'supplier_sku',
            'opening_inventory',
            'opening_inventory_value',
            'purchases_from_suppliers_additions',
            'purchases_from_suppliers_value',
            'returns_from_customers_additions',
            'customer_returns_retail_value',
            'other_additions_additions',
            'other_additions_value',
            'sales_reductions',
            'sold_retail_value',
            'destruction_reductions',
            'destruction_value',
            'theft_reductions',
            'theft_value',
            'returns_to_suppliers_reductions',
            'supplier_return_value',
            'other_reductions_reductions',
            'other_reductions_value',
            'closing_inventory',
            'closing_inventory_value',
        ];

        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Log an error
                Log::error('Missing headers: ' . implode(', ', $missingHeaders));
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);
                // Throw an exception for missing headers
                throw new  \Exception('Missing header: ' . implode(', ', $formattedHeaders)); // Throwing the exception
            }
        }

        // Proceed with creating the model if headers are valid
        return new GlobalTillSalesSummaryReport([
            'compliance_code' => $row['compliance_code'] ?? null,
            'supplier_sku' => $row['supplier_sku'] ?? null,
            'opening_inventory' => $row['opening_inventory'] ?? null,
            'opening_inventory_value' => $row['opening_inventory_value'] ?? null,
            'purchases_from_suppliers_additions' => $row['purchases_from_suppliers_additions'] ?? null,
            'purchases_from_suppliers_value' => $row['purchases_from_suppliers_value'] ?? null,
            'returns_from_customers_additions' => $row['returns_from_customers_additions'] ?? null,
            'customer_returns_retail_value' => $row['customer_returns_retail_value'] ?? null,
            'other_additions_additions' => $row['other_additions_additions'] ?? null,
            'other_additions_value' => $row['other_additions_value'] ?? null,
            'sales_reductions' => $row['sales_reductions'] ?? null,
            'sold_retail_value' => $row['sold_retail_value'] ?? null,
            'destruction_reductions' => $row['destruction_reductions'] ?? null,
            'destruction_value' => $row['destruction_value'] ?? null,
            'theft_reductions' => $row['theft_reductions'] ?? null,
            'theft_value' => $row['theft_value'] ?? null,
            'returns_to_suppliers_reductions' => $row['returns_to_suppliers_reductions'] ?? null,
            'supplier_return_value' => $row['supplier_return_value'] ?? null,
            'other_reductions_reductions' => $row['other_reductions_reductions'] ?? null,
            'other_reductions_value' => $row['other_reductions_value'] ?? null,
            'closing_inventory' => $row['closing_inventory'] ?? null,
            'closing_inventory_value' => $row['closing_inventory_value'] ?? null,
            'report_id' => $this->reportId,
            'location' => $this->location,
        ]);
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
