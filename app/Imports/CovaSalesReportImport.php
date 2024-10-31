<?php

namespace App\Imports;

use App\Models\CovaSalesReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class CovaSalesReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $covaDiagnosticReportId; // Add this line to declare the variable
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    public function __construct($location, $reportId, $covaDiagnosticReportId)
    {
        $this->location = $location;
        $this->reportId = $reportId;
        $this->covaDiagnosticReportId = $covaDiagnosticReportId;
    }

    public function model(array $row)
    {
        // List of required headers
        $requiredHeaders = [
            'product', 
            'sku', 
            'classification', 
            'items_sold', 
            'items_ref', 
            'net_sold', 
            'gross_sales', 
            'subtotal', 
            'total_cost', 
            'gross_profit', 
            'gross_margin', 
            'total_discount', 
            'markdown_percent', 
            'avg_regular_price', 
            'avg_sold_at_price', 
            'unit_type', 
            'net_weight', 
            'total_net_weight', 
            'brand', 
            'supplier', 
            'supplier_skus', 
            'total_tax', 
            'hst_13'
        ];

        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Format headers to replace underscores with spaces
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);

                // Log an error and store the message
                Log::error('Missing headers: ' . implode(', ', $missingHeaders));

                // Add the error message to the errors array
                $this->errors[] = 'Missing header: ' . implode(', ', $formattedHeaders);

                // Throw an exception with the collected errors
                throw new \Exception('' . implode(', ', $this->errors));

                // Set the flag to prevent further header checks
                $this->hasCheckedHeaders = true;
                return null; // Stop processing this row
            } 
        }

        // Proceed with creating the model if headers are valid
        return new CovaSalesReport([
            'report_id' => $this->reportId,
            'cova_diagnostic_report_id' => $this->covaDiagnosticReportId, // Add this line to save the diagnostic report ID
            'product' => $row['product'] ?? null,
            'sku' => $row['sku'] ?? null,
            'classification' => $row['classification'] ?? null,
            'items_sold' => $row['items_sold'] ?? null,
            'items_ref' => $row['items_ref'] ?? null,
            'net_sold' => $row['net_sold'] ?? null,
            'gross_sales' => $row['gross_sales'] ?? null,
            'subtotal' => $row['subtotal'] ?? null,
            'total_cost' => $row['total_cost'] ?? null,
            'gross_profit' => $row['gross_profit'] ?? null,
            'gross_margin' => $row['gross_margin'] ?? null,
            'total_discount' => $row['total_discount'] ?? null,
            'markdown_percent' => $row['markdown_percent'] ?? null,
            'avg_regular_price' => $row['avg_regular_price'] ?? null,
            'avg_sold_at_price' => $row['avg_sold_at_price'] ?? null,
            'unit_type' => $row['unit_type'] ?? null,
            'net_weight' => $row['net_weight'] ?? null,
            'total_net_weight' => $row['total_net_weight'] ?? null,
            'brand' => $row['brand'] ?? null,
            'supplier' => $row['supplier'] ?? null,
            'supplier_skus' => $row['supplier_skus'] ?? null,
            'total_tax' => $row['total_tax'] ?? null,
            'hst_13' => $row['hst_13'] ?? null,
        ]);
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
