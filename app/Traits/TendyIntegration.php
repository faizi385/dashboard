<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use Illuminate\Support\Facades\Log;
use App\Models\TendyDiagnosticReport;
use App\Models\TendySalesSummaryReport; // Import the sales summary report model

trait TendyIntegration
{
    use ICIntegrationTrait;

    /**
     * Process Tendy reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function processTendyReports($reports)
    {
        Log::info('Processing Tendy reports:', ['reports' => $reports]);
        
        foreach ($reports as $report) {
            // Retrieve the Tendy report by report_id
            $tendyReport = TendyDiagnosticReport::find($report->id);
        
            if (!$tendyReport) {
                Log::warning('Tendy report not found:', ['report_id' => $report->id]);
                continue;
            }
        
            $retailer_id = $tendyReport->report->retailer_id ?? null;
            $location = $tendyReport->report->location ?? null;

            if (!$retailer_id) {
                Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
                continue;
            }

            $sku = $tendyReport->product_sku;
            $gtin = $tendyReport->barcode;

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

            // Match the product using SKU from TendyDiagnosticReport and if not found, check TendySalesSummaryReport
            if (!empty($sku)) {
                $product = $this->matchICSku($sku);
            }

            // // If no product found, try matching by product name from TendyDiagnosticReport
            // if (!$product && !empty($tendyReport->productname)) {
            //     $product = $this->matchICProductName($tendyReport->product);
            // }

            // If still not found, check TendySalesSummaryReport for SKU
            if (!$product && !empty($sku)) {
                $tendySalesReport = TendySalesSummaryReport::where('sku', $sku)->first();
                if ($tendySalesReport) {
                    // Try to match product by name from sales summary
                    $product = $this->matchICProductName($tendySalesReport->product);
                }
            }
            if ($product) {
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;

                $lpName = Product::find($product->id)->lp->name ?? null;

                $dqi_fee = $this->calculateDqiFee($tendyReport, $product);
                $dqi_per = $this->calculateDqiPer($tendyReport, $product);
        
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
                    'product_name' => $tendyReport->product,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    // Check for net_qty_sold in TendySalesSummaryReport
                    'sold' => isset($tendyReport->net_qty_sold) ? $tendySalesReport->net_qty_sold : '0',
              

                    'purchase' => $tendyReport->quantity_purchased_units ?? '0',
                    'average_price' =>       isset($tendyReport->avg_retail_price) ? $tendySalesReport->avg_retail_price: '0',
                    'average_cost' => $report->average_cost,
                    'report_price_og' => $report->report_price_og,
                    'barcode' => $gtin,
                    'transfer_in' => $tendyReport->quantity_purchased_units_transfer ?? '0',
                    'transfer_out' => $tendyReport->quantity_sold_units_transfer ?? '0',
                    'pos' => 'Tendy',
                    'pos_report_id' => $tendyReport->id,
                    'comment' => 'Record found in the Master Catalog',
                    'opening_inventory_unit' => $tendyReport->opening_inventory_units ?? '0',
                    'closing_inventory_unit' => $tendyReport->closing_inventory_units ?? '0',
                
                    'dqi_fee' => $dqi_fee,
                    'dqi_per' => $dqi_per,
                    'reconciliation_date' => now(),
                ];
    

                $offers = $this->DQISummaryFlag($tendyReport->product_sku, null, $product); // Get the offers

                if (!empty($offers)) {
                    $cleanSheetData['offer_id'] = $offers->id;
                    $cleanSheetData['lp_id'] = $product->lp_id;
                    $cleanSheetData['dqi_fee'] = $dqi_fee;
                    $cleanSheetData['dqi_per'] = $dqi_per;

                    $this->saveToCleanSheet($cleanSheetData);
                } else {
                    Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);

                    $offer = null;
                    if (!empty($gtin) && !empty($sku)) {
                        $offer = $this->matchOfferProduct($sku, $gtin);
                    } elseif (!empty($gtin)) {
                        $offer = $this->matchOfferBarcode($gtin);
                    } elseif (!empty($sku)) {
                        $offer = $this->matchOfferSku($sku);
                    }

                    if ($offer) {
                        $lpName = Offer::find($offer->id)->lp->name ?? null;
                        $dqi_fee = $this->calculateDqiFee($tendyReport, $offer);
                        $dqi_per = $this->calculateDqiPer($tendyReport, $offer);

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
                            'sold' => isset($tendyReport->net_qty_sold) ? $tendySalesReport->net_qty_sold : '0',
                            'purchase' =>$tendyReport->quantity_purchased_units ?? '0',
                            'average_price' =>  isset($tendyReport->avg_retail_price) ? $tendySalesReport->avg_retail_price: '0',
                            'average_cost' => $report->average_cost,
                            'report_price_og' => $report->report_price_og,
                            'barcode' => $gtin,
                            'transfer_in' => $tendyReport->quantity_purchased_units_transfer ?? '0',
                            'transfer_out' => $tendyReport->quantity_sold_units_transfer ?? '0',
                            'pos' => 'Tendy',
                            'pos_report_id' => $tendyReport->id,
                            'comment' => 'Record found in the Offers Table',
                            'opening_inventory_unit' =>  $tendyReport->opening_inventory_units ?? '0',
                            'closing_inventory_unit' => $tendyReport->closing_inventory_units ?? '0',
                            'purchase' => $tendyReport->purchased ?? '0',
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
                            'lp_name' => $lpName,
                            'thc_range' => null,
                            'cbd_range' => null,
                            'size_in_gram' => null,
                            'location' => $location,
                            'province' => null,
                            'province_slug' => null,
                            'sku' => $sku,
                            'product_name' => $tendyReport->name,
                            'category' => null,
                            'brand' => null,
                            'sold' =>isset($tendyReport->net_qty_sold) ? $tendySalesReport->net_qty_sold : '0',
                            'purchase' => $tendyReport->quantity_purchased_units ?? '0',
                            'average_price' => $report->average_price,
                            'average_cost' => $report->average_cost,
                            'report_price_og' => $report->report_price_og,
                            'barcode' => $gtin,
                            'transfer_in' => $tendyReport->quantity_purchased_units_transfer ?? '0',
                            'transfer_out' => $report->transfer_out,
                            'pos' => 'Tendy',
                            'pos_report_id' => $tendyReport->id,
                            'comment' => 'No match found in the Master Catalog or Offers Table',
                            'opening_inventory_unit' =>  $tendyReport->opening_inventory_units ?? '0',
                            'closing_inventory_unit' => $tendyReport->closing ?? '0',
                            'purchase' => $tendyReport->purchased ?? '0',
                            'dqi_fee' => null,
                            'dqi_per' => null,
                            'reconciliation_date' => now(),
                        ];

                        $this->saveToCleanSheet($cleanSheetData);
                    }
                }
            } else {
                Log::warning('SKU is empty for report:', ['report_id' => $report->id]);
            }
        }
    }
}
