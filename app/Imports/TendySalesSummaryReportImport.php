<?php

namespace App\Imports;

use App\Models\TendySalesSummaryReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; 
class TendySalesSummaryReportImport implements ToModel,WithHeadingRow
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
        
        // Skip this row if the required location data is not present
       

        return new TendySalesSummaryReport([
           
            'category' => $row['category'],
            'compliance_type' => $row['compliance_type'], // Add compliance_type
            'brand' => $row['brand'],
            'product' => $row['product'],
            'variant' => $row['variant'], // Add variant
            'sku' => $row['sku'],
            'items_sold' => $row['items_sold'],
            'items_refunded' => $row['items_refunded'],
            'net_qty_sold' => $row['net_qty_sold'],
            'gross_sales' => $row['gross_sales'],
            'net_sales' => $row['net_sales'],
            'total_discounts' => $row['total_discounts'],
            'markdown' => $row['markdown'],
            'reward_tiers' => $row['reward_tiers'],
            'total_tax' => $row['total_tax'],
            'cost_of_goods_sold' => $row['cost_of_goods_sold'],
            'gross_profit' => $row['gross_profit'],
            'avg_retail_price' => $row['avg_retail_price'],
            'gross_margin' => $row['gross_margin'],
            'report_id' => $this->reportId,
            'location' => $this->location,
        ]);
    }
}
