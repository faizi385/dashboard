<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\GlobalTillDiagnosticReport;
use App\Models\GlobalTillSalesSummaryReport;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Exception; // Ensure to import the Exception class

class GlobalTillSalesSummaryReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
 // Added gb_diagnostic_report_id
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    public function __construct($location, $reportId)
    {
        $this->location = $location; // Store location
        $this->reportId = $reportId; // Store report_id
   // Store gb_diagnostic_report_id
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
                throw new Exception('Missing header: ' . implode(', ', $formattedHeaders)); // Throwing the exception
            }
        }

        // Find the corresponding diagnostic report
        $gobatellDiagnosticReport = GlobalTillDiagnosticReport::where('supplier_sku', $row['supplier_sku'])
            ->where('report_id', $this->reportId)
            ->first();

        if ($gobatellDiagnosticReport) {
            if (!empty(array_filter($row))) {
                if (isset($row['grand_total'])) {
                    unset($row['grand_total']);
                }

                if ($row['supplier_sku'] != null || $row['compliance_code'] != null) {
                    if ($row['supplier_sku'] != '*') {
                        return new GlobalTillSalesSummaryReport([
                            'compliance_code' => $row['compliance_code'] ?? null,
                            'supplier_sku' => $row['supplier_sku'] ?? null,
                            'opening_inventory' => $this->cleanNumericValue($row['opening_inventory']),
                            'opening_inventory_value' => $this->cleanNumericValue($row['opening_inventory_value']),
                            'purchases_from_suppliers_additions' => $this->cleanNumericValue($row['purchases_from_suppliers_additions']),
                            'purchases_from_suppliers_value' => $this->cleanNumericValue($row['purchases_from_suppliers_value']),
                            'returns_from_customers_additions' => $this->cleanNumericValue($row['returns_from_customers_additions']),
                            'customer_returns_retail_value' => $this->cleanNumericValue($row['customer_returns_retail_value']),
                            'other_additions_additions' => $this->cleanNumericValue($row['other_additions_additions']),
                            'other_additions_value' => $this->cleanNumericValue($row['other_additions_value']),
                            'sales_reductions' => $this->cleanNumericValue($row['sales_reductions']),
                            'sold_retail_value' => $this->cleanNumericValue($row['sold_retail_value']),
                            'destruction_reductions' => $this->cleanNumericValue($row['destruction_reductions']),
                            'destruction_value' => $this->cleanNumericValue($row['destruction_value']),
                            'theft_reductions' => $this->cleanNumericValue($row['theft_reductions']),
                            'theft_value' => $this->cleanNumericValue($row['theft_value']),
                            'returns_to_suppliers_reductions' => $this->cleanNumericValue($row['returns_to_suppliers_reductions']),
                            'supplier_return_value' => $this->cleanNumericValue($row['supplier_return_value']),
                            'other_reductions_reductions' => $this->cleanNumericValue($row['other_reductions_reductions']),
                            'other_reductions_value' => $this->cleanNumericValue($row['other_reductions_value']),
                            'closing_inventory' => $this->cleanNumericValue($row['closing_inventory']),
                            'closing_inventory_value' => $this->cleanNumericValue($row['closing_inventory_value']),
                            'report_id' => $this->reportId,
                            'location' => $this->location,
                            'gb_diagnostic_report_id' =>    $gobatellDiagnosticReport->id// Adding gb_diagnostic_report_id
                        ]);
                    }
                }
            }
        }

        return null; // Return null if no record is created
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
