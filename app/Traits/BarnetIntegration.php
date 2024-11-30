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

trait BarnetIntegration
{
    /**
     * Process Greenline reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapBarnetCatalouge($barnetReport, $report)
    {
        Log::info('Processing Barnet reports:', ['report' => $report]);
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00'; $cleanSheetData['product_price'] = '0.00'; $cleanSheetData['average_cost'] = '0.00';
        $cleanSheetData['offer_gtin_matched'] = '0'; $cleanSheetData['offer_sku_matched'] = '0';
        $cleanSheetData['address_id'] = $report->address_id; $cleanSheetData['created_at'] = now(); $cleanSheetData['updated_at'] = now();
        $retailer_id = $barnetReport->report->retailer_id ?? null;
        $location = $barnetReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $barnetReport->product_sku;
        $gtin = null;
        $productName = $barnetReport->description;
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
            $product = $this->matchICBarcodeSku($barnetReport->barcode,$barnetReport->product_sku,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if (!empty($sku) && empty($product)) {
            $product = $this->matchICSku($barnetReport->product_sku,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if (!empty($gtin) && empty($product)) {
            $product = $this->matchICBarcode($barnetReport->barcode,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
//        if (!empty($productName) && empty($product)){
//            $product = $this->matchICProductName($barnetReport->description,$provinceName,$provinceSlug,$provinceId,$lpId);
//        }
        if ($product) {
            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $barnetReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] =  $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province_id'] = $provinceId;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] = $productName ?? $product->product_name;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $barnetReport->quantity_sold_units ?? '0';
            $cleanSheetData['purchase'] = $barnetReport->quantity_purchased_units ?? '0';
            $barnetAveragePrice = $this->barnetAveragePrice($barnetReport->quantity_sold_value,$barnetReport->quantity_sold_units);
            $cleanSheetData['average_price'] = $barnetAveragePrice;
            $barnetAverageCost = $this->barnetAverageCost($barnetReport->opening_inventory_value,$barnetReport->opening_inventory_units);
            $cleanSheetData['report_price_og'] = $barnetAverageCost;
            if($product->price_per_unit != "0.00" && $product->price_per_unit != "0" && !empty($product->price_per_unit)){
                $cleanSheetData['product_price'] = GeneralFunctions::formatAmountValue($product->price_per_unit) ?? '0.00';
            }
            $cleanSheetData['barcode'] = $gtin;
            $cleanSheetData['report_id'] = $report->id;
            $cleanSheetData['transfer_in'] = $barnetReport->other_additions_units ?? '0';
            $cleanSheetData['transfer_out'] = $barnetReport->transfer_units ?? '0';
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $barnetReport->opening_inventory_units ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $barnetReport->closing_inventory_units ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            list($cleanSheetData, $offer) = $this->DQISummaryFlag($cleanSheetData,$report,$sku,'',$productName,$provinceName,$provinceSlug,$provinceId,$lpId);
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
                $calculatedDQI = $this->calculateDQI($cleanSheetData['purchase'],$cleanSheetData['average_cost'],$offer->data_fee);
                $cleanSheetData['dqi_per'] = $calculatedDQI['dqi_per'];
                $cleanSheetData['dqi_fee'] = $calculatedDQI['dqi_fee'];
                $cleanSheetData['comment'] = 'Record found in the Product Catalog and Offer';
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
            Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,$lpId);
                if(!empty($offer)){
                    $cleanSheetData['offer_sku_matched'] = '1';
                }
            }
//            if (!empty($productName) && empty($offer)) {
//                $offer = $this->matchOfferProductName($report->date,$productName,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,$lpId);
//            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $barnetReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['thc_range'] = $offer->thc_range;
                $cleanSheetData['cbd_range'] = $offer->cbd_range;
                $cleanSheetData['size_in_gram'] = $offer->product_size;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province_id'] = $provinceId;
                $cleanSheetData['province'] = $offer->province;
                $cleanSheetData['province_slug'] = $offer->province_slug;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $productName;
                $cleanSheetData['category'] = $offer->category;
                $cleanSheetData['brand'] = $offer->brand;
                $cleanSheetData['sold'] = $barnetReport->quantity_sold_units ?? '0';
                $cleanSheetData['purchase'] = $barnetReport->quantity_purchased_units ?? '0';
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    $cleanSheetData['carveout_id'] = $checkCarveout->id ?? null;
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                    $cleanSheetData['carveout_id'] = null;
                }
                $barnetAveragePrice = $this->barnetAveragePrice($barnetReport->quantity_sold_value,$barnetReport->quantity_sold_units);
                $cleanSheetData['average_price'] = $barnetAveragePrice;
                $barnetAverageCost = $this->barnetAverageCost($barnetReport->opening_inventory_value,$barnetReport->opening_inventory_units);
                $cleanSheetData['report_price_og'] = $barnetAverageCost;
                $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($offer->unit_cost) ?? "0.00";
                $cleanSheetData['product_price'] = '0.00';
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['transfer_in'] = $barnetReport->other_additions_units ?? '0';
                $cleanSheetData['transfer_out'] = $barnetReport->transfer_units ?? '0';
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $barnetReport->opening_inventory_units ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $barnetReport->closing_inventory_units ?? '0';
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
                $cleanSheetData['pos_report_id'] = $barnetReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['thc_range'] = null;
                $cleanSheetData['cbd_range'] = null;
                $cleanSheetData['size_in_gram'] = null;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province_id'] = $provinceId;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $productName;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $barnetReport->quantity_sold_units ?? '0';
                $cleanSheetData['purchase'] = $barnetReport->quantity_purchased_units ?? '0';
                $barnetAveragePrice = $this->barnetAveragePrice($barnetReport->quantity_sold_value,$barnetReport->quantity_sold_units);
                $cleanSheetData['average_price'] = $barnetAveragePrice;
                $barnetAverageCost = $this->barnetAverageCost($barnetReport->opening_inventory_value,$barnetReport->opening_inventory_units);
                $cleanSheetData['report_price_og'] = $barnetAverageCost;
                $cleanSheetData['average_cost'] = '0.00';
                $cleanSheetData['product_price'] = '0.00';
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['transfer_in'] = $barnetReport->other_additions_units ?? '0';
                $cleanSheetData['transfer_out'] = $barnetReport->transfer_units ?? '0';
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $barnetReport->opening_inventory_units ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $barnetReport->closing_inventory_units ?? '0';
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
        $cleanSheetData['product_price'] = $this->sanitizeNumeric($cleanSheetData['product_price']);

        return $cleanSheetData;
    }
    public function barnetAveragePrice ($quantity_sold_value,$quantity_sold_units){
        $quantity_sold_value = GeneralFunctions::formatAmountValue($quantity_sold_value);
        $quantity_sold_units = (double)trim($quantity_sold_units);
        if(!empty($quantity_sold_value) && (int)$quantity_sold_units > 0 && $quantity_sold_value != '0' && $quantity_sold_value != '0.00' && $quantity_sold_units != '0'){
            $barnetAveragePrice = ($quantity_sold_value/$quantity_sold_units);
        }
        else{
            $barnetAveragePrice = "0.00";
        }
        return $barnetAveragePrice;
    }

    public function barnetAverageCost ($opening_inventory_value,$opening_inventory_units){
        $opening_inventory_value = GeneralFunctions::formatAmountValue($opening_inventory_value);
        $opening_inventory_units = (double)trim($opening_inventory_units);
        if(!empty($opening_inventory_value) && (int)$opening_inventory_units > 0 && $opening_inventory_value != '0' && $opening_inventory_value != '0.00' && $opening_inventory_units != '0'){
            $barnetAverageCost = ($opening_inventory_value/$opening_inventory_units);
        } elseif(!empty($opening_inventory_value) && (int)$opening_inventory_units < 0 && $opening_inventory_value < 0){
            $barnetAverageCost = ($opening_inventory_value/$opening_inventory_units);
        }
        else{
            $barnetAverageCost = "0.00";
        }
        return $barnetAverageCost;
    }


}
