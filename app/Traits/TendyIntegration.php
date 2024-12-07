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
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00'; $cleanSheetData['product_price'] = '0.00'; $cleanSheetData['average_cost'] = '0.00';
        $cleanSheetData['offer_gtin_matched'] = '0'; $cleanSheetData['offer_sku_matched'] = '0';
        $cleanSheetData['address_id'] = $report->address_id; $cleanSheetData['created_at'] = now(); $cleanSheetData['updated_at'] = now();
        $retailer_id = $tendyDaignosticReport->report->retailer_id ?? null;
        $location = $tendyDaignosticReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $tendyDaignosticReport->product_sku;
        $gtin = null;
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
//        if (empty($sku) && empty($product)){
//            $product = $this->matchICProductName($tendyDaignosticReport->TendySalesSummaryReport->product,$provinceName,$provinceSlug,$provinceId, $lpId);
//        }
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
            $cleanSheetData['product_name'] = $productName;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $tendyDaignosticReport->sold;
            $cleanSheetData['purchase'] = $tendyDaignosticReport->quantity_purchased_units ?? '0';
            $cleanSheetData['average_price'] = isset($tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price) ? $tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price: '0';
            if ($tendyDaignosticReport && $tendyDaignosticReport->TendySalesSummaryReport) {
                $tendyAverageCost = $this->tendyAverageCost($tendyDaignosticReport->TendySalesSummaryReport->cost_of_goods_sold,$tendyDaignosticReport->TendySalesSummaryReport->net_qty_sold);
            }else{
                $tendyAverageCost = '0';
            }
            $cleanSheetData['report_price_og'] = $tendyAverageCost;
            $cleanSheetData['product_price'] = GeneralFunctions::formatAmountValue($product->price_per_unit) ?? '0.00';
            $cleanSheetData['barcode'] = $gtin;
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
            list($cleanSheetData, $offer) = $this->DQISummaryFlag($cleanSheetData,$report,$tendyDaignosticReport->product_sku,'',$tendyDaignosticReport->productname,$provinceName,$provinceSlug,$provinceId,$lpId );
            if (!empty($offer)) {
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($offer->unit_cost) ?? "0.00";
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    $cleanSheetData['carveout_id'] = $checkCarveout->id ?? null;
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                    $cleanSheetData['carveout_id'] = null;
                }
                $cleanSheetData['dqi_flag'] = 1;
                $cleanSheetData['flag'] = '3';
                $cleanSheetData['comment'] = 'Record found in the Product Catalog and Offer';
                $calculatedDQI = $this->calculateDQI($cleanSheetData['purchase'],$cleanSheetData['average_cost'],$offer->data_fee);
                $cleanSheetData['dqi_per'] = $calculatedDQI['dqi_per'];
                $cleanSheetData['dqi_fee'] = $calculatedDQI['dqi_fee'];
            }
            else{
                $cleanSheetData['offer_id'] = null;
                $cleanSheetData['carveout_id'] = null;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['dqi_flag'] = 0;
                $cleanSheetData['average_cost'] = '0.00';
                $cleanSheetData['flag'] = '1';
                $cleanSheetData['comment'] = 'Record found in the Product Catalog';
            }
        } else {
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$tendyDaignosticReport->product_sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id, $lpId);
            }
//            if (empty($sku) && empty($offer)) {
//                $offer = $this->matchOfferProductName($report->date,$productName,$provinceName,$provinceSlug,$provinceId,$report->retailer_id, $lpId);
//            }
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
                $cleanSheetData['province_id'] = $provinceId ;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $productName;
                $cleanSheetData['category'] = $offer->category;
                $cleanSheetData['brand'] = $offer->brand;
                $cleanSheetData['sold'] = $tendyDaignosticReport->sold;
                $cleanSheetData['purchase'] = $tendyDaignosticReport->quantity_purchased_units ?? '0';
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    $cleanSheetData['carveout_id'] = $checkCarveout->id ?? null;
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                    $cleanSheetData['carveout_id'] = null;
                }
                $cleanSheetData['average_price'] = isset($tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price) ? $tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price: '0';
                if ($tendyDaignosticReport && $tendyDaignosticReport->TendySalesSummaryReport) {
                    $tendyAverageCost = $this->tendyAverageCost($tendyDaignosticReport->TendySalesSummaryReport->cost_of_goods_sold,$tendyDaignosticReport->TendySalesSummaryReport->net_qty_sold);
                }else{
                    $tendyAverageCost = '0';
                }
                $cleanSheetData['report_price_og'] = $tendyAverageCost;
                $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue(trim(str_replace('$', '', trim($offer->unit_cost)))) ?? '0.00';
                $cleanSheetData['product_price'] = '0.00';
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['transfer_in'] = $tendyDaignosticReport->quantity_purchased_units_transfer ?? '0';
                $cleanSheetData['transfer_out'] = $tendyDaignosticReport->quantity_sold_units_transfer ?? '0';
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $tendyDaignosticReport->opening_inventory_units ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $tendyDaignosticReport->closing_inventory_units ?? '0';
                $cleanSheetData['flag'] = '2';
                $cleanSheetData['dqi_flag'] = 1;
                $cleanSheetData['product_variation_id'] = null;
                $calculatedDQI = $this->calculateDQI($cleanSheetData['purchase'],$cleanSheetData['average_cost'],$offer->data_fee);
                $cleanSheetData['dqi_per'] = $calculatedDQI['dqi_per'];
                $cleanSheetData['dqi_fee'] = $calculatedDQI['dqi_fee'];
                $cleanSheetData['comment'] = 'Record found in the Offers';
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
                $cleanSheetData['purchase'] = $tendyDaignosticReport->quantity_purchased_units ?? '0';
                $cleanSheetData['average_price'] = isset($tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price) ? $tendyDaignosticReport->TendySalesSummaryReport->avg_retail_price: '0';
                if ($tendyDaignosticReport && $tendyDaignosticReport->TendySalesSummaryReport) {
                    $tendyAverageCost = $this->tendyAverageCost($tendyDaignosticReport->TendySalesSummaryReport->cost_of_goods_sold,$tendyDaignosticReport->TendySalesSummaryReport->net_qty_sold);
                }else{
                    $tendyAverageCost = '0';
                }
                $cleanSheetData['report_price_og'] = $tendyAverageCost;
                $cleanSheetData['average_cost'] = '0.00';
                $cleanSheetData['product_price'] = '0.00';
                $cleanSheetData['barcode'] = $gtin;
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
                $cleanSheetData['carveout_id'] = null;
                $cleanSheetData['dqi_per'] = 0.00;
                $cleanSheetData['dqi_fee'] = 0.00;
            }
        }
        $cleanSheetData['sold'] = $this->sanitizeNumeric($cleanSheetData['sold']);
        $cleanSheetData['purchase'] = $this->sanitizeNumeric($cleanSheetData['purchase']);
        $cleanSheetData['average_price'] = $this->sanitizeNumeric($cleanSheetData['average_price']);
        $cleanSheetData['report_price_og'] = $this->sanitizeNumeric($cleanSheetData['report_price_og']);
        $cleanSheetData['average_cost'] = $this->sanitizeNumeric($cleanSheetData['average_cost']);

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
