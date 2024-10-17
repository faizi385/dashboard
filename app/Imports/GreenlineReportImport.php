<?php
namespace App\Imports;

use App\Models\GreenLineReport;
use App\Models\Report;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class GreenLineReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $errors = []; 
    protected $hasCheckedHeaders = false; 
    protected $requiredHeaders = ['sku', 'name', 'barcode', 'brand', 'compliance_category', 'opening', 'sold', 'purchased', 'closing', 'average_price', 'average_cost'];

    public function __construct($location)
    {
        $this->location = $location;
    }

    public function model(array $row)
    {
        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($this->requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Remove underscores from missing headers
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);

                // Log an error and store the message
                Log::error('Missing headers: ' . implode(', ', $formattedHeaders));
                $this->errors[] = 'Missing headers: ' . implode(', ', $formattedHeaders);
                $this->hasCheckedHeaders = true; // Set the flag to prevent further checks

                // Throw an exception to stop the import process
                throw new \Exception('Import failed. ' . implode(', ', $this->errors));
            }
        }

        // Fetch the report ID based on the location
        $report = Report::where('location', $this->location)->first();

        // Proceed with creating the model if headers are valid
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
            'report_id' => $report ? $report->id : null,
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
