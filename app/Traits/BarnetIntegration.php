<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\BarnetPosReport;
use Illuminate\Support\Facades\Log;
use App\Models\BarnetReport; // Adjust the model name based on your application

trait BarnetIntegration
{
    use ICIntegrationTrait;

    /**
     * Process Barnet reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function processBarnetReports($reports)
    {
        Log::info('Processing Barnet reports:', ['reports' => $reports]);

        foreach ($reports as $report) {
            // Retrieve the Barnet report by report_id
            $barnetReport = BarnetPosReport::with('report')->find($report->id);

            if (!$barnetReport) {
                Log::warning('Barnet report not found:', ['report_id' => $report->id]);
                continue;
            }

            $retailer_id = $barnetReport->report->retailer_id ?? null;
            $location = $barnetReport->report->location ?? null;

            if (!$retailer_id) {
                Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
                continue;
            }

            $sku = $barnetReport->product_sku;
            $gtin = $barnetReport->barcode;

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
            if (!empty($gtin) && !empty($sku)) {
                $product = $this->matchICBarcodeSku($sku, $gtin);
            } elseif (!empty($gtin) && empty($sku)) {
                $product = $this->matchICBarcode($gtin);
            } elseif (empty($gtin) && !empty($sku)) {
                $product = $this->matchICSku($sku);
                
            } elseif (!empty($techPOSReport->productname)) {
                // If no SKU match, try to match product by name
                $product = $this->matchICProductName($barnetReport->productname);
            }

            if ($product) {
                // Fetch province information
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;

                // Fetch LP name using lp_id
                $lpName = Product::find($product->id)->lp->name ?? null; // Assuming you have a relationship set up in Product model

                // Calculate dqi_fee and dqi_per
                $dqi_fee = $this->calculateDqiFee($barnetReport, $product);
                $dqi_per = $this->calculateDqiPer($barnetReport, $product);

                $cleanSheetData = [
                    'retailer_id' => $retailer_id,
                    // 'lp_id' => $product->lp_id,
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
                    'product_name' => $barnetReport->name,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    'sold' => $barnetReport->quantity_sold_units ?? '0',
                    'purchase' => $barnetReport->quantity_purchased_units ?? '0',
                    'average_price' => $report->average_price,
                    'average_cost' => $report->average_cost,
                    'report_price_og' => $report->report_price_og,
                    'barcode' => $gtin,
                    'transfer_in' => $barnetReport->other_additions_units ?? '0',
                    'transfer_out' => $barnetReport->transfer_units ?? '0',
                    'pos' => 'Barnet',
                    'pos_report_id' => $barnetReport->id,
                    'comment' => 'Record found in the Master Catalog',
                    'opening_inventory_unit' =>$barnetReport->opening_inventory_units ?? '0',
                    'closing_inventory_unit' => $barnetReport->closing_inventory_units ?? '0',
                
                    // 'dqi_fee' => $dqi_fee,
                    // 'dqi_per' => $dqi_per,
                    'reconciliation_date' => now(),
                ];
                $offers =$this->DQISummaryFlag($barnetReport->product_sku,$barnetReport->barcode,$barnetReport->description); // Get the offers

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
                    $dqi_fee = $this->calculateDqiFee($barnetReport, $offer);
                    $dqi_per = $this->calculateDqiPer($barnetReport, $offer);

                    $cleanSheetData = [
                        'retailer_id' => $retailer_id,
                        'lp_id' => $offer->lp_id,
                        'report_id' => $report->id,
                        'retailer_name' => $retailerName,
                        'lp_name' => $lpName,
                        'thc_range' => $offer->thc_range,
                        'cbd_range' => $offer->cbd_range,
                        'size_in_gram' => $offer->size_in_gram,
                        'location' => $location,
                        'province' => $offer->province,
                        'province_slug' => $offer->province_slug,
                        'sku' => $sku,
                        'product_name' => $offer->product_name,
                        'category' => $offer->category,
                        'brand' => $offer->brand,
                        'sold' => $barnetReport->quantity_sold_units ?? '0',
                        'purchase' => $barnetReport->quantity_purchased_units ?? '0',
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' =>$barnetReport->other_additions_units ?? '0',
                        'transfer_out' =>$barnetReport->transfer_units ?? '0',
                        'pos' => 'Barnet',
                        'pos_report_id' => $barnetReport->id,
                        'comment' => 'Record found in the Offers Table',
                        'opening_inventory_unit' => $barnetReport->opening_inventory_units ?? '0',
                        'closing_inventory_unit' => $barnetReport->closing_inventory_units ?? '0',
                    
                        'dqi_fee' => $dqi_fee,
                        'dqi_per' => $dqi_per,
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                } else {
                    // If neither product nor offer is found, save report data as is
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
                        'product_name' => $barnetReport->name,
                        'category' => null,
                        'brand' => null,
                        'sold' =>  $barnetReport->quantity_sold_units ?? '0',
                        'purchase' => $barnetReport->quantity_purchased_units ?? '0',
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $barnetReport->other_additions_units ?? '0',
                        'transfer_out' =>$barnetReport->transfer_units ?? '0',
                        'pos' => 'Barnet',
                        'pos_report_id' => $barnetReport->id,
                        'comment' => 'No product or offer found for this report',
                        'opening_inventory_unit' =>  $barnetReport->opening_inventory_units ?? '0',
                        'closing_inventory_unit' => $barnetReport->closing_inventory_units ?? '0',
            
                        'dqi_fee' => null,
                        'dqi_per' => null,
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                }
            }
        }
    }

    // Example method to save to CleanSheet
    // protected function saveToCleanSheet(array $data)
    // {
    //     // Assuming you have a CleanSheet model to save the data
    //     CleanSheet::create($data);
    // }

    // // Example methods for matching products and offers
    // protected function matchICBarcodeSku($sku, $gtin)
    // {
    //     // Implement matching logic based on your application structure
    // }

    // protected function matchOfferProduct($sku, $gtin)
    // {
    //     // Implement matching logic based on your application structure
    // }
}
