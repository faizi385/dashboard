<?php

namespace App\Imports;

use App\Models\GlobalTillSalesSummaryReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class GlobalTillSalesSummaryReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;

    public function __construct($location, $reportId)
    {
        $this->location = $location; // Store location
        $this->reportId = $reportId; // Store report_id
    }

    public function model(array $row)
    {
        // You can adjust the column keys if needed (index-based or associative)
        return new GlobalTillSalesSummaryReport([
            'compliance_code' => $row['compliance_code'],
            'supplier_sku' => $row['supplier_sku'],
            'opening_inventory' => $row['opening_inventory'],
            'opening_inventory_value' => $row['opening_inventory_value'],
            'purchases_from_suppliers_additions' => $row['purchases_from_suppliers_additions'],
            'purchases_from_suppliers_value' => $row['purchases_from_suppliers_value'],
            'returns_from_customers_additions' => $row['returns_from_customers_additions'],
            'customer_returns_retail_value' => $row['customer_returns_retail_value'],
            'other_additions_additions' => $row['other_additions_additions'],
            'other_additions_value' => $row['other_additions_value'],
            'sales_reductions' => $row['sales_reductions'],
            'sold_retail_value' => $row['sold_retail_value'],
            'destruction_reductions' => $row['destruction_reductions'],
            'destruction_value' => $row['destruction_value'],
            'theft_reductions' => $row['theft_reductions'],
            'theft_value' => $row['theft_value'],
            'returns_to_suppliers_reductions' => $row['returns_to_suppliers_reductions'],
            'supplier_return_value' => $row['supplier_return_value'],
            'other_reductions_reductions' => $row['other_reductions_reductions'],
            'other_reductions_value' => $row['other_reductions_value'],
            'closing_inventory' => $row['closing_inventory'],
            'closing_inventory_value' => $row['closing_inventory_value'],
            'report_id' => $this->reportId,
            'location' => $this->location,
        ]);
    }
}
