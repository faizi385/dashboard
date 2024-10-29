<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\GlobalTillDiagnosticReport;
use App\Models\GlobalTillSalesSummaryReport;
use Illuminate\Support\Facades\Log;

trait GlobalTillIntegration
{
    use ICIntegrationTrait;

    /**
     * Process GlobalTill reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function processGlobalTillReports($reports)
    {
        Log::info('Processing GlobalTill reports:', ['reports' => $reports]);

        foreach ($reports as $report) {
            // Attempt to find the report in global_till_diagnostic_reports
            $globalTillReport = GlobalTillDiagnosticReport::with('report')->find($report->id);

            // Check in sales summary reports if not found in diagnostic reports
            // if (!$globalTillReport) {
            //     $globalTillReport = GlobalTillSalesSummaryReport::with('report')->find($report->id);
            // }

            if (!$globalTillReport) {
                Log::warning('GlobalTill report not found in both tables:', ['report_id' => $report->id]);
                continue;
            }

            $retailer_id = $globalTillReport->report->retailer_id ?? null;
            $location = $globalTillReport->report->location ?? null;

            if (!$retailer_id) {
                Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
                continue;
            }

            $sku = $globalTillReport->supplier_sku;
            $gtin = $globalTillReport->compliance_code;

            // Initialize variables for product and retailer details
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
            if (!empty($gtin) && !empty($sku)) {
                $product = $this->matchICBarcodeSku($sku, $gtin);
            } elseif (!empty($gtin)) {
                $product = $this->matchICBarcode($gtin);
            } elseif (!empty($sku)) {
                $product = $this->matchICSku($sku);
            } else {
                $product = $this->matchICProductName($globalTillReport->product);
            }

            if ($product) {
                // Fetch province information
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;

                // Fetch LP name using lp_id
                $lpName = Product::find($product->id)->lp->name ?? null;

                // Calculate dqi_fee and dqi_per
                $dqi_fee = $this->calculateDqiFee($globalTillReport, $product);
                $dqi_per = $this->calculateDqiPer($globalTillReport, $product);

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
                    'product_name' => $globalTillReport->productname,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    'sold' => $globalTillReport->sales_reductions ?? "0",
                    'purchase' => $globalTillReport->purchases_from_suppliers_additions ?? "0",
                    'average_price' => $report->average_price,
                    'average_cost' => $report->average_cost,
                    'report_price_og' => $report->report_price_og,
                    'barcode' => $gtin,
                    'transfer_in' => $report->transfer_in,
                    'transfer_out' => $report->transfer_out,
                    'pos' => 'GlobalTill',
                    'pos_report_id' => $globalTillReport->id,
                    'comment' => 'Record found in the Master Catalog',
                    'opening_inventory_unit' => $globalTillReport->opening_inventory ?? "0",
                    'closing_inventory_unit' => $globalTillReport->closing_inventory ?? "0",
                    'reconciliation_date' => now(),
                ];

                // Check for DQI offers
                $offers = $this->DQISummaryFlag($globalTillReport->supplier_sku, $globalTillReport->compliance_code, $globalTillReport->product);

                if (!empty($offers)) {
                    $cleanSheetData['offer_id'] = $offers->id;
                    $cleanSheetData['lp_id'] = $product->lp_id;
                    $cleanSheetData['dqi_fee'] = $dqi_fee;
                    $cleanSheetData['dqi_per'] = $dqi_per;
                }

                $this->saveToCleanSheet($cleanSheetData);
            } else {
                Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);

                // Attempt to find an offer based on SKU or product name
                $offer = !empty($sku) ? $this->matchOfferSku($sku) : null;

                if (!$offer && !empty($globalTillReport->productname)) {
                    $offer = $this->matchOfferProductName($globalTillReport->productname);
                }

                if ($offer) {
                    $lpName = Offer::find($offer->id)->lp->name ?? null;

                    $dqi_fee = $this->calculateDqiFee($globalTillReport, $offer);
                    $dqi_per = $this->calculateDqiPer($globalTillReport, $offer);

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
                        'sold' => $globalTillReport->sales_reductions ?? "0",
                        'purchase' => $globalTillReport->purchases_from_suppliers_additions ?? "0",
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'GlobalTill',
                        'pos_report_id' => $globalTillReport->id,
                        'comment' => 'Record found in the Offers Table',
                        'opening_inventory_unit' => $globalTillReport->opening_inventory ?? "0",
                        'closing_inventory_unit' => $globalTillReport->closing_inventory ?? "0",
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
                        'product_name' => $globalTillReport->productname,
                        'category' => $globalTillReport->category,
                        'brand' => $globalTillReport->brand,
                        'sold' => $globalTillReport->sales_reductions ?? "0",
                        'purchase' => $globalTillReport->purchases_from_suppliers_additions ?? "0",
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'GlobalTill',
                        'pos_report_id' => $globalTillReport->id,
                        'comment' => 'No matching product or offer found',
                        'opening_inventory_unit' => $globalTillReport->opening_inventory ?? "0",
                        'closing_inventory_unit' => $globalTillReport->closing_inventory ?? "0",
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                }
            }
        }

        Log::info('Completed processing GlobalTill reports.');
    }

 
}
