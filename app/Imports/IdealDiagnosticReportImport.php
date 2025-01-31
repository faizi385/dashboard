<?php

namespace App\Imports;

use App\Models\Report;
use Illuminate\Support\Facades\Log;
use App\Models\IdealDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IdealDiagnosticReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $retailerId; // New property for retailer ID
    protected $lpId;
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated
    protected $diagnosticReportId; // Stores the last imported diagnostic report ID

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
        $requiredHeaders = [
            'sku',
            'description',
            'opening',
            'purchases',
            'returns',
            'trans_in',
            'trans_out',
            'unit_sold',
            'write_offs',
            'closing',
            'net_sales_ex',
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
        $report = Report::find($this->reportId);
        $reportDate = $report ? $report->date : null; 
        if(!empty($row['sku']) || !empty($row['description'])) {
            // Create the diagnostic report and save it to the database
            $diagnosticReport = IdealDiagnosticReport::create([
                'report_id' => $this->reportId,
                'sku' => $row['sku'],
                'description' => $row['description'],
                'opening' => $row['opening'],
                'purchases' => $row['purchases'],
                'returns' => $row['returns'],
                'trans_in' => $row['trans_in'],
                'trans_out' => $row['trans_out'],
                'unit_sold' => $row['unit_sold'],
                'write_offs' => $row['write_offs'],
                'closing' => $row['closing'],
                'net_sales_ex' => $row['net_sales_ex'],
                'retailer_id' => $this->retailerId, // Include retailer ID
                'lp_id' => $this->lpId,
                'date' => $reportDate, // Add report date to the model
            ]);

            // Store the ID of the last inserted diagnostic report
            $this->diagnosticReportId = $diagnosticReport->id;
        }
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }

    public function getId()
    {
        return $this->diagnosticReportId; // Return the ID of the last imported diagnostic report
    }
}


