<?php

namespace App\Imports;

use App\Models\GlobalTillDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GlobalTillDiagnosticReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;

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
        return new GlobalTillDiagnosticReport([
            'report_id' => $this->reportId,
            'storelocation' => $row['storelocation'],
            'store_sku' => $row['store_sku'],
            'product' => $row['product'],
            'compliance_code' => $row['compliance_code'],
            'supplier_sku' => $row['supplier_sku'],
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
            'product_url' => $row['product_url'],
            'inventory_transactions_url' => $row['inventory_transactions_url'],
        ]);
    }
}
