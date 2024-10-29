<?php

namespace App\Traits;

use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use Illuminate\Support\Facades\Log;
use App\Models\ProfitTechInventoryLog;


trait ProfitTechIntegration
{
    use ICIntegrationTrait;

    /**
     * Process ProfitTech reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function processProfitTechReports($reports)
    {
        Log::info('Processing ProfitTech reports:', ['reports' => $reports]);

        foreach ($reports as $report) {
       
            $profitTechReport = ProfitTechInventoryLog::with('report')->find($report->id);

            if (!$profitTechReport) {
                Log::warning('ProfitTech report not found:', ['report_id' => $report->id]);
                continue;
            }

            $retailer_id = $profitTechReport->report->retailer_id ?? null;
            $location = $profitTechReport->report->location ?? null;

            if (!$retailer_id) {
                Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
                continue;
            }

            $sku = $profitTechReport->product_sku;
            $gtin = $profitTechReport->barcode;

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
            } if (!empty($profitTechReport->productname) && empty($product)) {
                $product = $this->matchICProductName($profitTechReport->productname);
            }
            if ($product) {
               
                $provinceName = $product->province;
                $province = Province::where('name', $provinceName)->first();
                $provinceSlug = $province->slug ?? null;

      
                $lpName = Product::find($product->id)->lp->name ?? null;
                
             
                $dqi_fee = $this->calculateDqiFee($profitTechReport, $product);
                $dqi_per = $this->calculateDqiPer($profitTechReport, $product);

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
                    'product_name' =>  $profitTechReport->productname,
                    'category' => $product->category,
                    'brand' => $product->brand,
                    'sold' => $profittechReports->quantity_sold_instore_units ?? '0',
                    'purchase' => $profittechReports->quantity_purchased_units ?? '0',
                    'average_price' => $report->average_price,
                    'average_cost' => $report->average_cost,
                    'report_price_og' => $report->report_price_og,
                    'barcode' => $gtin,
                    'transfer_in' => $report->transfer_in,
                    'transfer_out' => $report->transfer_out,
                    'pos' => 'ProfitTech',
                    'pos_report_id' => $profitTechReport->id,
                    'comment' => 'Record found in the Master Catalog',
                    'opening_inventory_unit' =>$profittechReports->opening_inventory_units ?? '0',
                    'closing_inventory_unit' =>  $profittechReports->closing_inventory_units ?? '0',
    
                    // 'dqi_fee' => $dqi_fee,
                    // 'dqi_per' => $dqi_per,
                    'reconciliation_date' => now(),
                ];

                $offers =$this->DQISummaryFlag($profitTechReport->product_sku,'','');

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

                $offer = !empty($sku) ? $this->matchOfferSku($sku) : null;

                if (!$offer && !empty($profitTechReport->productname)) {
                    $offer = $this->matchOfferProductName($profitTechReport->productname);
                }

                if ($offer) {
               
                    $lpName = Offer::find($offer->id)->lp->name ?? null;


                    $dqi_fee = $this->calculateDqiFee($profitTechReport, $offer);
                    $dqi_per = $this->calculateDqiPer($profitTechReport, $offer);

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
                        'sold' => $profittechReports->quantity_sold_instore_units ?? '0',
                        'purchase' => $profittechReports->quantity_purchased_units ?? '0',
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'ProfitTech',
                        'pos_report_id' => $profitTechReport->id,
                        'comment' => 'Record found in the Offers Table',
                        'opening_inventory_unit' => $profittechReports->opening_inventory_units ?? '0',
                        'closing_inventory_unit' => $profittechReports->closing_inventory_units ?? '0',
                        'purchase' => $profitTechReport->purchased ?? '0',
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
                        'sku' => $profitTechReport->$sku,
                        'product_name' => $profitTechReport->productname,
                        'category' => $profitTechReport->category,
                        'brand' => $profitTechReport->brand,
                        'sold' => $profittechReports->quantity_sold_instore_units ?? '0',
                        'purchase' =>  $profittechReports->quantity_purchased_units ?? '0',
                        'average_price' => $report->average_price,
                        'average_cost' => $report->average_cost,
                        'report_price_og' => $report->report_price_og,
                        'barcode' => $gtin,
                        'transfer_in' => $report->transfer_in,
                        'transfer_out' => $report->transfer_out,
                        'pos' => 'ProfitTech',
                        'pos_report_id' => $profitTechReport->id,
                        'comment' => 'Product not found in Master Catalog or Offers Table',
                        'opening_inventory_unit' =>$profittechReports->opening_inventory_units ?? '0',
                        'closing_inventory_unit' =>  $profittechReports->closing_inventory_units ?? '0',
                        'purchase' => $profitTechReport->purchased ?? '0',
                        'dqi_fee' => '0',
                        'dqi_per' => '0',
                        'reconciliation_date' => now(),
                    ];

                    $this->saveToCleanSheet($cleanSheetData);
                }
            }
        }
    }
}
