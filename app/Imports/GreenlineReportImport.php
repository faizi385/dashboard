<?php
namespace App\Imports;

use App\Models\GreenLineReport;
use App\Models\Report; // Add this line to access the reports table
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GreenLineReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $retailerId;
    protected $lpId;
    protected $errors = [];
    protected $hasCheckedHeaders = false;
    protected $requiredHeaders = [
        'sku', 'name', 'barcode', 'brand', 'compliance_category',
        'opening', 'sold', 'purchased', 'closing', 'average_price', 'average_cost'
    ];

    public function __construct($location, $reportId, $retailerId, $lpId = null)
    {
        $this->location = $location;
        $this->reportId = $reportId;
        $this->retailerId = $retailerId;
        $this->lpId = $lpId;
    }

    public function model(array $row)
    {
        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($this->requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header);
                }, $missingHeaders);

                Log::error('Missing headers: ' . implode(', ', $formattedHeaders));
                $this->errors[] = 'Missing headers: ' . implode(', ', $formattedHeaders);
                $this->hasCheckedHeaders = true;

                throw new \Exception('Import failed. ' . implode(', ', $this->errors));
            }
        }

        // Get the date from the reports table
        $report = Report::find($this->reportId);
        $reportDate = $report ? $report->date : null; // Get the date from the report or set null if not found

        // Proceed with creating the model
        if(!empty($row['sku']) || !empty($row['barcode']) || $row['name']){
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
                'report_id' => $this->reportId,
                'retailer_id' => $this->retailerId,
                'lp_id' => $this->lpId,
                'date' => $reportDate, // Store the date from the report
            ]);
        }
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
