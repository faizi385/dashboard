<?php

namespace App\Imports;

use App\Models\Report;
use Illuminate\Support\Facades\Log;
use App\Models\TendyDiagnosticReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TendyDiagnosticReportImport implements ToModel, WithHeadingRow
{
    protected $reportId;
    protected $location;
    protected $retailerId;
    protected $lpId;
    protected $errors = [];
    protected $hasCheckedHeaders = false;

    public function __construct($reportId, $location, $retailerId, $lpId = null)
    {
        $this->reportId = $reportId;
        $this->location = $location;
        $this->retailerId = $retailerId;
        $this->lpId = $lpId;
    }

    public function model(array $row)
    {
        $requiredHeaders = [
            'product_sku',
            'opening_inventory_units',
            'opening_inventory_value',
            'quantity_purchased_units',
            'quantity_purchased_value',
            'quantity_purchased_units_transfer',
            'quantity_purchased_value_transfer',
            'returns_from_customers_units',
            'returns_from_customers_value',
            'other_additions_units',
            'other_additions_value',
            'quantity_sold_instore_units',
            'quantity_sold_instore_value',
            'quantity_sold_online_units',
            'quantity_sold_online_value',
            'quantity_sold_units_transfer',
            'quantity_sold_value_transfer',
            'quantity_destroyed_units',
            'quantity_destroyed_value',
            'quantity_losttheft_units',
            'quantity_losttheft_value',
            'returns_to_aglc_units',
            'returns_to_aglc_value',
            'other_reductions_units',
            'other_reductions_value',
            'closing_inventory_units',
            'closing_inventory_value',
        ];

        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                Log::error('Missing headers in Tendy diagnostic report: ' . implode(', ', $missingHeaders));

                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header);
                }, $missingHeaders);

                throw new \Exception('Missing header: ' . implode(', ', $formattedHeaders));
            }
        }
        $report = Report::find($this->reportId);

        $reportDate = $report ? $report->date : null;
        if(!empty($row['product_sku'])) {


            return new TendyDiagnosticReport([
                'report_id' => $this->reportId,
                'location' => $this->location,
                'product_sku' => $row['product_sku'] ?? null,
                'opening_inventory_units' => $row['opening_inventory_units'] ?? null,
                'opening_inventory_value' => $row['opening_inventory_value'] ?? null,
                'quantity_purchased_units' => $row['quantity_purchased_units'] ?? null,
                'quantity_purchased_value' => $row['quantity_purchased_value'] ?? null,
                'quantity_purchased_units_transfer' => $row['quantity_purchased_units_transfer'] ?? null,
                'quantity_purchased_value_transfer' => $row['quantity_purchased_value_transfer'] ?? null,
                'returns_from_customers_units' => $row['returns_from_customers_units'] ?? null,
                'returns_from_customers_value' => $row['returns_from_customers_value'] ?? null,
                'other_additions_units' => $row['other_additions_units'] ?? null,
                'other_additions_value' => $row['other_additions_value'] ?? null,
                'quantity_sold_instore_units' => $row['quantity_sold_instore_units'] ?? null,
                'quantity_sold_instore_value' => $row['quantity_sold_instore_value'] ?? null,
                'quantity_sold_online_units' => $row['quantity_sold_online_units'] ?? null,
                'quantity_sold_online_value' => $row['quantity_sold_online_value'] ?? null,
                'quantity_sold_units_transfer' => $row['quantity_sold_units_transfer'] ?? null,
                'quantity_sold_value_transfer' => $row['quantity_sold_value_transfer'] ?? null,
                'quantity_destroyed_units' => $row['quantity_destroyed_units'] ?? null,
                'quantity_destroyed_value' => $row['quantity_destroyed_value'] ?? null,
                'quantity_losttheft_units' => $row['quantity_losttheft_units'] ?? null,
                'quantity_losttheft_value' => $row['quantity_losttheft_value'] ?? null,
                'returns_to_aglc_units' => $row['returns_to_aglc_units'] ?? null,
                'returns_to_aglc_value' => $row['returns_to_aglc_value'] ?? null,
                'other_reductions_units' => $row['other_reductions_units'] ?? null,
                'other_reductions_value' => $row['other_reductions_value'] ?? null,
                'closing_inventory_units' => $row['closing_inventory_units'] ?? null,
                'closing_inventory_value' => $row['closing_inventory_value'] ?? null,
                'retailer_id' => $this->retailerId,
                'lp_id' => $this->lpId,
                'date' => $reportDate,
            ]);
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
