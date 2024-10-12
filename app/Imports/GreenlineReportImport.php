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

    public function __construct($location)
    {
        $this->location = $location;
    }

    public function model(array $row)
    {
        // List of required headers
        $requiredHeaders = ['sku', 'name', 'barcode', 'brand', 'compliance_category', 'opening', 'sold', 'purchased', 'closing', 'average_price', 'average_cost'];

        // Check if required headers are missing
        $missingHeaders = array_diff($requiredHeaders, array_keys($row));
        if (!empty($missingHeaders)) {
            // Log an error or throw an exception
            Log::error('Missing headers: ' . implode(', ', $missingHeaders));
            return null; // Stop processing this row
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

    // Helper function to convert string to decimal
    private function convertToDecimal($value)
    {
        // Remove dollar signs and convert to float
        return floatval(str_replace('$', '', $value));
    }
}
