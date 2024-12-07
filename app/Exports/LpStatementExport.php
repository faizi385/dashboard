<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LpStatementExport implements FromCollection, WithMapping, WithHeadings
{
    private $includeTotalFeePercentage;
    private $sortedCollection;

    public function __construct($includeTotalFeePercentage = true, $sortedCollection)
    {
        $this->includeTotalFeePercentage = $includeTotalFeePercentage;
        $this->sortedCollection = $sortedCollection;
    }

    public function collection()
    {
        return $this->sortedCollection;
    }

    public function map($row): array
    {
        $data = [
            $row->province,
            $row->retailer_dba,
            $row->retailer,
            $row->product,
            $row->sku,
            $row->category,
            $row->brand,
            !empty($row->quantity_purchased) ? $row->quantity_purchased : '0',
            !empty($row->sold) ? $row->sold : '0',
            !empty(str_replace('$', '', $row->average_price)) ? $row->average_price : number_format(0, 2, '.', ','),
            !empty($row->opening_inventory_unit) ? $row->opening_inventory_unit : '0',
            !empty($row->closing_inventory_unit) ? $row->closing_inventory_unit : '0',
            $row->unit_cost,
            $row->total_purchased_cost,
            number_format((float)$row->total_fee_dollars, 2, '.', '') ?? 0.00,
            $row->calculated_value
        ];

        if ($this->includeTotalFeePercentage) {
            $data[] = $row->total_fee_percentage;
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            "Province",
            "Distributor",
            "Location",
            'Product',
            "SKU",
            "Category",
            "Brand",
            "Quantity Received",
            "Quantity Sold",
            "Average Price($)",
            "Opening Inventory Units",
            "Closing Inventory Units",
            "Unit Cost($)",
            "Total Purchase Cost($)",
            "Total Fee($)",
            "Total Fee With Tax($)"
        ];

        if ($this->includeTotalFeePercentage) {
            $headings[] = "Total Fee(%)";
        }

        return $headings;
    }
}


