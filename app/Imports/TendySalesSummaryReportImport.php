<?php

namespace App\Imports;

use App\Models\TendyDiagnosticReport;
use App\Models\TendySalesSummaryReport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class TendySalesSummaryReportImport implements ToModel, WithHeadingRow
{
    protected $location;
    protected $reportId;
    protected $errors = []; // Array to store error messages
    protected $hasCheckedHeaders = false; // Flag to check if headers have been validated

    public function __construct($location, $reportId)
    {
        $this->location = $location; // Store location
        $this->reportId = $reportId; // Store report_id
    }

    public function model(array $row)
    {
        // List of required headers
        $requiredHeaders = [
            'category',
            'compliance_type',
            'brand',
            'product',
            'variant',
            'sku',
            'items_sold',
            'items_refunded',
            'net_qty_sold',
            'gross_sales',
            'net_sales',
            'total_discounts',
            'markdown',
            'reward_tiers',
            'total_tax',
            'cost_of_goods_sold',
            'gross_profit',
            'avg_retail_price',
            'gross_margin',
        ];

        // Check if required headers are missing only once
        if (!$this->hasCheckedHeaders) {
            $missingHeaders = array_diff($requiredHeaders, array_keys($row));
            if (!empty($missingHeaders)) {
                // Log an error
                Log::error('Missing headers in Tendy sales summary report: ' . implode(', ', $missingHeaders));

                // Format headers to replace underscores with spaces
                $formattedHeaders = array_map(function ($header) {
                    return str_replace('_', ' ', $header); // Replace underscores with spaces
                }, $missingHeaders);

                // Throw an exception with a message listing the missing headers
                throw new \Exception('Missing header: ' . implode(', ', $formattedHeaders));
            }
        }
        $tendyDaignosticReport = TendyDiagnosticReport::where('product_sku', $row['sku'])->where('report_id', $this->reportId)->first();
        if($tendyDaignosticReport) {
            if (!empty(array_filter($row))) {
                if ($row['product'] != null || $row['sku'] != null) {
                    if (($row['sku'] != '*')) {
                        // Proceed with creating the model if headers are valid
                        return new TendySalesSummaryReport([
                            'category' => $row['category'] ?? null,
                            'compliance_type' => $row['compliance_type'] ?? null,
                            'brand' => $row['brand'] ?? null,
                            'product' => $row['product'] ?? null,
                            'variant' => $row['variant'] ?? null,
                            'sku' => $row['sku'] ?? null,
                            'items_sold' => $row['items_sold'] ?? null,
                            'items_refunded' => $row['items_refunded'] ?? null,
                            'net_qty_sold' => $row['net_qty_sold'] ?? null,
                            'gross_sales' => $row['gross_sales'] ?? null,
                            'net_sales' => $row['net_sales'] ?? null,
                            'total_discounts' => $row['total_discounts'] ?? null,
                            'markdown' => $row['markdown'] ?? null,
                            'reward_tiers' => $row['reward_tiers'] ?? null,
                            'total_tax' => $row['total_tax'] ?? null,
                            'cost_of_goods_sold' => $row['cost_of_goods_sold'] ?? null,
                            'gross_profit' => $row['gross_profit'] ?? null,
                            'avg_retail_price' => $row['avg_retail_price'] ?? null,
                            'gross_margin' => $row['gross_margin'] ?? null,
                            'report_id' => $this->reportId,
                            'location' => $this->location,
                            'diagnostic_report_id' => $tendyDaignosticReport->id ?? null,
                        ]);
                    }
                }
            }
        }else{
            return;
        }
    }

    public function getErrors()
    {
        return $this->errors; // Return collected errors
    }
}
