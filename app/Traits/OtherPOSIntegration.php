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

trait OtherPOSIntegration
{
    /**
     * Process Greenline reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapOtherPosCatalouge($OtherPOSReport,$report)
    {
        Log::info('Processing Greenline reports:', ['report' => $report]);
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $OtherPOSReport->report->retailer_id ?? null;
        $location = $OtherPOSReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $OtherPOSReport->sku;
        $gtin = $OtherPOSReport->barcode;
        $productName = $OtherPOSReport->name;
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

        $lp = Lp::where('id',$retailer->lp_id)->first();
        $cleanSheetData['lp_id'] = $lpId = $retailer->lp_id;
        $cleanSheetData['lp_name'] = $lpName = $lp->name;

        if (!empty($gtin) && !empty($sku)) {
            $product = $this->matchICBarcodeSku($OtherPOSReport->barcode,$OtherPOSReport->sku,$provinceName,$provinceSlug,$provinceId,$lpId );
        }
        if (!empty($sku) && empty($product)) {
            $product = $this->matchICSku($OtherPOSReport->sku,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if (!empty($gtin) && empty($product)) {
            $product = $this->matchICBarcode($OtherPOSReport->barcode,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if (!empty($productName) && empty($product)){
            $product = $this->matchICProductName($OtherPOSReport->name,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if ($product) {
            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $OtherPOSReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] =  $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['province_id'] =  $provinceId;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] = $product->product_name;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $OtherPOSReport->sold ?? '0';
            $cleanSheetData['purchase'] = $OtherPOSReport->purchased ?? '0';
            if(trim(str_replace('$', '', trim($OtherPOSReport->average_price))) != "0.00" && ((float)trim(str_replace('$', '', trim($OtherPOSReport->average_price))) > 0.00 || (float)trim(str_replace('$', '', trim($OtherPOSReport->average_price))) < 0.00)) {
                $cleanSheetData['average_price'] = trim(str_replace('$', '', trim($OtherPOSReport->average_price)));
            }
            else{
                $cleanSheetData['average_price'] = "0.00";
            }
            $eposAverageCost = trim(str_replace('$', '', trim($OtherPOSReport->average_cost)));
            if ($eposAverageCost != "0.00" && ((float)$eposAverageCost > 0.00 || (float)$eposAverageCost < 0.00)) {
                $cleanSheetData['average_cost'] = $eposAverageCost;
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
            if($OtherPOSReport->transfer > 0){
                $cleanSheetData['transfer_in'] = $OtherPOSReport->transfer;
                $cleanSheetData['transfer_out'] = 0;
            }
            elseif($OtherPOSReport->transfer < 0){
                $cleanSheetData['transfer_in'] = 0;
                $cleanSheetData['transfer_out'] = str_replace('-','',$OtherPOSReport->transfer);
            }
            else{
                $cleanSheetData['transfer_in'] = 0;
                $cleanSheetData['transfer_out'] = 0;
            }
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $OtherPOSReport->opening ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $OtherPOSReport->closing ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            $offer = $this->DQISummaryFlag($report,$OtherPOSReport->sku,$OtherPOSReport->barcode,$OtherPOSReport->name,$provinceName,$provinceSlug,$provinceId,$lpId );
            if (!empty($offer)) {
                $cleanSheetData['offer_id'] = $offer->id;
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
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
                $FinalDQIFEEMake = (float)trim($offer->data_fee, '%') * 100;
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
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['dqi_flag'] = 0;
                $cleanSheetData['flag'] = '1';
                $cleanSheetData['comment'] = 'Record found in the Master Catalog';
            }
        } else {
            Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,   $lpId );
            } if (!empty($gtin) && empty($offer)) {
                $offer = $this->matchOfferBarcode($report->date,$gtin,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,   $lpId );
            } if (!empty($productName) && empty($offer)) {
                $offer = $this->matchOfferProductName($report->date,$productName,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,   $lpId );
            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $OtherPOSReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['thc_range'] = $offer->thc_range;
                $cleanSheetData['cbd_range'] = $offer->cbd_range;
                $cleanSheetData['size_in_gram'] = $offer->product_size;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $offer->province;
                $cleanSheetData['province_slug'] = $offer->province_slug;
                $cleanSheetData['province_id'] =  $provinceId;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $offer->product_name;
                $cleanSheetData['category'] = $offer->category;
                $cleanSheetData['brand'] = $offer->brand;
                $cleanSheetData['sold'] = $OtherPOSReport->sold;
                $cleanSheetData['purchase'] = $OtherPOSReport->purchased ?? '0';
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offer->lp_id,$offer->lp_name,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }

                if(trim(str_replace('$', '', trim($OtherPOSReport->average_price))) != "0.00" && ((float)trim(str_replace('$', '', trim($OtherPOSReport->average_price))) > 0.00 || (float)trim(str_replace('$', '', trim($OtherPOSReport->average_price))) < 0.00)) {
                    $cleanSheetData['average_price'] = trim(str_replace('$', '', trim($OtherPOSReport->average_price)));
                }
                else{
                    $cleanSheetData['average_price'] = "0.00";
                }
                $OtherPOSReportAverageCost = trim(str_replace('$', '', trim($OtherPOSReport->average_cost)));
                if ($OtherPOSReportAverageCost != "0.00" && ((float)$OtherPOSReportAverageCost > 0.00 || (float)$OtherPOSReportAverageCost < 0.00)) {
                    $cleanSheetData['average_cost'] = $OtherPOSReportAverageCost;
                    $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                }
                else{
                    $OtherPOSReportAverageCost = trim(str_replace('$', '', trim($offer->unit_cost)));
                    if( $OtherPOSReportAverageCost != "0.00" && ((float)$OtherPOSReportAverageCost > 0.00 || (float) $OtherPOSReportAverageCost  < 0.00)) {
                        $cleanSheetData['average_cost'] =$OtherPOSReportAverageCost;
                    }
                    else{
                        $OtherPOSReportAverageCost = "0.00";
                        $cleanSheetData['average_cost'] = "0.00";
                    }
                }
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                if($OtherPOSReport->transfer > 0){
                    $cleanSheetData['transfer_in'] = $OtherPOSReport->transfer;
                    $cleanSheetData['transfer_out'] = 0;
                }
                elseif($OtherPOSReport->transfer < 0){
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = str_replace('-','',$OtherPOSReport->transfer);
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $OtherPOSReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $OtherPOSReport->closing ?? '0';
                $cleanSheetData['flag'] = '2';
                $cleanSheetData['comment'] = 'Record found in the Offers';
                $cleanSheetData['dqi_flag'] = 1;
                $cleanSheetData['product_variation_id'] = null;
                $TotalQuantityGet = $cleanSheetData['purchase'];
                $TotalUnitCostGet = $cleanSheetData['average_cost'];
                $TotalPurchaseCostMake = (float)$TotalQuantityGet * (float)$TotalUnitCostGet;
                $FinalDQIFEEMake = (float)trim($offer->data_fee, '%') * 100;
                $FinalFeeInDollar = (float)$TotalPurchaseCostMake * $FinalDQIFEEMake / 100;
                $cleanSheetData['dqi_per'] = $FinalDQIFEEMake;
                $cleanSheetData['dqi_fee'] = number_format($FinalFeeInDollar,2);
            } else {
                Log::info('No product or offer found, saving report data as is:', ['report_data' => $report]);
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = null;
                $cleanSheetData['pos_report_id'] = $OtherPOSReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['thc_range'] = null;
                $cleanSheetData['cbd_range'] = null;
                $cleanSheetData['size_in_gram'] = null;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['province_id'] =  $provinceId;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $OtherPOSReport->name;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $OtherPOSReport->sold;
                $cleanSheetData['purchase'] = $OtherPOSReport->purchased ?? '0';
                $cleanSheetData['average_price'] = trim(str_replace('$','',trim($OtherPOSReport->average_price)));
                $cleanSheetData['average_cost'] = trim(str_replace('$','',trim($OtherPOSReport->average_cost)));
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['c_flag'] = '';
                if($OtherPOSReport->transfer > 0){
                    $cleanSheetData['transfer_in'] = $OtherPOSReport->transfer;
                    $cleanSheetData['transfer_out'] = 0;
                }
                elseif($OtherPOSReport->transfer < 0){
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = str_replace('-','',$OtherPOSReport->transfer);
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $OtherPOSReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $OtherPOSReport->closing ?? '0';
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



}
