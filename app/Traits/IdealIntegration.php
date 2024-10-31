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

trait IdealIntegration
{
    /**
     * Process TechPos reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapIdealCatalouge($idealDaignosticReport,$report)
    {
        Log::info('Processing Ideal reports:', ['report' => $report]);
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $idealDaignosticReport->report->retailer_id ?? null;
        $location = $idealDaignosticReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $idealDaignosticReport->sku;
        $gtin = '';
        $productName = $idealDaignosticReport->productname;
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
        $product = $this->matchICSku($idealDaignosticReport->sku,$provinceName,$provinceSlug,$provinceId);
        }
        if (!empty($productName) && empty($product)){
            $product = $this->matchICProductName($idealDaignosticReport->productname,$provinceName,$provinceSlug,$provinceId);
        }
        if ($product) {
            $lp = Lp::where('id',$product->lp_id)->first();
            $lpName = $lp->name ?? null;
            $lpId = $lp->id ?? null;

            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $idealDaignosticReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] =  $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['province_id'] =  $provinceId ;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] = $productName;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $idealDaignosticReport->unit_sold ?? "0";
            $cleanSheetData['purchase'] = $idealDaignosticReport->purchases ?? '0';
            if ($idealDaignosticReport->net_sales_ex && $idealDaignosticReport->unit_sold) {
                $cleanSheetData['average_price'] = $this->avgPriceForIdeal($idealDaignosticReport);
            } else {
                $cleanSheetData['average_price'] = "0.00";
            }
            // if($IdealSalesSummaryReport != null){
            //     if (!empty($IdealSalesSummaryReport->purchase_amount) && $IdealSalesSummaryReport->purchase_amount != '0' && $IdealSalesSummaryReport->purchase_amount != '0.00'
            //         && !empty($IdealSalesSummaryReport->quantity_purchased) && $IdealSalesSummaryReport->quantity_purchased != '0' && $IdealSalesSummaryReport->quantity_purchased != '0.00') {
            //         $cleanSheetData['average_cost'] = $this->avgCostForIdeal($IdealSalesSummaryReport);
            //         $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
            //     } else if ($product->price_per_unit) {
            //         $cleanSheetData['average_cost'] = $product->price_per_unit;
            //     } else {
            //         $cleanSheetData['average_cost'] = "0.00";
            //     }
            // }
            // else{
            //     $cleanSheetData['average_cost'] = "0.00";
            // }
            $cleanSheetData['barcode'] = $gtin;
            $cleanSheetData['report_id'] = $report->id;
            if ($idealDaignosticReport->trans_in > 0) {
                $cleanSheetData['transfer_in'] = $idealDaignosticReport->trans_in;
            } else {
                $cleanSheetData['transfer_in'] = 0;
            }
            if ($idealDaignosticReport->trans_out < 0) {
                $cleanSheetData['transfer_out'] = str_replace('-', '', $idealDaignosticReport->trans_out);
            } else {
                $cleanSheetData['transfer_out'] = 0;
            }
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $idealDaignosticReport->opening ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $idealDaignosticReport->closing ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            $offer = $this->DQISummaryFlag($report,$idealDaignosticReport->sku,'',$idealDaignosticReport->productname,$provinceName,$provinceSlug,$provinceId);
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
                $cleanSheetData['pos_report_id'] = $idealDaignosticReport->id;
                $cleanSheetData['lp_id'] = $offer->lp_id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['lp_name'] = $offer->lp_name;
                $cleanSheetData['thc_range'] = $offer->thc_range;
                $cleanSheetData['cbd_range'] = $offer->cbd_range;
                $cleanSheetData['size_in_gram'] = $offer->product_size;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $offer->province;
                $cleanSheetData['province_slug'] = $offer->province_slug;
                $cleanSheetData['province_id'] =  $provinceId ;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $offer->product_name;
                $cleanSheetData['category'] = $offer->category;
                $cleanSheetData['brand'] = $offer->brand;
                $cleanSheetData['sold'] = $idealDaignosticReport->unit_sold ?? "0";
                $cleanSheetData['purchase'] = $idealDaignosticReport->purchases ?? '0';
                $cleanSheetData['average_price'] = $this->avgPriceForIdeal($idealDaignosticReport);
                $cleanSheetData['average_cost'] = $this->avgCostForIdeal($IdealSalesSummaryReport) == '0.00' ? trim(str_replace('$', '', trim($offer->unit_cost))) : $this->avgCostForIdeal($IdealSalesSummaryReport);
                $cleanSheetData['report_price_og'] = $this->avgCostForIdeal($IdealSalesSummaryReport);
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                if ($idealDaignosticReport->trans_in > 0) {
                    $cleanSheetData['transfer_in'] = $idealDaignosticReport->trans_in;
                } else {
                    $cleanSheetData['transfer_in'] = 0;
                }
                if ($idealDaignosticReport->trans_out < 0) {
                    $cleanSheetData['transfer_out'] = str_replace('-', '', $idealDaignosticReport->trans_out);
                } else {
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $idealDaignosticReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $idealDaignosticReport->closing ?? '0';
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
                $cleanSheetData['pos_report_id'] = $idealDaignosticReport->id;
                $cleanSheetData['lp_id'] = null;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['lp_name'] = null;
                $cleanSheetData['thc_range'] = null;
                $cleanSheetData['cbd_range'] = null;
                $cleanSheetData['size_in_gram'] = null;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['province_id'] =  $provinceId ;
                $cleanSheetData['sku'] = $idealDaignosticReport->sku;
                $cleanSheetData['product_name'] = $idealDaignosticReport->description;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $idealDaignosticReport->unit_sold ?? "0";
            $cleanSheetData['purchase'] = $idealDaignosticReport->purchases ?? '0';
                $cleanSheetData['average_price'] = $this->avgPriceForIdeal($idealDaignosticReport);
                $cleanSheetData['average_cost'] = $this->avgCostForIdeal($IdealSalesSummaryReport);
                $cleanSheetData['report_price_og'] = $idealDaignosticReport->average_cost;
                $cleanSheetData['barcode'] = null;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['report_id'] = $report->id;
                if($idealDaignosticReport->quantitytransferinunits > 0 || $idealDaignosticReport->otheradditionsunits){
                    $cleanSheetData['transfer_in'] = $idealDaignosticReport->quantitytransferinunits + $idealDaignosticReport->otheradditionsunits ;
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                }

                if($idealDaignosticReport->quantitytransferoutunits > 0 || $idealDaignosticReport->otherreductionsunits > 0){
                    $cleanSheetData['transfer_out'] = $idealDaignosticReport->quantitytransferoutunits + $idealDaignosticReport->otherreductionsunits ;
                }
                else{
                    $cleanSheetData['transfer_out'] = 0 ;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $idealDaignosticReport->opening ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $idealDaignosticReport->closing ?? '0';
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

    private function avgPriceForIdeal($idealDaignosticReport)
    {
        $net_sales_ex = $idealDaignosticReport->net_sales_ex ? str_replace('$', '', trim($idealDaignosticReport->net_sales_ex)) : 0;
        $unit_sold = $idealDaignosticReport->unit_sold ? str_replace('$', '', trim($idealDaignosticReport->unit_sold)) : 1;
        if( (float) $unit_sold == '0.00' && (float) $unit_sold == '0') {
            $unit_sold = 1;
        }
        if( (float) $net_sales_ex != '0.00' && (float) $net_sales_ex != '0') {
            $average_price = (float)$net_sales_ex / (float)$unit_sold;
        }
        else{
            $average_price = '0.00';
        }
        return $average_price;
    }
    private function avgCostForIdeal($IdealSalesSummaryReport)
    {
        if($IdealSalesSummaryReport != null){
        $purchase_amount = str_replace('$', '', trim($IdealSalesSummaryReport->purchase_amount));
        $quantity_purchased = str_replace('$', '', trim($IdealSalesSummaryReport->quantity_purchased));

        if( (float)$quantity_purchased == '0.00' && (float)$quantity_purchased == '0') {
                    $quantity_purchased = 1;
                }
                if( (float) $purchase_amount != '0.00' && (float) $purchase_amount != '0') {
                $average_cost = (float)$purchase_amount / (float)$quantity_purchased    ;
                } else {
                    $average_cost = '0.00';
                }
            }
        else{
            $average_cost = '0.00';
        }

        return $average_cost;
    }
}
