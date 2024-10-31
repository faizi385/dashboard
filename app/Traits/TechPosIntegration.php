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
use App\Models\TechPOSReport;
use Illuminate\Support\Facades\Log;

trait TechPosIntegration
{
    /**
     * Process TechPos reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapTechPosCatalouge($techPOSReport,$report)
    {
        Log::info('Processing TechPos reports:', ['report' => $report]);
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $techPOSReport->report->retailer_id ?? null;
        $location = $techPOSReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $techPOSReport->sku;
        $gtin = '';
        $productName = $techPOSReport->productname;
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

        if (!empty($sku)) {
        $product = $this->matchICSku($techPOSReport->sku,$provinceName,$provinceSlug,$provinceId);
        }
        if (!empty($productName) && empty($product)){
            $product = $this->matchICProductName($techPOSReport->productname,$provinceName,$provinceSlug,$provinceId);
        }
        if ($product) {
            $lp = Lp::where('id',$product->lp_id)->first();
            $lpName = $lp->name ?? null;
            $lpId = $lp->id ?? null;

            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $techPOSReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] =  $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] = $productName;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $techPOSReport->sold;
            $cleanSheetData['purchase'] = $techPOSReport->purchased ?? '0';
            $cleanSheetData['average_price'] = $this->techpos_averge_price($techPOSReport);
            $techPOSAverageCost = \App\Helpers\GeneralFunctions::formatAmountValue($techPOSReport->costperunit) ?? '0';
            if ($techPOSAverageCost != "0.00" && $techPOSAverageCost != '0' && (float)$techPOSAverageCost != 0.00) {
                $cleanSheetData['average_cost'] = $techPOSAverageCost;
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
            }
            else{
                if($product->price_per_unit != "0.00") {
                    $cleanSheetData['average_cost'] = $product->price_per_unit;
                }
                else{
                    $cleanSheetData['average_cost'] = "0.00";
                }
            }
            $cleanSheetData['barcode'] = $gtin;
            $cleanSheetData['report_id'] = $report->id;
            if($techPOSReport->quantitytransferinunits > 0 || $techPOSReport->otheradditionsunits){
                $cleanSheetData['transfer_in'] = $techPOSReport->quantitytransferinunits + $techPOSReport->otheradditionsunits ;
            }
            else{
                $cleanSheetData['transfer_in'] = 0;
            }

            if($techPOSReport->quantitytransferoutunits > 0 || $techPOSReport->otherreductionsunits > 0){
                $cleanSheetData['transfer_out'] = $techPOSReport->quantitytransferoutunits + $techPOSReport->otherreductionsunits ;
            }
            else{
                $cleanSheetData['transfer_out'] = 0 ;
            }
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $techPOSReport->opening ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $techPOSReport->closing ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            $offer = $this->DQISummaryFlag($report,$techPOSReport->sku,'',$techPOSReport->productname,$provinceName,$provinceSlug,$provinceId);
            if (!empty($offer)) {
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['lp_id'] = $offer->lp_id;
                $cleanSheetData['lp_name'] = $offer->lp_name;
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offer->lp_id,$offer->lp_name,$offer->provincial_sku);
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
                if( $cleanSheetData['average_cost'] == '0.00' && (int) $cleanSheetData['average_cost'] == 0){
                    $cleanSheetData['average_cost'] = \App\Helpers\GeneralFunctions::formatAmountValue($offer->unit_cost) ?? "0.00";
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
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id);
            }
            if (!empty($productName) && empty($offer)) {
                $offer = $this->matchOfferProductName($report->date,$productName,$provinceName,$provinceSlug,$provinceId,$report->retailer_id);
            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $techPOSReport->id;
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
                $cleanSheetData['sold'] = $techPOSReport->sold;
                $cleanSheetData['purchase'] = $techPOSReport->purchased ?? '0';
                $cleanSheetData['average_price'] = $this->techpos_averge_price($techPOSReport);
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offer->lp_id,$offer->lp_name,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }
                $techPOSAverageCost =\App\Helpers\GeneralFunctions::formatAmountValue($techPOSReport->costperunit) ?? '0';
                if ($techPOSAverageCost != "0.00" && $techPOSAverageCost != '0' && (float)$techPOSAverageCost != 0.00) {
                    $cleanSheetData['average_cost'] = $techPOSAverageCost;
                    $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                }
                else{
                    $techPOSAverageCost =\App\Helpers\GeneralFunctions::formatAmountValue($offer->unit_cost);
                    if($offer->unit_cost != "0.00" && $offer->unit_cost != "0" && (float)$offer->unit_cost != 0.00) {
                        $cleanSheetData['average_cost'] = $offer->unit_cost;
                    }
                    else{
                        $techPOSAverageCost = "0.00";
                        $cleanSheetData['average_cost'] = "0.00";
                    }
                }
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                if($techPOSReport->quantitytransferinunits > 0 || $techPOSReport->otheradditionsunits){
                    $cleanSheetData['transfer_in'] = $techPOSReport->quantitytransferinunits + $techPOSReport->otheradditionsunits ;
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                }

                if($techPOSReport->quantitytransferoutunits > 0 || $techPOSReport->otherreductionsunits > 0){
                    $cleanSheetData['transfer_out'] = $techPOSReport->quantitytransferoutunits + $techPOSReport->otherreductionsunits ;
                }
                else{
                    $cleanSheetData['transfer_out'] = 0 ;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $techPOSReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $techPOSReport->closing ?? '0';
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
                $cleanSheetData['pos_report_id'] = $techPOSReport->id;
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
                $cleanSheetData['product_name'] = $productName;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $techPOSReport->sold;
                $cleanSheetData['purchase'] = $techPOSReport->purchased ?? '0';
                $cleanSheetData['average_price'] = $techPOSReport->average_price;
                $cleanSheetData['average_cost'] = $techPOSReport->average_cost;
                $cleanSheetData['report_price_og'] = $techPOSReport->average_cost;
                $cleanSheetData['barcode'] = null;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['report_id'] = $report->id;
                if($techPOSReport->quantitytransferinunits > 0 || $techPOSReport->otheradditionsunits){
                    $cleanSheetData['transfer_in'] = $techPOSReport->quantitytransferinunits + $techPOSReport->otheradditionsunits ;
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                }

                if($techPOSReport->quantitytransferoutunits > 0 || $techPOSReport->otherreductionsunits > 0){
                    $cleanSheetData['transfer_out'] = $techPOSReport->quantitytransferoutunits + $techPOSReport->otherreductionsunits ;
                }
                else{
                    $cleanSheetData['transfer_out'] = 0 ;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $techPOSReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $techPOSReport->closing ?? '0';
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

    public function techpos_averge_price($techPOSReport){

        $quantitysoldunits =  (double)trim($techPOSReport->quantitysoldunits);
        $quantitysoldvalue =  \App\Helpers\GeneralFunctions::formatAmountValue($techPOSReport->quantitysoldvalue);
        if ($quantitysoldunits == '0' || $quantitysoldunits == '0.00') {
            $quantitysoldunits = 1;
        }
        $average_price = $quantitysoldvalue / $quantitysoldunits;

        return $average_price;
    }
}
