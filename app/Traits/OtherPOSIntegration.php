<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\OtherPOSReport;
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
            $lpName = null; 
            $retailerName = null; 

            $retailer = Retailer::find($retailer_id);
            if ($retailer) {
                $retailerName = trim("{$retailer->first_name} {$retailer->last_name}");
            } else {
                Log::warning('Retailer not found:', ['retailer_id' => $retailer_id]);
            }

    
            if (!empty($sku)) {
                $product = $this->matchICSku($sku);
            } if (!empty($otherPOSReport->productname) && empty($product)) {
                $product = $this->matchICProductName($otherPOSReport->productname);
            if ($product) {
        
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;

             
                $lpName = Product::find($product->id)->lp->name ?? null; 

          
                $dqi_fee = $this->calculateDqiFee($otherPOSReport, $product);
                $dqi_per = $this->calculateDqiPer($otherPOSReport, $product);

                $cleanSheetData = [
                    'retailer_id' => $retailer_id,
                    // 'lp_id' => $product->lp_id,
                    'report_id' => $report->id,
                    'retailer_name' => $retailerName, 
                    'lp_name' => $lpName, 
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
                    // 'dqi_fee' => $dqi_fee,
                    // 'dqi_per' => $dqi_per,
                    'reconciliation_date' => now(),
                ];
                $offers =$this->DQISummaryFlag($otherPOSReport->sku,$otherPOSReport->barcode,$otherPOSReport->name); // Get the offers

                if (!empty($offers)) {
                    if((int) $cleanSheetData['purchased'] > 0){
                        $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offers->lp_id,$offers->lp,$offers->provincial,$product);
                        $cleanSheet['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    }
                    else{
                        $cleanSheet['c_flag'] = '';
                    }
                    $cleanSheetData['offer_id'] = $offers->id;
                    $cleanSheetData['lp_id'] = $product->lp_id;
                    $cleanSheetData['dqi_fee'] = $dqi_fee;
                    $cleanSheetData['dqi_per'] = $dqi_per;
                }
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

                    $dqi_fee = $this->calculateDqiFee($otherPOSReport, $offer);
                    $dqi_per = $this->calculateDqiPer($otherPOSReport, $offer);

                    $cleanSheetData = [
                        'retailer_id' => $retailer_id,
                        'lp_id' => $offer->lp_id,
                        'report_id' => $report->id,
                        'offer_id' => $offer->id,
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

  
}
}