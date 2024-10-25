<?php

namespace App\Imports;

use App\Models\IdealSalesSummaryReport; // Adjust the model namespace if needed
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class IdealSalesSummaryReportImport implements ToModel, WithHeadingRow
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
                // Log an error and store the message
                Log::error('Missing headers: ' . implode(', ', $missingHeaders));

                // Format headers to replace underscores with spaces
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);

                $this->errors[] = 'Missing header: ' . implode(', ', $formattedHeaders); // Use formatted headers
                $this->hasCheckedHeaders = true; // Set the flag to prevent further checks
                return null; // Stop processing this row
            }
        }

        // Proceed with creating the model if headers are valid
        return new IdealSalesSummaryReport([
            'location' => $this->location,
            'report_id' => $this->reportId,
            'sku' => $row['sku'],
            'product_description' => $row['product_description'],
            'quantity_purchased' => $row['quantity_purchased'],
            'purchase_amount' => $row['purchase_amount'],
            'return_quantity' => $row['return_quantity'],
            'amount_return' => $row['amount_return'],
        ]);
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
