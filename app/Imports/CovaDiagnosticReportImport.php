<?php

namespace App\Imports;

use App\Models\CovaDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CovaDiagnosticReportImport implements ToModel, WithHeadingRow
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
        return new CovaDiagnosticReport([
            'product_name' => $row['product_name'],
            'type' => $row['type'],
            'aglc_sku' => $row['aglc_sku'],
            'new_brunswick_sku' => $row['new_brunswick_sku'],
            'ocs_sku' => $row['ocs_sku'],
            'ylc_sku' => $row['ylc_sku'],
            'manitoba_barcodeupc' => $row['manitoba_barcodeupc'],
            'ontario_barcodeupc' => $row['ontario_barcodeupc'],
            'saskatchewan_barcodeupc' => $row['saskatchewan_barcodeupc'],
            'link_to_product' => $row['link_to_product'],
            'opening_inventory_units' => $row['opening_inventory_units'],
            'quantity_purchased_units' => $row['quantity_purchased_units'],
            'reductions_receiving_error_units' => $row['reductions_receiving_error_units'],
            'returns_from_customers_units' => $row['returns_from_customers_units'],
            'other_additions_units' => $row['other_additions_units'],
            'quantity_sold_units' => $row['quantity_sold_units'],
            'quantity_destroyed_units' => $row['quantity_destroyed_units'],
            'quantity_lost_theft_units' => $row['quantity_lost_theft_units'],
            'returns_to_supplier_units' => $row['returns_to_supplier_units'],
            'other_reductions_units' => $row['other_reductions_units'],
            'closing_inventory_units' => $row['closing_inventory_units'],
            'report_id' => $this->reportId,
            'location' => $this->location,
        ]);
    }
}
