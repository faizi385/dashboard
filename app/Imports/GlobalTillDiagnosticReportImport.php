<?php

namespace App\Imports;

use App\Models\GlobalTillDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Exception;

class GlobalTillDiagnosticReportImport implements ToModel, WithHeadingRow
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

    /**
     * Clean the value by checking for formulas or invalid numeric values.
     */
    private function cleanNumericValue($value)
    {
        // If the value starts with a formula (=), return null
        if (is_string($value) && strpos($value, '=') === 0) {
            return null;
        }

        // Return the numeric value if valid, otherwise return null
        return is_numeric($value) ? $value : null;
    }

    public function model(array $row)
    {
        // List of required headers
        $requiredHeaders = [
            'storelocation',
            'store_sku',
            'product',
            'compliance_code',
            'supplier_sku',
            'pos_equivalent_grams',
            'compliance_weight',
            'opening_inventory',
            'purchases_from_suppliers_additions',
            'returns_from_customers_additions',
            'other_additions_additions',
            'sales_reductions',
            'destruction_reductions',
            'theft_reductions',
            'returns_to_suppliers_reductions',
            'other_reductions_reductions',
            'closing_inventory',
            'product_url',
            'inventory_transactions_url',
        ];

        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Log an error
                Log::error('Missing headers: ' . implode(', ', $missingHeaders));

                // Format headers to replace underscores with spaces
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);

                // Throw an exception for missing headers
                throw new  \Exception('Missing headers' . implode(', ', $formattedHeaders)); 
            }
        }

        // Proceed with creating the model if headers are valid
        return new GlobalTillDiagnosticReport([
            'report_id' => $this->reportId,
            'storelocation' => $row['storelocation'] ?? null,
            'store_sku' => $row['store_sku'] ?? null,
            'product' => $row['product'] ?? null,
            'compliance_code' => $row['compliance_code'] ?? null,
            'supplier_sku' => $row['supplier_sku'] ?? null,
            'pos_equivalent_grams' => $this->cleanNumericValue($row['pos_equivalent_grams']),
            'compliance_weight' => $this->cleanNumericValue($row['compliance_weight']),
            'opening_inventory' => $this->cleanNumericValue($row['opening_inventory']),
            'purchases_from_suppliers_additions' => $this->cleanNumericValue($row['purchases_from_suppliers_additions']),
            'returns_from_customers_additions' => $this->cleanNumericValue($row['returns_from_customers_additions']),
            'other_additions_additions' => $this->cleanNumericValue($row['other_additions_additions']),
            'sales_reductions' => $this->cleanNumericValue($row['sales_reductions']),
            'destruction_reductions' => $this->cleanNumericValue($row['destruction_reductions']),
            'theft_reductions' => $this->cleanNumericValue($row['theft_reductions']),
            'returns_to_suppliers_reductions' => $this->cleanNumericValue($row['returns_to_suppliers_reductions']),
            'other_reductions_reductions' => $this->cleanNumericValue($row['other_reductions_reductions']),
            'closing_inventory' => $this->cleanNumericValue($row['closing_inventory']),
            'product_url' => $row['product_url'] ?? null,
            'inventory_transactions_url' => $row['inventory_transactions_url'] ?? null,
        ]);
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}