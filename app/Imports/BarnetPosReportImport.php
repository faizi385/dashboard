<?php

namespace App\Imports;

use App\Models\BarnetPosReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarnetPosReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;

    public function __construct($location, $reportId)
    {
        $this->location = $location;
        $this->reportId = $reportId;
    }

    public function model(array $row)
    {
        return new BarnetPosReport([
            'store' => $row['store'],
            'product_sku' => $row['product_sku'],
            'description' => $row['description'],
            'uom' => $row['uom'],
            'category' => $row['category'],
            'opening_inventory_units' => $row['opening_inventory_units'],
            'opening_inventory_value' => $row['opening_inventory_value'],
            'quantity_purchased_units' => $row['quantity_purchased_units'],
            'quantity_purchased_value' => $row['quantity_purchased_value'],
            'returns_from_customers_units' => $row['returns_from_customers_units'],
            'returns_from_customers_value' => $row['returns_from_customers_value'],
            'other_additions_units' => $row['other_additions_units'],
            'other_additions_value' => $row['other_additions_value'],
            'quantity_sold_units' => $row['quantity_sold_units'],
            'quantity_sold_value' => $row['quantity_sold_value'],
            'transfer_units' => $row['transfer_units'],
            'transfer_value' => $row['transfer_value'],
            'returns_to_vendor_units' => $row['returns_to_vendor_units'],
            'returns_to_vendor_value' => $row['returns_to_vendor_value'],
            'inventory_adjustment_units' => $row['inventory_adjustment_units'],
            'inventory_adjustment_value' => $row['inventory_adjustment_value'],
            'destroyed_units' => $row['destroyed_units'],
            'destroyed_value' => $row['destroyed_value'],
            'closing_inventory_units' => $row['closing_inventory_units'],
            'closing_inventory_value' => $row['closing_inventory_value'],
            'min_stock' => $row['min_stock'],
            'low_inv' => $row['low_inv'],
            'report_id' => $this->reportId,
            'location' => $this->location,
        ]);
    }
}
