<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\IdealDiagnosticReport;
use App\Models\IdealSalesSummaryReport;
use Illuminate\Support\Facades\Log;

trait IdealIntegration
{
    use ICIntegrationTrait;

    /**
     * Process IdealPOS reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function processIdealPOSReports($reports)
    {
        Log::info('Processing IdealPOS reports:', ['reports' => $reports]);

        foreach ($reports as $report) {
            // Attempt to find the report in ideal_diagnostic_reports or ideal_sales_summary_reports
            $idealReport = IdealDiagnosticReport::with('report')->find($report->id);

            // if (!$idealReport) {
            //     // If not found in diagnostic reports, check in sales summary reports
            //     $idealReport = IdealSalesSummaryReport::with('report')->find($report->id);
            // }

            if (!$idealReport) {
                Log::warning('IdealPOS report not found in both tables:', ['report_id' => $report->id]);
                continue;
            }

            $retailer_id = $idealReport->report->retailer_id ?? null;
            $location = $idealReport->report->location ?? null;

            if (!$retailer_id) {
                Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
                continue;
            }

            $sku = $idealReport->sku;
            $gtin = $idealReport->barcode;

            $provinceName = null;
            $provinceSlug = null;
            $product = null;
            $lpName = null;
            $retailerName = null;

            // Fetch Retailer Name
            $retailer = Retailer::find($retailer_id);
            if ($retailer) {
                $retailerName = trim("{$retailer->first_name} {$retailer->last_name}");
            } else {
                Log::warning('Retailer not found:', ['retailer_id' => $retailer_id]);
            }

            // Match the product using SKU and GTIN
            if (!empty($sku)) {
                $product = $this->matchICSku($sku);
            } elseif (!empty($idealReport->productname)) {
                // If no SKU match, try to match product by name
                $product = $this->matchICProductName($idealReport->description);
            }

            if ($product) {
                // Fetch province information
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;

                // Fetch LP name using lp_id
                $lpName = Product::find($product->id)->lp->name ?? null;
                
                // Calculate dqi_fee and dqi_per
                $dqi_fee = $this->calculateDqiFee($idealReport, $product);
                $dqi_per = $this->calculateDqiPer($idealReport, $product);

                $cleanSheetData = [
                    'retailer_id' => $retailer_id,
                    'report_id' => $report->id,
                    'retailer_name' => $retailerName,
                    'lp_name' => $lpName,
                    'thc_range' => $product->thc_range,
                    'cbd_range' => $product->cbd_range,
                    'size_in_gram' => $product->product_size,
                    'location' => $location,
                    'province' => $provinceName,
                    'province_slug' => $provinceSlug,
                    'sku' => $sku,
                    'product_name' => $idealReport->productname,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    'sold' => $idealReport->unit_sold ?? "0",
                    'purchase' => $idealReport->purchases,
                    'average_price' => $report->average_price,
                    'average_cost' => $report->average_cost,
                    'report_price_og' => $report->report_price_og,
                    'barcode' => $gtin,
                    'transfer_in' => $report->transfer_in,
                    'transfer_out' => $report->transfer_out,
                    'pos' => 'IdealPOS',
                    'pos_report_id' => $idealReport->id,
                    'comment' => 'Record found in the Master Catalog',
                    'opening_inventory_unit' => $idealReport->opening ?? "0",
                    'closing_inventory_unit' => $idealReport->closing ?? "0",
                    'reconciliation_date' => now(),
                ];

                $offers = $this->DQISummaryFlag($idealReport->sku, $idealReport->barcode, $idealReport->productname);

                if (!empty($offers)) {
                    $cleanSheetData['offer_id'] = $offers->id;
                    $cleanSheetData['lp_id'] = $product->lp_id;
                    $cleanSheetData['dqi_fee'] = $dqi_fee;
                    $cleanSheetData['dqi_per'] = $dqi_per;
                }

                $this->saveToCleanSheet($cleanSheetData);
            } else {
                Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);

                $offer = !empty($sku) ? $this->matchOfferSku($sku) : null;

                if (!$offer && !empty($idealReport->productname)) {
                    $offer = $this->matchOfferProductName($idealReport->productname);
                }

                if ($offer) {
                    $lpName = Offer::find($offer->id)->lp->name ?? null;

                    $dqi_fee = $this->calculateDqiFee($idealReport, $offer);
                    $dqi_per = $this->calculateDqiPer($idealReport, $offer);

                    $cleanSheetData = [
                        'retailer_id' => $retailer_id,
                        'lp_id' => $offer->lp_id,
                        'report_id' => $report->id,
                        'retailer_name' => $retailerName,
                        'lp_name' => $lpName,
                        'thc_range' => $offer->thc_range,
                        'cbd_range' => $offer->cbd_range,
                        'size_in_gram' => $offer->product_size,
                        'location' => $location,
                        'province' => $offer->province,
                        'province_slug' => $offer->province_slug,
                        'sku' => $sku,
                        'product_name' => $offer->product_name,
                        'category' => $offer->category,
                        'brand' => $offer->brand,
                        'sold' => $idealReport->unit_sold ?? "0",
                        'purchase' => $idealReport->purchases,
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'IdealPOS',
                        'pos_report_id' => $idealReport->id,
                        'comment' => 'Record found in the Offers Table',
                        'opening_inventory_unit' => $idealReport->opening ?? "0",
                        'closing_inventory_unit' => $idealReport->closing ?? "0",
                        'dqi_fee' => $dqi_fee,
                        'dqi_per' => $dqi_per,
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                } else {
                    Log::info('No product or offer found, saving report data as is:', ['report_data' => $report]);

                    $cleanSheetData = [
                        'retailer_id' => $retailer_id,
                        'lp_id' => null,
                        'report_id' => $report->id,
                        'retailer_name' => $retailerName,
                        'lp_name' => null,
                        'thc_range' => null,
                        'cbd_range' => null,
                        'size_in_gram' => null,
                        'location' => $location,
                        'province' => null,
                        'province_slug' => null,
                        'sku' => $sku,
                        'product_name' => $idealReport->productname,
                        'category' => $idealReport->category,
                        'brand' => $idealReport->brand,
                        'sold' => $idealReport->unit_sold ?? "0",
                        'purchase' => $idealReport->purchases,
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'IdealPOS',
                        'pos_report_id' => $idealReport->id,
                        'comment' => 'Record not found in the Catalog and Offers Table',
                        'opening_inventory_unit' => $idealReport->opening ?? "0",
                        'closing_inventory_unit' => $idealReport->closing ?? "0",
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                }
            }
        }
    }
}
