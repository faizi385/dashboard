<?php

namespace App\Traits;

use App\Models\Lp;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\TechPOSReport;
use App\Models\ProductVariation;
use App\Helpers\GeneralFunctions;
use Illuminate\Support\Facades\Log;

use App\Models\GlobalTillSalesSummaryReport;

trait GlobalTillIntegration
{
    /**
     * Process Globaltill and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapGlobaltillMasterCatalouge($gobatellDiagnosticReport,$report)
    {

        $GobatellSalesSummaryReport =  GlobalTillSalesSummaryReport::where('gb_diagnostic_report_id', $gobatellDiagnosticReport->id)->first();
        Log::info('Processing Globaltill reports:', ['report' => $report]);
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $gobatellDiagnosticReport->report->retailer_id ?? null;
        $location = $gobatellDiagnosticReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $gobatellDiagnosticReport->supplier_sku;
        $gtin = $gobatellDiagnosticReport->compliance_code;
        $productName = $gobatellDiagnosticReport->product;
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
            $product = $this->matchICBarcodeSku($gobatellDiagnosticReport->compliance_code, $gobatellDiagnosticReport->supplier_sku,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if (!empty($sku) && empty($product)) {
            $product = $this->matchICSku($gobatellDiagnosticReport->supplier_sku,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if (!empty($gtin) && empty($product)) {
            $product = $this->matchICBarcode($gobatellDiagnosticReport->compliance_code,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if (!empty($productName) && empty($product)){
            $product = $this->matchICProductName($gobatellDiagnosticReport->product,$provinceName,$provinceSlug,$provinceId,$lpId);
        }
        if ($product) {
            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $gobatellDiagnosticReport->id;
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
            $cleanSheetData['sold'] = $gobatellDiagnosticReport->sales_reductions ?? "0";
            $cleanSheetData['purchase'] = $gobatellDiagnosticReport->purchases_from_suppliers_additions ?? "0";
            $cleanSheetData['average_price'] = $this->avgPriceForGlobaltill($GobatellSalesSummaryReport);
            $cleanSheetData['average_cost'] = $this->avgCostForGlobaltill($GobatellSalesSummaryReport);
            $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
            if( $cleanSheetData['average_cost'] == 0.00 ||  $cleanSheetData['average_cost'] == 0){
                $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($product->price_per_unit);
            }
            if( $cleanSheetData['average_cost'] == 0.00 ||  $cleanSheetData['average_cost'] == 0){
                $cleanSheetData['average_cost'] = 0;
            }
            $cleanSheetData['barcode'] = $gtin;
            $cleanSheetData['report_id'] = $report->id;
            if ($gobatellDiagnosticReport->other_additions_additions > 0) {
                $cleanSheetData['transfer_in'] = $gobatellDiagnosticReport->other_additions_additions;
            } else {
                $cleanSheetData['transfer_in'] = 0;
            }
            if ($gobatellDiagnosticReport->other_reductions_reductions < 0) {
                $cleanSheetData['transfer_out'] = str_replace('-', '', $gobatellDiagnosticReport->other_reductions_reductions);
            } else {
                $cleanSheetData['transfer_out'] = 0;
            }
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $gobatellDiagnosticReport->opening_inventory ?? "0";
            $cleanSheetData['closing_inventory_unit'] = $gobatellDiagnosticReport->closing_inventory ?? "0";
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            $offer = $this->DQISummaryFlag($report,$gobatellDiagnosticReport->supplier_sku,'',$gobatellDiagnosticReport->productname,$provinceName,$provinceSlug,$provinceId,$lpId );
            if (!empty($offer)) {
                $cleanSheetData['offer_id'] = $offer->id;
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
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
                if( $cleanSheetData['average_cost'] == '0.00' && (int) $cleanSheetData['average_cost'] == 0){
                    $cleanSheetData['average_cost'] = \App\Helpers\GeneralFunctions::formatAmountValue($offer->unit_cost) ?? "0.00";
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
            $offer = null;
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$gobatellDiagnosticReport->supplier_sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,     $lpId);
            } if (!empty($gtin) && empty($offer)) {
                $offer = $this->matchOfferBarcode($report->date,$gobatellDiagnosticReport->compliance_code,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,     $lpId);
            } if (!empty($productName) && empty($offer)) {
                $offer = $this->matchOfferProductName($report->date,$gobatellDiagnosticReport->product,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,     $lpId);
            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $gobatellDiagnosticReport->id;
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
                $cleanSheetData['sold'] = $gobatellDiagnosticReport->sales_reductions ?? "0";
                $cleanSheetData['purchase'] = $gobatellDiagnosticReport->purchases_from_suppliers_additions ?? "0";
                $cleanSheetData['average_price'] = $this->avgPriceForGlobaltill($GobatellSalesSummaryReport);
                $cleanSheetData['average_cost'] = $this->avgCostForGlobaltill($GobatellSalesSummaryReport);
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }
                if( $cleanSheetData['average_cost'] == 0.00 ||  $cleanSheetData['average_cost'] == 0){
                    $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($product->price_per_unit);
                }
                if( $cleanSheetData['average_cost'] == 0.00 ||  $cleanSheetData['average_cost'] == 0){
                    $cleanSheetData['average_cost'] = 0;
                }
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                if ($gobatellDiagnosticReport->other_additions_additions > 0) {
                    $cleanSheetData['transfer_in'] = $gobatellDiagnosticReport->other_additions_additions;
                } else {
                    $cleanSheetData['transfer_in'] = 0;
                }
                if ($gobatellDiagnosticReport->other_reductions_reductions < 0) {
                    $cleanSheetData['transfer_out'] = str_replace('-', '', $gobatellDiagnosticReport->other_reductions_reductions);
                } else {
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $gobatellDiagnosticReport->opening_inventory ?? "0";
                $cleanSheetData['closing_inventory_unit'] = $gobatellDiagnosticReport->closing_inventory ?? "0";
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
                $cleanSheetData['pos_report_id'] = $gobatellDiagnosticReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['thc_range'] = null;
                $cleanSheetData['cbd_range'] = null;
                $cleanSheetData['size_in_gram'] = null;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['province_id'] =  $provinceId ;
                $cleanSheetData['sku'] = $gobatellDiagnosticReport->sku;
                $cleanSheetData['product_name'] = $gobatellDiagnosticReport->description;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $gobatellDiagnosticReport->sales_reductions ?? "0";
                $cleanSheetData['purchase'] = $gobatellDiagnosticReport->purchases_from_suppliers_additions ?? "0";
                $cleanSheetData['average_price'] = $this->avgPriceForGlobaltill($GobatellSalesSummaryReport);
                $cleanSheetData['average_cost'] = $this->avgCostForGlobaltill($GobatellSalesSummaryReport);
                $cleanSheetData['report_price_og'] = $gobatellDiagnosticReport->average_cost;
                $cleanSheetData['barcode'] = null;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['report_id'] = $report->id;
                if ($gobatellDiagnosticReport->other_additions_additions > 0) {
                    $cleanSheetData['transfer_in'] = $gobatellDiagnosticReport->other_additions_additions;
                } else {
                    $cleanSheetData['transfer_in'] = 0;
                }
                if ($gobatellDiagnosticReport->other_reductions_reductions < 0) {
                    $cleanSheetData['transfer_out'] = str_replace('-', '', $gobatellDiagnosticReport->other_reductions_reductions);
                } else {
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $gobatellDiagnosticReport->opening_inventory ?? "0";
                $cleanSheetData['closing_inventory_unit'] = $gobatellDiagnosticReport->closing_inventory ?? "0";
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

    private function avgPriceForGlobaltill($GobatellSalesSummaryReport)
    {
        if (!empty($GobatellSalesSummaryReport->sold_retail_value) && !empty($GobatellSalesSummaryReport->sales_reductions)) {
            $sold_retail_value = GeneralFunctions::formatAmountValue($GobatellSalesSummaryReport->sold_retail_value);
            $sales_reductions = GeneralFunctions::formatAmountValue($GobatellSalesSummaryReport->sales_reductions);
            if ($sales_reductions == 0.00 || $sales_reductions == 0) {
                $sales_reductions = 1;
            }
            $averagePrice = $sold_retail_value / $sales_reductions;
        }
        else{
            $averagePrice = 0.00;
        }

        return $averagePrice;
    }
    private function avgCostForGlobaltill($GobatellSalesSummaryReport)
    {
        if (!empty($GobatellSalesSummaryReport->purchases_from_suppliers_value) && !empty($GobatellSalesSummaryReport->purchases_from_suppliers_additions)) {
            $purchases_from_suppliers_value = GeneralFunctions::formatAmountValue($GobatellSalesSummaryReport->purchases_from_suppliers_value);
            $purchases_from_suppliers_additions = GeneralFunctions::formatAmountValue($GobatellSalesSummaryReport->purchases_from_suppliers_additions);
            if($purchases_from_suppliers_value != 0 && $purchases_from_suppliers_value != 0.00 && $purchases_from_suppliers_additions != 0 && $purchases_from_suppliers_additions != 0.00){
                $average_cost = $purchases_from_suppliers_value / $purchases_from_suppliers_additions ;
            }
            else{
                $average_cost = 0.00;
            }
        }
        else{
            $average_cost = 0.00;
        }

        return $average_cost;
    }
}
