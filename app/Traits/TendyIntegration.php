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
use App\Models\tendyDaignosticReport;
use Illuminate\Support\Facades\Log;

trait TendyIntegration
{
    /**
     * Process TechPos reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapTendyPosCatalouge($tendyDaignosticReport,$report)
    {
        Log::info('Processing Tendy reports:', ['report' => $report]);
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $tendyDaignosticReport->report->retailer_id ?? null;
        $location = $tendyDaignosticReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $tendyDaignosticReport->product_sku;
        $gtin = '';
        $productName = $tendyDaignosticReport->productname;
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

        if (!empty($sku)) {
        $product = $this->matchICSku($tendyDaignosticReport->product_sku,$provinceName,$provinceSlug,$provinceId, $lpId);
        }
        if (empty($sku) && empty($product)){
            $product = $this->matchICProductName($tendyDaignosticReport->TendySalesSummaryReport->product,$provinceName,$provinceSlug,$provinceId, $lpId);
        }
        if ($product) {
            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $tendyDaignosticReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] =  $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['province_id'] =  $provinceId ;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] = $product->product_name;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $tendyDaignosticReport->sold;
            $cleanSheetData['purchase'] = $tendyDaignosticReport->purchased ?? '0';
            $cleanSheetData['average_price'] = isset($tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price) ? $tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price: '0';
            if ($tendyDaignosticReport && $tendyDaignosticReport->TendySalesSummaryReport) {
                $tendyAverageCost = $this->tendyAverageCost($tendyDaignosticReport->TendySalesSummaryReport->cost_of_goods_sold,$tendyDaignosticReport->TendySalesSummaryReport->net_qty_sold);
                if($tendyAverageCost == "0.00"){
                    $tendyAverageCost = $product->price_per_unit;
                }
            }else{
                $tendyAverageCost = '0';
            }
            $cleanSheetData['average_cost'] = $tendyAverageCost;
            $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
            $cleanSheetData['barcode'] = null;
            $cleanSheetData['report_id'] = $report->id;
            $cleanSheetData['transfer_in'] = $tendyDaignosticReport->quantity_purchased_units_transfer ?? '0';
            $cleanSheetData['transfer_out'] = $tendyDaignosticReport->quantity_sold_units_transfer ?? '0';
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $tendyDaignosticReport->opening_inventory_units ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $tendyDaignosticReport->closing_inventory_units ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            $offer = $this->DQISummaryFlag($report,$tendyDaignosticReport->product_sku,'',$tendyDaignosticReport->productname,$provinceName,$provinceSlug,$provinceId,$lpId );
            if (!empty($offer)) {
                $cleanSheetData['offer_id'] = $offer->id;
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    $cleanSheetData['carveout_id'] = $checkCarveout->id ?? null;
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }
                $cleanSheetData['dqi_flag'] = 1;
                $cleanSheetData['flag'] = '3';

                $cleanSheetData['comment'] = 'Record found in the Master Catalog and Offer';
                if( $cleanSheetData['average_cost'] == '0.00' && (int) $cleanSheetData['average_cost'] == 0){
                    $cleanSheetData['average_cost'] = \App\Helpers\GeneralFunctions::formatAmountValue($offer->unit_cost) ?? "0.00";
                }
                $TotalQuantityGet = $cleanSheetData['purchase'];
                $TotalUnitCostGet = $cleanSheetData['average_cost'];
                $TotalPurchaseCostMake = (float)$TotalQuantityGet * (float)$TotalUnitCostGet;
                $FinalDQIFEEMake = (float)trim($offer->data_fee, '%') * 100;
                $FinalFeeInDollar = (float)$TotalPurchaseCostMake * $FinalDQIFEEMake / 100;
                $cleanSheetData['dqi_per'] = $FinalDQIFEEMake;
                $cleanSheetData['dqi_fee'] = number_format($FinalFeeInDollar,2);
            }
            else{
                $cleanSheetData['offer_id'] = null;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['dqi_flag'] = 0;
                $cleanSheetData['flag'] = '1';
                $cleanSheetData['comment'] = 'Record found in the Master Catalog';
            }
        } else {
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$tendyDaignosticReport->product_sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id, $lpId);
            }
            if (empty($sku) && empty($offer)) {
                $offer = $this->matchOfferProductName($report->date,$productName,$provinceName,$provinceSlug,$provinceId,$report->retailer_id, $lpId);
            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $tendyDaignosticReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
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
                $cleanSheetData['sold'] = $tendyDaignosticReport->sold;
                $cleanSheetData['purchase'] = $tendyDaignosticReport->purchased ?? '0';
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    $cleanSheetData['carveout_id'] = $checkCarveout->id ?? null;
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }
                $cleanSheetData['average_price'] = isset($tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price) ? $tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price: '0';
                if ($tendyDaignosticReport && $tendyDaignosticReport->TendySalesSummaryReport) {
                    $tendyAverageCost = $this->tendyAverageCost($tendyDaignosticReport->TendySalesSummaryReport->cost_of_goods_sold,$tendyDaignosticReport->TendySalesSummaryReport->net_qty_sold);
                    if($tendyAverageCost == "0.00"){
                        $tendyAverageCost = $product->price_per_unit;
                    }
                }else{
                    $tendyAverageCost = '0';
                }
                $cleanSheetData['average_cost'] = $tendyAverageCost;
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['transfer_in'] = $tendyDaignosticReport->quantity_purchased_units_transfer ?? '0';
                $cleanSheetData['transfer_out'] = $tendyDaignosticReport->quantity_sold_units_transfer ?? '0';
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $tendyDaignosticReport->opening_inventory_units ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $tendyDaignosticReport->closing_inventory_units ?? '0';
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
                $cleanSheetData['pos_report_id'] = $tendyDaignosticReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['thc_range'] = null;
                $cleanSheetData['cbd_range'] = null;
                $cleanSheetData['size_in_gram'] = null;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['province_id'] =  $provinceId ;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $productName;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $tendyDaignosticReport->sold;
                $cleanSheetData['purchase'] = $tendyDaignosticReport->purchased ?? '0';
                $cleanSheetData['average_price'] = isset($tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price) ? $tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price: '0';
                if ($tendyDaignosticReport && $tendyDaignosticReport->TendySalesSummaryReport) {
                    $tendyAverageCost = $this->tendyAverageCost($tendyDaignosticReport->TendySalesSummaryReport->cost_of_goods_sold,$tendyDaignosticReport->TendySalesSummaryReport->net_qty_sold);
                }else{
                    $tendyAverageCost = '0';
                }
                $cleanSheetData['average_cost'] = $tendyAverageCost;
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                $cleanSheetData['barcode'] = null;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['transfer_in'] = $tendyDaignosticReport->quantity_purchased_units_transfer ?? '0';
                $cleanSheetData['transfer_out'] = $tendyDaignosticReport->quantity_sold_units_transfer ?? '0';
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $tendyDaignosticReport->opening_inventory_units ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $tendyDaignosticReport->closing_inventory_units ?? '0';
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

    public function tendyAverageCost ($cost_of_goods_sold,$net_qty_sold){
        $cost_of_goods_sold = GeneralFunctions::formatAmountValue($cost_of_goods_sold);
        $net_qty_sold = (double)trim($net_qty_sold);
        if(!empty($cost_of_goods_sold) && (int)$net_qty_sold > 0 && $cost_of_goods_sold != '0' && $cost_of_goods_sold != '0.00' && $net_qty_sold != '0'){
            $tendyAverageCost = ($cost_of_goods_sold/$net_qty_sold);
        } elseif(!empty($cost_of_goods_sold) && (int)$net_qty_sold < 0 && $cost_of_goods_sold < 0){
            $tendyAverageCost = ($cost_of_goods_sold/$net_qty_sold);
        }
        else{
            $tendyAverageCost = "0.00";
        }
        return $tendyAverageCost;
    }
}
