<?php

namespace App\Imports;

use App\Models\TendyDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TendyDiagnosticReportImport implements ToModel, WithHeadingRow
{
    protected $reportId;
    protected $location;

    public function __construct($reportId, $location)
    {
        $this->reportId = $reportId;
        $this->location = $location;
    }

    public function model(array $row)
    {
        // Skip rows that do not have the required data
        if (empty($row) || !isset($row['product_sku'])) {
            return null; 
        }

        return new TendyDiagnosticReport([
            'report_id' => $this->reportId,
            'location' => $this->location, // Add the location here
            'product_sku' => $row['product_sku'],
            'opening_inventory_units' => $row['opening_inventory_units'],
            'opening_inventory_value' => $row['opening_inventory_value'],
            'quantity_purchased_units' => $row['quantity_purchased_units'],
            'quantity_purchased_value' => $row['quantity_purchased_value'],
            'quantity_purchased_units_transfer' => $row['quantity_purchased_units_transfer'],
            'quantity_purchased_value_transfer' => $row['quantity_purchased_value_transfer'],
            'returns_from_customers_units' => $row['returns_from_customers_units'],
            'returns_from_customers_value' => $row['returns_from_customers_value'],
            'other_additions_units' => $row['other_additions_units'],
            'other_additions_value' => $row['other_additions_value'],
            'quantity_sold_instore_units' => $row['quantity_sold_instore_units'],
            'quantity_sold_instore_value' => $row['quantity_sold_instore_value'],
            'quantity_sold_online_units' => $row['quantity_sold_online_units'],
            'quantity_sold_online_value' => $row['quantity_sold_online_value'],
            'quantity_sold_units_transfer' => $row['quantity_sold_units_transfer'],
            'quantity_sold_value_transfer' => $row['quantity_sold_value_transfer'],
            'quantity_destroyed_units' => $row['quantity_destroyed_units'],
            'quantity_destroyed_value' => $row['quantity_destroyed_value'],
            'quantity_losttheft_units' => $row['quantity_losttheft_units'],
            'quantity_losttheft_value' => $row['quantity_losttheft_value'],
            'returns_to_aglc_units' => $row['returns_to_aglc_units'],
            'returns_to_aglc_value' => $row['returns_to_aglc_value'],
            'other_reductions_units' => $row['other_reductions_units'],
            'other_reductions_value' => $row['other_reductions_value'],
            'closing_inventory_units' => $row['closing_inventory_units'],
            'closing_inventory_value' => $row['closing_inventory_value'],
        ]);
    }
}
