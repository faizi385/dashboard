<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\GreenlineReport;
use Illuminate\Support\Facades\Log;

trait GreenlineICIntegration
{
    use ICIntegrationTrait;

    /**
     * Process Greenline reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function processGreenlineReports($reports)
    {
        Log::info('Processing Greenline reports:', ['reports' => $reports]);
        
        foreach ($reports as $report) {
            // Retrieve the Greenline report by report_id
            $greenlineReport = GreenlineReport::with('report')->find($report->id);
        
            if (!$greenlineReport) {
                Log::warning('Greenline report not found:', ['report_id' => $report->id]);
                continue;
            }
        
            $retailer_id = $greenlineReport->report->retailer_id ?? null;
            $location = $greenlineReport->report->location ?? null;
    
            if (!$retailer_id) {
                Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
                continue;
            }
    
            $sku = $greenlineReport->sku;
            $gtin = $greenlineReport->barcode;
    
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
            } else {
                Log::warning('Both SKU and GTIN are empty for report:', ['report_id' => $report->id]);
                continue;
            }
        
            if ($product) {
                // Fetch province information
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;
    
                // Fetch LP name using lp_id
                $lpName = Product::find($product->id)->lp->name ?? null; // Assuming you have a relationship set up in Product model
                
                // Calculate dqi_fee and dqi_per
                $dqi_fee = $this->calculateDqiFee($greenlineReport, $product);
                $dqi_per = $this->calculateDqiPer($greenlineReport, $product);
        
                $cleanSheetData = [
                    'retailer_id' => $retailer_id,
                    // 'lp_id' => $product->lp_id,
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
                    'product_name' => $greenlineReport->name,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    'sold' => $greenlineReport->sold,
                    'purchase' => $report->purchase,
                    'average_price' => $report->average_price,
                    'average_cost' => $report->average_cost,
                    'report_price_og' => $report->report_price_og,
                    'barcode' => $gtin,
                    'transfer_in' => $report->transfer_in,
                    'transfer_out' => $report->transfer_out,
                    'pos' => 'Greenline',
                    'pos_report_id' => $greenlineReport->id,
                    'comment' => 'Record found in the Master Catalog',
                    'opening_inventory_unit' => $greenlineReport->opening ?? '0',
                    'closing_inventory_unit' => $greenlineReport->closing ?? '0',
                    'purchase' => $greenlineReport->purchased ?? '0',
                    // 'dqi_fee' => $dqi_fee,
                    // 'dqi_per' => $dqi_per,
                    'reconciliation_date' => now(),
                ];
                $offers =$this->DQISummaryFlag($greenlineReport->sku,$greenlineReport->barcode,$greenlineReport->name); // Get the offers

                if (!empty($offers)) {
                    $cleanSheetData['offer_id'] = $offers->id;
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
                    $lpName = Offer::find($offer->id)->lp->name ?? null; // Assuming you have a relationship set up in Offer model
    
                    // Handle offer data if product not found
                    $dqi_fee = $this->calculateDqiFee($greenlineReport, $offer);
                    $dqi_per = $this->calculateDqiPer($greenlineReport, $offer);
        
                    $cleanSheetData = [
                        'retailer_id' => $retailer_id,
                        'lp_id' => $offer->lp_id,
                        'report_id' => $report->id,
                        'retailer_name' => $retailerName, // Use fetched Retailer name
                        'lp_name' => $lpName, // Use fetched LP name
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
                        'sold' => $greenlineReport->sold,
                        'purchase' => $report->purchase,
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'Greenline',
                        'pos_report_id' => $greenlineReport->id,
                        'comment' => 'Record found in the Offers Table',
                        'opening_inventory_unit' => $greenlineReport->opening ?? '0',
                        'closing_inventory_unit' => $greenlineReport->closing ?? '0',
                        'purchase' => $greenlineReport->purchased ?? '0',
                        'dqi_fee' => $dqi_fee,
                        'dqi_per' => $dqi_per,
                        'reconciliation_date' => now(),
                    ];
        
                    // Call saveToCleanSheet after finding the offer
                    $this->saveToCleanSheet($cleanSheetData);
                } else {
                    // If neither product nor offer is found, save report data as is
                    Log::info('No product or offer found, saving report data as is:', ['report_data' => $report]);
    
                    $cleanSheetData = [
                        'retailer_id' => $retailer_id,
                        'lp_id' => null, // or some default value if needed
                        'report_id' => $report->id,
                        'retailer_name' => $retailerName, // Use fetched Retailer name or null
                        'lp_name' => $lpName, // Use fetched LP name or null
                        'thc_range' => null, // or some default value if needed
                        'cbd_range' => null, // or some default value if needed
                        'size_in_gram' => null, // or some default value if needed
                        'location' => $location,
                        'province' => null, // or some default value if needed
                        'province_slug' => null, // or some default value if needed
                        'sku' => $sku,
                        'product_name' => $greenlineReport->name,
                        'category' => null, // or some default value if needed
                        'brand' => null, // or some default value if needed
                        'sold' => $greenlineReport->sold,
                        'purchase' => $report->purchase,
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'Greenline',
                        'pos_report_id' => $greenlineReport->id,
                        'comment' => 'No matching product or offer found.',
                        'opening_inventory_unit' => $greenlineReport->opening ?? '0',
                        'closing_inventory_unit' => $greenlineReport->closing ?? '0',
                        'purchase' => $greenlineReport->purchased ?? '0',
                        'dqi_fee' => null, // or some default value if needed
                        'dqi_per' => null, // or some default value if needed
                        'reconciliation_date' => now(),
                    ];
        
                    $this->saveToCleanSheet($cleanSheetData);
                }
            }
        }
    }
    
    
    /**
     * Save data to CleanSheet.
     *
     * @param array $cleanSheetData
     * @return void
     */
    public function saveToCleanSheet($cleanSheetData)  
    {
        try {
            CleanSheet::insert($cleanSheetData);
            Log::info('Data saved to CleanSheet successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving data to CleanSheet:', ['error' => $e->getMessage()]);
        }
    }
}
