<?php

namespace App\Traits;

use App\Helpers\GeneralFunctions;
use App\Models\Lp;
use App\Models\Offer;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\GreenlineReport;
use Illuminate\Support\Facades\Log;
// use App\Traits\ICIntegrationTrait;

trait GreenlineICIntegration
{
    // use ICIntegrationTrait;

    /**
     * Process Greenline reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapGreenlineCatalouge($greenlineReport,$report)
    {
        Log::info('Processing Greenline reports:', ['report' => $report]);
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $greenlineReport->report->retailer_id ?? null;
        $location = $greenlineReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $greenlineReport->sku;
        $gtin = $greenlineReport->barcode;
        $productName = $greenlineReport->name;
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

        if (!empty($gtin) && !empty($sku)) {
            $product = $this->matchICBarcodeSku($greenlineReport->barcode,$greenlineReport->sku,$provinceName,$provinceSlug,$provinceId);
        }
        if (!empty($sku) && empty($product)) {
            $product = $this->matchICSku($greenlineReport->sku,$provinceName,$provinceSlug,$provinceId);
        }
        if (!empty($gtin) && empty($product)) {
            $product = $this->matchICBarcode($greenlineReport->barcode,$provinceName,$provinceSlug,$provinceId);
        }
        if (!empty($productName) && empty($product)){
            $product = $this->matchICProductName($greenlineReport->name,$provinceName,$provinceSlug,$provinceId);
        }
        if ($product) {
            $lp = Lp::where('id',$product->lp_id)->first();
            $lpName = $lp->name ?? null;
            $lpId = $lp->id ?? null;

            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $greenlineReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] =  $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] = $greenlineReport->name;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $greenlineReport->sold;
            $cleanSheetData['purchase'] = $greenlineReport->purchased ?? '0';
            $greenlineAveragePrice = trim(str_replace('$', '', trim($greenlineReport->average_price)));
            $greenlineAveragePrice = trim($greenlineReport->average_price);
            if($greenlineAveragePrice != "0.00" && ((float)$greenlineAveragePrice > 0.00 || (float)$greenlineAveragePrice < 0.00)) {
                $cleanSheetData['average_price'] = $greenlineAveragePrice;
            }
            else{
                $greenlineAveragePrice = 0.00;
                $cleanSheetData['average_price'] = 0.00;
            }
            $greenlineAverageCost = trim(str_replace('$', '', trim($greenlineReport->average_cost)));
            $greenlineAverageCost = trim($greenlineReport->average_cost);
            if ($greenlineAverageCost != "0.00" && ((float)$greenlineAverageCost > 0.00 || (float)$greenlineAverageCost < 0.00)) {
                $cleanSheetData['average_cost'] = $greenlineAverageCost;
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
            }
            else{
                if($product->price_per_unit != "0.00" && ((float)$product->price_per_unit > 0.00 || (float)$product->price_per_unit < 0.00)) {
                    $cleanSheetData['average_cost'] = $product->price_per_unit;
                }
                else{
                    $cleanSheetData['average_cost'] = "0.00";
                }
            }
            $cleanSheetData['barcode'] = $gtin;
            $cleanSheetData['report_id'] = $report->id;
            if($greenlineReport->transfer > 0){
                $cleanSheetData['transfer_in'] = $greenlineReport->transfer;
                $cleanSheetData['transfer_out'] = 0;
            }
            elseif($greenlineReport->transfer < 0){
                $cleanSheetData['transfer_in'] = 0;
                $cleanSheetData['transfer_out'] = str_replace('-','',$greenlineReport->transfer);
            }
            else{
                $cleanSheetData['transfer_in'] = 0;
                $cleanSheetData['transfer_out'] = 0;
            }
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $greenlineReport->opening ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $greenlineReport->closing ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            $offer = $this->DQISummaryFlag($report,$greenlineReport->sku,$greenlineReport->barcode,$greenlineReport->name,$provinceName,$provinceSlug,$provinceId);
            if (!empty($offer)) {
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['lp_id'] = $product->lp_id;
                $cleanSheetData['lp_name'] = $offer->lp_name;
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offer->lp_id,$offer->lp_name,$offer->provincial_sku,$product);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
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
                $cleanSheetData['dqi_fee'] = number_format($FinalFeeInDollar,2);
                $cleanSheetData['comment'] = 'Record found in the Master Catalog and Offer';
                if( $cleanSheetData['average_cost'] == '0.00' && (int)$cleanSheetData['average_cost'] == 0){
                    $cleanSheetData['average_cost'] = $offers->unit_cost ?? "0.00";
                }
            }
            else{
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
            } if (!empty($gtin) && empty($offer)) {
                $offer = $this->matchOfferBarcode($report->date,$gtin,$provinceName,$provinceSlug,$provinceId,$report->retailer_id);
            } if (!empty($productName) && empty($offer)) {
                $offer = $this->matchOfferProductName($report->date,$productName,$provinceName,$provinceSlug,$provinceId,$report->retailer_id);
            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $greenlineReport->id;
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
                $cleanSheetData['sold'] = $greenlineReport->sold;
                $cleanSheetData['purchase'] = $greenlineReport->purchased ?? '0';
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offer->lp_id,$offer->lp_name,$offer->provincial_sku,$product);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }

                $greenlineAveragePrice = trim(str_replace('$', '', trim($greenlineReport->average_price)));
                $greenlineAveragePrice = trim($greenlineReport->average_price);
                if($greenlineAveragePrice != "0.00" && ((float)$greenlineAveragePrice > 0.00 || (float)$greenlineAveragePrice < 0.00)) {
                    $cleanSheetData['average_price'] = $greenlineAveragePrice;
                }
                else{
                    $greenlineAveragePrice = "0.00";
                    $cleanSheetData['average_price'] = "0.00";
                }
                $greenlineAverageCost = trim(str_replace('$', '', trim($greenlineReport->average_cost)));
                $greenlineAverageCost = trim($greenlineReport->average_cost);
                if ($greenlineAverageCost != "0.00" && ((float)$greenlineAverageCost > 0.00 || (float)$greenlineAverageCost < 0.00)) {
                    $cleanSheetData['average_cost'] = $greenlineAverageCost;
                    $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                }
                else{
                    $greenlineAverageCost = trim(str_replace('$', '', trim($offer->unit_cost)));
                    if($greenlineAverageCost != "0.00" && ((float)$greenlineAverageCost > 0.00 || (float)$greenlineAverageCost < 0.00)) {
                        $cleanSheetData['average_cost'] = $greenlineAverageCost;
                    }
                    else{
                        $greenlineAverageCost = "0.00";
                        $cleanSheetData['average_cost'] = "0.00";
                    }
                }
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                if($greenlineReport->transfer > 0){
                    $cleanSheetData['transfer_in'] = $greenlineReport->transfer;
                    $cleanSheetData['transfer_out'] = 0;
                }
                elseif($greenlineReport->transfer < 0){
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = str_replace('-','',$greenlineReport->transfer);
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $greenlineReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $greenlineReport->closing ?? '0';
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
                $cleanSheetData['pos_report_id'] = $greenlineReport->id;
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
                $cleanSheetData['product_name'] = $greenlineReport->name;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $greenlineReport->sold;
                $cleanSheetData['purchase'] = $greenlineReport->purchased ?? '0';
                $cleanSheetData['average_price'] = $greenlineReport->average_price;
                $cleanSheetData['average_cost'] = $greenlineReport->average_cost;
                $cleanSheetData['report_price_og'] = $greenlineReport->average_cost;
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['c_flag'] = '';
                if($greenlineReport->transfer > 0){
                    $cleanSheetData['transfer_in'] = $greenlineReport->transfer;
                    $cleanSheetData['transfer_out'] = 0;
                }
                elseif($greenlineReport->transfer < 0){
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = str_replace('-','',$greenlineReport->transfer);
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $greenlineReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $greenlineReport->closing ?? '0';
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
}
