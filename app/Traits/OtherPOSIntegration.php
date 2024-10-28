<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\OtherPOSReport; // Assuming this is your Other POS report model
use Illuminate\Support\Facades\Log;

trait OtherPOSIntegration
{
    use ICIntegrationTrait;

    /**
     * Process Other POS reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function processOtherPOSReports($reports)
    {
        Log::info('Processing Other POS reports:', ['reports' => $reports]);

        foreach ($reports as $report) {
            // Retrieve the Other POS report by report_id
            $otherPOSReport = OtherPOSReport::with('report')->find($report->id);

            if (!$otherPOSReport) {
                Log::warning('Other POS report not found:', ['report_id' => $report->id]);
                continue;
            }

            $retailer_id = $otherPOSReport->report->retailer_id ?? null;
            $location = $otherPOSReport->report->location ?? null;

            if (!$retailer_id) {
                Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
                continue;
            }

            $sku = $otherPOSReport->sku;
            $gtin = $otherPOSReport->barcode;

            $provinceName = null;
            $provinceSlug = null;
            $product = null;
            $lpName = null; // Initialize LP Name variable
            $retailerName = null; // Initialize Retailer Name variable

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
            } elseif (!empty($otherPOSReport->productname)) {
                // If no SKU match, try to match product by name
                $product = $this->matchICProductName($otherPOSReport->productname);
            if ($product) {
                // Fetch province information
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;

                // Fetch LP name using lp_id
                $lpName = Product::find($product->id)->lp->name ?? null; // Assuming you have a relationship set up in Product model

                // Calculate dqi_fee and dqi_per
                $dqi_fee = $this->calculateDqiFee($otherPOSReport, $product);
                $dqi_per = $this->calculateDqiPer($otherPOSReport, $product);

                $cleanSheetData = [
                    'retailer_id' => $retailer_id,
                    'lp_id' => $product->lp_id,
                    'report_id' => $report->id,
                    'retailer_name' => $retailerName, // Use fetched Retailer name
                    'lp_name' => $lpName, // Use fetched LP name
                    'thc_range' => $product->thc_range,
                    'cbd_range' => $product->cbd_range,
                    'size_in_gram' =>  $product->product_size,
                    'location' => $location,
                    'province' => $provinceName,
                    'province_slug' => $provinceSlug,
                    'sku' => $sku,
                    'product_name' =>  $otherPOSReport->productname,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    'sold' => $otherPOSReport->sold ?? '0',
                    'purchase' => $otherPOSReport->purchased ?? '0',
                    'average_price' => $report->average_price,
                    'average_cost' => $report->average_cost,
                    'report_price_og' => $report->report_price_og,
                    'barcode' => $gtin,
                    'transfer_in' => $report->transfer_in,
                    'transfer_out' => $report->transfer_out,
                    'pos' => 'Other POS',
                    'pos_report_id' => $otherPOSReport->id,
                    'comment' => 'Record found in the Master Catalog',
                    'opening_inventory_unit' => $otherPOSReport->opening ?? '0',
                    'closing_inventory_unit' => $otherPOSReport->closing ?? '0',
            
                    'dqi_fee' => $dqi_fee,
                    'dqi_per' => $dqi_per,
                    'reconciliation_date' => now(),
                ];
                $offers =$this->DQISummaryFlag($otherPOSReport->sku,$otherPOSReport->barcode,$otherPOSReport->name); // Get the offers

                if (!empty($offers)) {
                    // Set DQI data in cleanSheetData
                    $cleanSheetData['lp_id'] = $product->lp_id;
                    $cleanSheetData['dqi_fee'] = $dqi_fee;
                    $cleanSheetData['dqi_per'] = $dqi_per;
                }
                $this->saveToCleanSheet($cleanSheetData);
            } else {
                Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);

                // Match the offer using SKU and GTIN
                $offer = null;
                if (!empty($gtin) && !empty($sku)) {
                    $offer = $this->matchOfferProduct($sku, $gtin); 
                } elseif (!empty($gtin)) {
                    $offer = $this->matchOfferBarcode($gtin); 
                } elseif (!empty($sku)) {
                    $offer = $this->matchOfferSku($sku); 
                }
                if ($offer) {
                    // Fetch LP name using lp_id
                    $lpName = Offer::find($offer->id)->lp->name ?? null;

                    // Handle offer data if product not found
                    $dqi_fee = $this->calculateDqiFee($otherPOSReport, $offer);
                    $dqi_per = $this->calculateDqiPer($otherPOSReport, $offer);

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
                        'sold' => $otherPOSReport->sold ?? '0',
                        'purchase' => $otherPOSReport->purchased ?? '0',
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'Other POS',
                        'pos_report_id' => $otherPOSReport->id,
                        'comment' => 'Record found in the Offers Table',
                        'opening_inventory_unit' =>$otherPOSReport->opening ?? '0',
                        'closing_inventory_unit' => $otherPOSReport->closing ?? '0',
                        
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
                        'sku' => $otherPOSReport->sku,
                        'product_name' => $otherPOSReport->productname,
                        'category' => $otherPOSReport->category,
                        'brand' => $otherPOSReport->brand,
                        'sold' => $otherPOSReport->sold ?? '0',
                        'purchase' => $otherPOSReport->purchased ?? '0',
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'Other POS',
                        'pos_report_id' => $otherPOSReport->id,
                        'comment' => 'No matching product or offer found',
                        'opening_inventory_unit' => $otherPOSReport->opening ?? '0',
                        'closing_inventory_unit' => $otherPOSReport->closing ?? '0',
                       
                        'dqi_fee' => null,
                        'dqi_per' => null,
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                }
            }
        }
    }

    /**
     * Save data to the CleanSheet.
     *
     * @param array $data
     * @return void
     */
}
}