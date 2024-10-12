<?php


namespace App\Imports;

use App\Models\IdealSalesSummaryReport; // Adjust the model namespace if needed
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IdealSalesSummaryReportImport implements ToModel, WithHeadingRow
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
        return new IdealSalesSummaryReport([
            'location' => $this->location,
            'report_id' => $this->reportId,
            'sku' => $row['sku'],
            'product_description' => $row['product_description'],
            'quantity_purchased' => $row['quantity_purchased'],
            'purchase_amount' => $row['purchase_amount'],
            'return_quantity' => $row['return_quantity'],
            'amount_return' => $row['amount_return'],
        ]);
    }
}
