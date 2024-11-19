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
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $barnetReport->report->retailer_id ?? null;
        $location = $barnetReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $barnetReport->product_sku;
        $gtin = $barnetReport->barcode;
        $productName = $barnetReport->description;
        $provinceId = $report->province_id;
        $provinceName = $report->province;
        $provinceSlug = $report->province_slug;
        $product = null;
        $lpId = $report->lp_id;

        $retailer = Retailer::find($retailer_id);
        if ($retailer) {
            $retailerName = trim("{$retailer->first_name} {$retailer->last_name}");
        } else {
            Log::warning('Retailer not found:', ['retailer_id' => $retailer_id]);
        }

        if (!empty($gtin) && !empty($sku)) {
            $product = $this->matchICBarcodeSku($barnetReport->barcode,$barnetReport->product_sku,$provinceName,$provinceSlug,$provinceId,    $lpId );
        }
        if (!empty($sku) && empty($product)) {
            $product = $this->matchICSku($barnetReport->product_sku,$provinceName,$provinceSlug,$provinceId,    $lpId );
        }
        if (!empty($gtin) && empty($product)) {
            $product = $this->matchICBarcode($barnetReport->barcode,$provinceName,$provinceSlug,$provinceId,    $lpId );
        }
        if (!empty($productName) && empty($product)){
            $product = $this->matchICProductName($barnetReport->description,$provinceName,$provinceSlug,$provinceId,    $lpId );
        }
        if ($product) {
            $lp = Lp::where('id',$product->lp_id)->first();
            $lpName = $lp->name ?? null;
            $lpId = $lp->id ?? null;

            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $barnetReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] =  $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] = $product->product_name;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $barnetReport->quantity_sold_units ?? '0';
            $cleanSheetData['purchase'] = $barnetReport->quantity_purchased_units ?? '0';
            $barnetAveragePrice = $this->barnetAveragePrice($barnetReport->quantity_sold_value,$barnetReport->quantity_sold_units);
            $cleanSheetData['average_price'] = $barnetAveragePrice;
            $barnetAverageCost = $this->barnetAverageCost($barnetReport->opening_inventory_value,$barnetReport->opening_inventory_units);
            $cleanSheetData['report_price_og'] = $barnetAverageCost;
            if($barnetAverageCost == "0.00"){
                $barnetAverageCost = $product->price_per_unit;
            }
            $cleanSheetData['average_cost'] = $barnetAverageCost;
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
            $offer = $this->DQISummaryFlag($report,$barnetReport->sku,$barnetReport->barcode,$barnetReport->name,$provinceName,$provinceSlug,$provinceId,$lpId );
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
                $offer = $this->matchOfferSku($report->date,$sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,    $lpId );
            } if (!empty($gtin) && empty($offer)) {
                $offer = $this->matchOfferBarcode($report->date,$gtin,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,    $lpId );
            } if (!empty($productName) && empty($offer)) {
                $offer = $this->matchOfferProductName($report->date,$productName,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,    $lpId );
            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $barnetReport->id;
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
                $cleanSheetData['sold'] = $barnetReport->quantity_sold_units ?? '0';
                $cleanSheetData['purchase'] = $barnetReport->quantity_purchased_units ?? '0';
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offer->lp_id,$offer->lp_name,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }

                $barnetAveragePrice = $this->barnetAveragePrice($barnetReport->quantity_sold_value,$barnetReport->quantity_sold_units);
                $cleanSheetData['average_price'] = $barnetAveragePrice;
                $barnetAverageCost = $this->barnetAverageCost($barnetReport->opening_inventory_value,$barnetReport->opening_inventory_units);
                $cleanSheetData['report_price_og'] = $barnetAverageCost;
                if($barnetAverageCost == "0.00"){
                    $barnetAverageCost = GeneralFunctions::formatAmountValue($offer->unit_cost);
                }
                $cleanSheet['average_cost'] = $barnetAverageCost;
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['transfer_in'] = $barnetReport->other_additions_units ?? '0';
                $cleanSheetData['transfer_out'] = $barnetReport->transfer_units ?? '0';
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $barnetReport->opening_inventory_units ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $barnetReport->closing_inventory_units ?? '0';
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
                $cleanSheetData['pos_report_id'] = $barnetReport->id;
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
                $cleanSheetData['product_name'] = $barnetReport->name;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $barnetReport->quantity_sold_units ?? '0';
                $cleanSheetData['purchase'] = $barnetReport->quantity_purchased_units ?? '0';
                $cleanSheetData['average_price'] = $barnetReport->average_price;
                $cleanSheetData['average_cost'] = $barnetReport->average_cost;
                $cleanSheetData['report_price_og'] = $barnetReport->average_cost;
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
