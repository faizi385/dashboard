<?php


namespace App\Imports;

use App\Models\CovaSalesReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CovaSalesReportImport implements ToModel, WithHeadingRow
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
        return new CovaSalesReport([
            'report_id' => $this->reportId,
            'product' => $row['product'],
            'sku' => $row['sku'],
            'classification' => $row['classification'],
            'items_sold' => $row['items_sold'],
            'items_ref' => $row['items_ref'],
            'net_sold' => $row['net_sold'],
            'gross_sales' => $row['gross_sales'],
            'subtotal' => $row['subtotal'],
            'total_cost' => $row['total_cost'],
            'gross_profit' => $row['gross_profit'],
            'gross_margin' => $row['gross_margin'],
            'total_discount' => $row['total_discount'],
            'markdown_percent' => $row['markdown_percent'],
            'avg_regular_price' => $row['avg_regular_price'],
            'avg_sold_at_price' => $row['avg_sold_at_price'],
            'unit_type' => $row['unit_type'],
            'net_weight' => $row['net_weight'],
            'total_net_weight' => $row['total_net_weight'],
            'brand' => $row['brand'],
            'supplier' => $row['supplier'],
            'supplier_skus' => $row['supplier_skus'],
            'total_tax' => $row['total_tax'],
            'hst_13' => $row['hst_13'],
        ]);
    }
}
