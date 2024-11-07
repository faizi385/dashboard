<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\IdealDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\IdealSalesSummaryReport; // Adjust the model namespace if needed

class IdealSalesSummaryReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $diagnosticReportId; // New property for the diagnostic report ID
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    public function __construct($location, $reportId, $diagnosticReportId) // Add diagnosticReportId to constructor
    {
        $this->location = $location;
        $this->reportId = $reportId;
        $this->diagnosticReportId = $diagnosticReportId; // Set diagnosticReportId
    }

    public function model(array $row)
    {
        // Define the required headers
        $requiredHeaders = [
            'sku',
            'product_description',
            'quantity_purchased',
            'purchase_amount',
            'return_quantity',
            'amount_return',
        ];

        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                Log::error('Missing headers: ' . implode(', ', $missingHeaders));

                // Format headers to replace underscores with spaces
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header);
                }, $missingHeaders);

                $this->errors[] = 'Missing header: ' . implode(', ', $formattedHeaders);
                $this->hasCheckedHeaders = true;
                return null; // Stop processing this row
            }
        }

        // Retrieve the ideal diagnostic report
        $idealDiagnosticReport = IdealDiagnosticReport::where('sku', $row['sku'])
            ->where('report_id', $this->reportId)
            ->first();

        // If a matching diagnostic report exists and the row is valid
        if ($idealDiagnosticReport && !empty(array_filter($row))) {
            // Unset 'grand_total' if it exists
            unset($row['grand_total']);

            // Ensure SKU is valid
            if (!empty($row['sku']) && $row['sku'] !== '*') {
                return new IdealSalesSummaryReport([
                    'location' => $this->location,
                    'report_id' => $this->reportId,
                    'ideal_diagnostic_report_id' => $idealDiagnosticReport->id, // Correctly reference the ID
                    'sku' => $row['sku'],
                    'product_description' => $row['product_description'],
                    'quantity_purchased' => $row['quantity_purchased'],
                    'purchase_amount' => $row['purchase_amount'],
                    'return_quantity' => $row['return_quantity'],
                    'amount_return' => $row['amount_return'],
                ]);
            }
        }

        return null; // Return null if no conditions are met
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
