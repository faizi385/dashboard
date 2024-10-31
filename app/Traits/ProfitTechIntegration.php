<?php

namespace App\Traits;

use App\Models\Lp;
use Carbon\Carbon;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\ProductVariation;
use App\Models\ProfitTechReport;
use App\Helpers\GeneralFunctions;
use Illuminate\Support\Facades\Log;

trait ProfitTechIntegration
{
    /**
     * Process ProfitTech reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapProfitTechCatalouge($profitTechReport, $report)
    {
        Log::info('Processing ProfitTech reports:', ['report' => $report]);
        $cleanSheetData = [];
        $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $profitTechReport->report->retailer_id ?? null;
        $location = $profitTechReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $profitTechReport->product_sku;
        $gtin = $profitTechReport->barcode;
        $productName = $profitTechReport->product_name;
        $provinceId = $report->province_id;
        $provinceName = $report->province;
        $provinceSlug = $report->province_slug;
        $product = null;

        $retailer = Retailer::find($retailer_id);
        if ($retailer) {
            $retailerName = trim("{$retailer->first_name} {$retailer->last_name}");
        } else {
            Log::warning('Retailer not found:', ['retailer_id' => $retailer_id]);
        }

       
        if (!empty($sku) && empty($product)) {
            $product = $this->matchICSku($profitTechReport->product_sku, $provinceName, $provinceSlug, $provinceId);
        }
      
   

        if ($product) {
            $lp = Lp::where('id', $product->lp_id)->first();
            $lpName = $lp->name ?? null;
            $lpId = $lp->id ?? null;

            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $profitTechReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] = $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] =  $productName;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $profitTechReport->quantity_sold_instore_units ?? '0';
            $cleanSheetData['purchase'] = $profitTechReport->quantity_purchased_units ?? '0';

            $profitTechAveragePrice = trim(str_replace('$', '', trim($profitTechReport->average_price)));
            $profitTechAveragePrice = trim($profitTechReport->average_price);
            if ($profitTechAveragePrice != "0.00" && ((float)$profitTechAveragePrice > 0.00 || (float)$profitTechAveragePrice < 0.00)) {
                $cleanSheetData['average_price'] = $profitTechAveragePrice;
            } else {
                $cleanSheetData['average_price'] = "0.00";
            }

            $profitTechAverageCost = trim(str_replace('$', '', trim($profitTechReport->average_cost)));
            $profitTechAverageCost = trim($profitTechReport->average_cost);
            if ($profitTechAverageCost != "0.00" && ((float)$profitTechAverageCost > 0.00 || (float)$profitTechAverageCost < 0.00)) {
                $cleanSheetData['average_cost'] = $profitTechAverageCost;
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
            } else {
                $cleanSheetData['average_cost'] = "0.00";
            }

            $cleanSheetData['barcode'] = $gtin;
            $cleanSheetData['report_id'] = $report->id;

            if ($profitTechReport->transfer > 0) {
                $cleanSheetData['transfer_in'] = $profitTechReport->transfer;
                $cleanSheetData['transfer_out'] = 0;
            } elseif ($profitTechReport->transfer < 0) {
                $cleanSheetData['transfer_in'] = 0;
                $cleanSheetData['transfer_out'] = str_replace('-', '', $profitTechReport->transfer);
            } else {
                $cleanSheetData['transfer_in'] = 0;
                $cleanSheetData['transfer_out'] = 0;
            }

            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $profitTechReport->opening ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $profitTechReport->closing ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;

            $offer = $this->DQISummaryFlag($report, $profitTechReport->sku, $profitTechReport->barcode, $profitTechReport->name, $provinceName, $provinceSlug, $provinceId);

            if (!empty($offer)) {
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['lp_id'] = $product->lp_id;
                $cleanSheetData['lp_name'] = $offer->lp_name;
                if ((int)$cleanSheetData['purchase'] > 0) {
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName, $offer->lp_id, $offer->lp_name, $offer->provincial_sku, $product);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                } else {
                    $cleanSheetData['c_flag'] = '';
                }
                $cleanSheetData['dqi_flag'] = 1;
                $cleanSheetData['flag'] = '3';
                $TotalQuantityGet = $cleanSheetData['purchase'];
                $TotalUnitCostGet = $cleanSheetData['average_cost'];
                $TotalPurchaseCostMake = (float)$TotalQuantityGet * (float)$TotalUnitCostGet;
                $FinalDQIFEEMake = (float)trim($offer->data, '%') * 100;
                $FinalFeeInDollar = (float)$TotalPurchaseCostMake * $FinalDQIFEEMake / 100;
                $cleanSheetData['dqi_per'] = $FinalDQIFEEMake;
                $cleanSheetData['dqi_fee'] = number_format($FinalFeeInDollar, 2);
                $cleanSheetData['comment'] = 'Record found in the Master Catalog and Offer';
            } else {
                $cleanSheetData['offer_id'] = null;
                $cleanSheetData['lp_id'] = $lpId;
                $cleanSheetData['lp_name'] = $lpName;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['dqi_flag'] = 0;
                $cleanSheetData['flag'] = '1';
                $cleanSheetData['comment'] = 'Record found in the Master Catalog';
            }
        } else {
            Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id);
            } 
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $profitTechReport->id;
                $cleanSheetData['lp_id'] = $offer->lp_id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['lp_name'] = $offer->lp_name;
                $cleanSheetData['thc_range'] = $offer->thc_range;
                $cleanSheetData['cbd_range'] = $offer->cbd_range;
                $cleanSheetData['size_in_gram'] = $offer->product_size;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $offer->province;
                $cleanSheetData['province_slug'] = $offer->province_slug;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $offer->product_name;
                $cleanSheetData['category'] = $offer->category;
                $cleanSheetData['brand'] = $offer->brand;
                $cleanSheetData['sold'] = $profitTechReport->quantity_sold_instore_units ?? '0';
                $cleanSheetData['purchase'] = $profitTechReport->quantity_purchased_units ?? '0';
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offer->lp_id,$offer->lp_name,$offer->provincial_sku,$product);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }

                $profitTechAveragePrice = trim(str_replace('$', '', trim($profitTechReport->average_price)));
                $profitTechAveragePrice  = trim($profitTechReport->average_price);
                if($profitTechAveragePrice  != "0.00" && ((float)$profitTechAveragePrice > 0.00 || (float)      $profitTechAveragePrice  < 0.00)) {
                    $cleanSheetData['average_price'] =       $profitTechAveragePrice ;
                }
                else{
                    $profitTechAveragePrice = "0.00";
                    $cleanSheetData['average_price'] = "0.00";
                }
             
            $profitTechAverageCost = trim(str_replace('$', '', trim($profitTechReport->average_cost)));
            $profitTechAverageCost = trim($profitTechReport->average_cost);
                if (  $profitTechAverageCost != "0.00" && ((float)  $profitTechAverageCost > 0.00 || (float)  $profitTechAverageCost < 0.00)) {
                    $cleanSheetData['average_cost'] =  $profitTechAverageCost;
                    $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                }
                else{
                    $profitTechAverageCost = trim(str_replace('$', '', trim($offer->unit_cost)));
                    if(    $profitTechAverageCost!= "0.00" && ((float)    $profitTechAverageCost> 0.00 || (float)    $profitTechAverageCost< 0.00)) {
                        $cleanSheetData['average_cost'] =     $profitTechAverageCost;
                    }
                    else{
                        $profitTechAverageCost = "0.00";
                        $cleanSheetData['average_cost'] = "0.00";
                    }
                }
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                if($profitTechReport->transfer > 0){
                    $cleanSheetData['transfer_in'] = $profitTechReport->transfer;
                    $cleanSheetData['transfer_out'] = 0;
                }
                elseif($profitTechReport->transfer < 0){
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = str_replace('-','',$profitTechReport->transfer);
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $profitTechReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $profitTechReport->closing ?? '0';
                $cleanSheetData['flag'] = '2';
                $cleanSheetData['comment'] = 'Record found in the Offers';
                $cleanSheetData['dqi_flag'] = 1;
                $cleanSheetData['product_variation_id'] = null;
                $TotalQuantityGet = $cleanSheetData['purchase'];
                $TotalUnitCostGet = $cleanSheetData['average_cost'];
                $TotalPurchaseCostMake = (float)$TotalQuantityGet * (float)$TotalUnitCostGet;
                $FinalDQIFEEMake = (float)trim($offer->data, '%') * 100;
                $FinalFeeInDollar = (float)$TotalPurchaseCostMake * $FinalDQIFEEMake / 100;
                $cleanSheetData['dqi_per'] = $FinalDQIFEEMake;
                $cleanSheetData['dqi_fee'] = number_format($FinalFeeInDollar,2);
            } else {
                Log::info('No product or offer found, saving report data as is:', ['report_data' => $report]);
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = null;
                $cleanSheetData['pos_report_id'] = $profitTechReport->id;
                $cleanSheetData['lp_id'] = null;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['lp_name'] = null;
                $cleanSheetData['thc_range'] = null;
                $cleanSheetData['cbd_range'] = null;
                $cleanSheetData['size_in_gram'] = null;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] =  $productName;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $profitTechReport->quantity_sold_instore_units ?? '0';
                $cleanSheetData['purchase'] = $profitTechReport->quantity_purchased_units ?? '0';
                $cleanSheetData['average_price'] = $profitTechReport->average_price;
                $cleanSheetData['average_cost'] = $profitTechReport->average_cost;
                $cleanSheetData['report_price_og'] = $profitTechReport->average_cost;
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['c_flag'] = '';
                if($profitTechReport->transfer > 0){
                    $cleanSheetData['transfer_in'] = $profitTechReport->transfer;
                    $cleanSheetData['transfer_out'] = 0;
                }
                elseif($profitTechReport->transfer < 0){
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = str_replace('-','',$profitTechReport->transfer);
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $profitTechReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $profitTechReport->closing ?? '0';
                $cleanSheetData['flag'] = '0';
                $cleanSheetData['comment'] = 'No matching product or offer found.';
                $cleanSheetData['dqi_flag'] = 0;
                $cleanSheetData['product_variation_id'] = null;
                $cleanSheetData['dqi_per'] = 0.00;
                $cleanSheetData['dqi_fee'] = 0.00;
            }
        }
        $cleanSheetData['sold'] = $this->sanitizeNumeric($cleanSheetData['sold']);
        $cleanSheetData['purchase'] = $this->sanitizeNumeric($cleanSheetData['purchase']);
        $cleanSheetData['average_price'] = $this->sanitizeNumeric($cleanSheetData['average_price']);
        $cleanSheetData['report_price_og'] = $this->sanitizeNumeric($cleanSheetData['report_price_og']);
        $cleanSheetData['average_cost'] = $this->sanitizeNumeric($cleanSheetData['average_cost']);

        if ($cleanSheetData['average_cost'] === 0.0 || $cleanSheetData['average_cost'] === 0) {
            $cleanSheetData['average_cost'] = GeneralFunctions::checkAvgCostCleanSheet($cleanSheetData['sku'],$cleanSheetData['province']);
        }

        return $cleanSheetData;
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
    public function profitech_averge_price($profittechReports, $provinceName){

        $quantitysoldunits =  (double)trim($profittechReports->opening_inventory_units);
        $quantitysoldvalue = \App\Helpers\GeneralFunctions::formatAmountValue($profittechReports->opening_inventory_value);;
        if ($quantitysoldunits == '0' || $quantitysoldunits == '0.00') {
            $quantitysoldunits = 1;
        }

        if($quantitysoldunits < 0 && $quantitysoldvalue < 0){
            $average_price = $quantitysoldvalue / $quantitysoldunits;
        } else {
            $average_price = (double)($quantitysoldvalue) / (double)($quantitysoldunits);
        }

        if($average_price == 0 || $average_price == '0.00'){
            $average_price = '0.00';
        }

        return (double)$average_price;
    }
    public function profitech_averge_cost($product,$createdAt,$provinceName,$provinceSlug){
        $average_cost = 0.00;
        $createdAtMonth = Carbon::parse($createdAt)->addMonth()->format('m');
        $createdAtYear = Carbon::parse($createdAt)->addMonth()->format('Y');
        $lpOffer = ProductVariation::whereMonth('created_at', $createdAtMonth)->whereYear('created_at', $createdAtYear)
            ->where('provincial', $product->sku)
            ->where('province', $provinceName)
            ->first();
        if(empty($lpOffer)){
            $lpOffer = ProductVariation::whereMonth('created_at', $createdAtMonth)->whereYear('created_at', $createdAtYear)
                ->where('provincial', $product->sku)
                ->where('province', $provinceSlug)
                ->first();
        }
        if(!empty($lpOffer)){
            $average_cost =  \App\Helpers\GeneralFunctions::formatAmountValue($lpOffer->unit_cost);
        }
        if($average_cost == '0.00' || $average_cost == '0') {
            $average_cost =  trim(str_replace('$', '', trim($product->price_per_unit)));
        }

        return $average_cost;
    }
}
