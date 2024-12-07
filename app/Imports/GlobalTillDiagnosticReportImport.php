<?php

namespace App\Imports;

use Exception;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\GlobalTillDiagnosticReport;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GlobalTillDiagnosticReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $retailerId;
    protected $lpId;
    protected $errors = [];
    protected $hasCheckedHeaders = false;
    protected $diagnosticReportId;

    public function __construct($location, $reportId, $retailerId, $lpId = null)
    {
        $this->location = $location;
        $this->reportId = $reportId;
        $this->retailerId = $retailerId;
        $this->lpId = $lpId;
    }

    /**
     * Clean the value by checking for formulas or invalid numeric values.
     */
    private function cleanNumericValue($value)
    {
        if (is_string($value) && strpos($value, '=') === 0) {
            return null;
        }
        return is_numeric($value) ? $value : null;
    }

    public function model(array $row)
    {
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
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                Log::error('Missing headers: ' . implode(', ', $missingHeaders));
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header);
                }, $missingHeaders);
                throw new \Exception('Missing headers: ' . implode(', ', $formattedHeaders));
            }
            $this->hasCheckedHeaders = true;
        }
        $report = Report::find($this->reportId);
        $reportDate = $report ? $report->date : null;
        if(!empty($row['supplier_sku']) || !empty($row['compliance_code']) || $row['product']){
            $diagnosticReport = new GlobalTillDiagnosticReport([
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
                'retailer_id' => $this->retailerId,
                'lp_id' => $this->lpId,
                'date' => $reportDate,
            ]);

            $diagnosticReport->save();
            $this->diagnosticReportId = $diagnosticReport->id;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}



