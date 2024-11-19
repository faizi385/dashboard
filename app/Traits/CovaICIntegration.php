<?php

namespace App\Traits;

use App\Helpers\GeneralFunctions;
use App\Models\CleanSheet;
use App\Models\CovaSalesReport;
use App\Models\InternalMasterCatalouge;
use App\Models\Lp;
use App\Models\LpVariableFeeStructure;
use App\Models\Retailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait CovaICIntegration
{
    public function mapCovaCatalouge($covaDaignosticReport, $report)
    {
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00';
        $retailer_id = $covaDaignosticReport->report->retailer_id ?? null;
        $location = $covaDaignosticReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }

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

        if ($report->province == 'ON' || $report->province == 'Ontario') {
            $product =  $this->getICMatchedData('ocs_sku', trim($covaDaignosticReport->ocs_sku) ?? '', 'ontario_barcode_upc', trim($covaDaignosticReport->ontario_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug, $provinceId);
            if(!empty($product) && empty($covaDaignosticReport->ocs_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            } else {
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            }
        } elseif ($report->province == 'AB' || $report->province == 'Alberta') {
            $product =  $this->getICMatchedData('aglc_sku', trim($covaDaignosticReport->aglc_sku) ?? '', 'manitoba_barcode_upc', trim($covaDaignosticReport->manitoba_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug, $provinceId);
            if(!empty($product) && empty($covaDaignosticReport->aglc_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            } else {
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            }
        } elseif ($report->province == 'MB' || $report->province == 'Manitoba') {
            $product =  $this->getICMatchedData('new_brunswick_sku', trim($covaDaignosticReport->new_brunswick_sku) ?? '', 'manitoba_barcode_upc', trim($covaDaignosticReport->manitoba_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug, $provinceId);
            if(!empty($product) && empty($covaDaignosticReport->new_brunswick_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            } else{
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            }
        } elseif ($report->province == 'BC' || $report->province == 'British Columbia') {
            $product =  $this->getICMatchedData('new_brunswick_sku', trim($covaDaignosticReport->new_brunswick_sku) ?? '', 'manitoba_barcode_upc', trim($covaDaignosticReport->manitoba_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug, $provinceId);
            if(!empty($product) && empty($covaDaignosticReport->new_brunswick_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            } else {
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            }
        } elseif ($report->province == 'SK' || $report->province == 'Saskatchewan') {
            $product =  $this->getICMatchedData('new_brunswick_sku', trim($covaDaignosticReport->new_brunswick_sku) ?? '', 'saskatchewan_barcode_upc', trim($covaDaignosticReport->saskatchewan_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug, $provinceId);
            if(!empty($product) && empty($covaDaignosticReport->new_brunswick_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            } else {
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->first();
            }
        }
        if (!empty($product)) {
            $lp = Lp::where('id',$product->lp_id)->first();
            $lpName = $lp->name ?? null;
            $lpId = $lp->id ?? null;

            $cleanSheetData['retailer_id'] = $report->retailer_id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] = $product->product_size;
            $cleanSheetData['location'] = $report->location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['province_id'] =  $provinceId ;
            $cleanSheetData['sku'] = $product->sku;
            $cleanSheetData['product_name'] = $product->product_name;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand_name ?? "0";
            $cleanSheetData['sold'] = $covaDaignosticReport->quantity_sold_units ?? "0";
            $cleanSheetData['purchase'] = $covaDaignosticReport->quantity_purchased_units;
            $cleanSheetData['pos_report_id'] = $covaDaignosticReport->id;
            if ($CovaSalesSummaryReport && !empty($CovaSalesSummaryReport->average_retail_price)) {
                $cleanSheetData['average_price']  = $CovaSalesSummaryReport->average_retail_price ?? "0.00";
            } else {
                $cleanSheetData['average_price'] = "0.00";
            }
            $cleanSheetData['average_cost'] = "0.00";
            if ($product !== null && isset($CovaSalesSummaryReport->total_Cost) && !empty((double)$CovaSalesSummaryReport->net_sold)) {
                $cleanSheetData['average_cost'] = $this->avgCostForCova($CovaSalesSummaryReport);
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
            }
            if (isset($product->price_per_unit) && (double)$product->price_per_unit != 0 &&  $cleanSheetData['average_cost'] == 0) {
                $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($product->price_per_unit);
            }
            $cleanSheetData['barcode'] = $product->gtin;
            $cleanSheetData['report_id'] = $report->id;
            if ($covaDaignosticReport->other_additions_units > 0) {
                $cleanSheetData['transfer_in'] = $covaDaignosticReport->other_additions_units;
            } else {
                $cleanSheetData['transfer_in'] = 0;
            }
            if ($covaDaignosticReport->other_reductions_units < 0) {
                $cleanSheetData['transfer_out'] = str_replace('-', '', $covaDaignosticReport->other_reductions_units);
            } else {
                $cleanSheetData['transfer_out'] = 0;
            }
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $covaDaignosticReport->opening_inventory_units ?? "0";
            $cleanSheetData['closing_inventory_unit'] = $covaDaignosticReport->closing_inventory_units ?? "0";
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            $offer = $this->getDQIMatchedData($report, $covaDaignosticReport, $provinceName, $provinceSlug,$provinceId);
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
                if ($cleanSheetData['average_cost'] == '0.00') {
                    $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($offer->unit_cost) ?? "0.00";
                }
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
            $offer = $this->getMatchedOffer($report, $covaDaignosticReport, $provinceName, $provinceSlug, $provinceId);
            if (!empty($offer)) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['lp_id'] = $offer->lp_id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['lp_name'] = $offer->lp_name;
                $cleanSheetData['thc_range'] = $offer->thc;
                $cleanSheetData['cbd_range'] = $offer->cbd;
                $cleanSheetData['size_in_gram'] = $offer->product_size;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['province_id'] =  $provinceId ;
                $cleanSheetData['sku'] = $offer->provincial_sku;
                $cleanSheetData['product_name'] = $offer->product_name;
                $cleanSheetData['category'] = $offer->category;
                $cleanSheetData['brand'] = $offer->brand;
                $cleanSheetData['sold'] = $covaDaignosticReport->quantity_sold_units ?? "0";
                $cleanSheetData['purchase'] = $covaDaignosticReport->quantity_purchased_units;
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report, $provinceSlug, $provinceName,$offer->lp_id,$offer->lp_name,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                }
                $cleanSheetData['pos_report_id'] = $covaDaignosticReport->id;
                if ($CovaSalesSummaryReport && !empty($CovaSalesSummaryReport->average_retail_price)) {
                    $cleanSheetData['average_price']  = $CovaSalesSummaryReport->average_retail_price ?? "0.00";
                } else {
                    $cleanSheetData['average_price'] = "0.00";
                }
                $cleanSheetData['average_cost'] = "0.00";
                if (isset($CovaSalesSummaryReport->total_Cost) && $CovaSalesSummaryReport->total_Cost != null && isset($CovaSalesSummaryReport->net_sold) && $CovaSalesSummaryReport->net_sold != null) {
                    if (!empty((double)$CovaSalesSummaryReport->total_Cost) && !empty((double)$CovaSalesSummaryReport->net_sold)) {
                        $cleanSheetData['average_cost'] = $this->avgCostForCova($CovaSalesSummaryReport);
                        $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                    }
                }
                if (isset($offer->unit_cost) && (double)$offer->unit_cost != 0 &&  $cleanSheetData['average_cost'] == 0) {
                    $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($offer->unit_cost);
                }
                $cleanSheetData['barcode'] = $offer->gtin;
                $cleanSheetData['report_id'] = $report->id;
                if ($covaDaignosticReport->other_additions_units > 0) {
                    $cleanSheetData['transfer_in'] = $covaDaignosticReport->other_additions_units;
                } else {
                    $cleanSheetData['transfer_in'] = 0;
                }

                if ($covaDaignosticReport->other_reductions_units < 0) {
                    $cleanSheetData['transfer_out'] = str_replace('-', '', $covaDaignosticReport->other_reductions_units);
                } else {
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $covaDaignosticReport->opening_inventory_units ?? "0";
                $cleanSheetData['closing_inventory_unit'] = $covaDaignosticReport->closing_inventory_units ?? "0";
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
                $this->addDataCleansheet($report, $covaDaignosticReport,  $cleanSheetData);
                $cleanSheetData['retailer_id'] = $report->retailer_id;
                $cleanSheetData['offer_id'] = null;
                $cleanSheetData['lp_id'] = null;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['lp_name'] = null;
                $cleanSheetData['thc_range'] = null;
                $cleanSheetData['cbd_range'] = null;
                $cleanSheetData['size_in_gram'] = null;
                $cleanSheetData['location'] = $report->location;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['province_id'] =  $provinceId ;
                $cleanSheetData['product_name'] = $covaDaignosticReport->product_name;
                $cleanSheetData['category'] = $CovaSalesSummaryReport->category ?? '';
                $cleanSheetData['brand'] =  $CovaSalesSummaryReport->brand ?? '';
                $cleanSheetData['sold'] = $covaDaignosticReport->quantity_sold_units ?? "0";
                $cleanSheetData['purchase'] = $covaDaignosticReport->quantity_purchased_units;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['pos_report_id'] = $covaDaignosticReport->id;
                if ($CovaSalesSummaryReport && !empty($CovaSalesSummaryReport->average_retail_price)) {
                    $cleanSheetData['average_price']  = $CovaSalesSummaryReport->average_retail_price ?? "0.00";
                } else {
                    $cleanSheetData['average_price'] = "0.00";
                }
                $cleanSheetData['average_cost'] = $this->avgCostForCova($CovaSalesSummaryReport);
                $cleanSheetData['report_price_og'] = $cleanSheetData['average_cost'];
                $cleanSheetData['report_id'] = $report->id;
                if ($covaDaignosticReport->other_additions_units > 0) {
                    $cleanSheetData['transfer_in'] = $covaDaignosticReport->other_additions_units;
                } else {
                    $cleanSheetData['transfer_in'] = 0;
                }

                if ($covaDaignosticReport->other_reductions_units < 0) {
                    $cleanSheetData['transfer_out'] = str_replace('-', '', $covaDaignosticReport->other_reductions_units);
                } else {
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $covaDaignosticReport->opening_inventory_units ?? "0";
                $cleanSheetData['closing_inventory_unit'] = $covaDaignosticReport->closing_inventory_units ?? "0";
                $cleanSheetData['flag'] = '0';
                $cleanSheetData['comment'] = 'Record not found in the Master Catalog And Offer';
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
    private function avgCostForCova($CovaSalesSummaryReport)
    {
        $total_Cost = GeneralFunctions::formatAmountValue($CovaSalesSummaryReport->total_Cost ?? 0);
        $net_sold = (double)trim($CovaSalesSummaryReport->net_sold ?? 0);

        if ($net_sold == 0) {
            $net_sold = 1;
        }
        if ($total_Cost != 0) {
            $averageCost = $total_Cost / (float)$net_sold;
        } else {
            $averageCost = 0;
        }

        return $averageCost;
    }
    private function getICMatchedData($sku_key, $sku_value, $barcode_key, $barcode_value, $product_key, $product_value, $provinceName, $provinceSlug, $provinceId)
    {
        if (!empty($barcode_value) && !empty($sku_value)) {
            $product = $this->matchICBarcodeSku($barcode_value, $sku_value, $provinceName, $provinceSlug, $provinceId);
        } 
        if (!empty($sku_value) && empty($product)) {
            $product = $this->matchICSku($sku_value, $provinceName, $provinceSlug, $provinceId);
        } 
        if (!empty($barcode_value)  && empty($product)) {
            $product = $this->matchICBarcode($barcode_value, $provinceName, $provinceSlug, $provinceId);
        } 
        if (!empty($product_value) && empty($product)) {
            $product = $this->matchICProductName($product_value, $provinceName, $provinceSlug, $provinceId);
        }
        return  $product ?? '';
    }
    private function getDQIMatchedData($report, $covaDaignosticReport, $provinceName, $provinceSlug, $provinceId)
    {
        if ($report->province == 'ON' || $report->province == 'Ontario') {
            return $this->DQISummaryFlag($report, trim($covaDaignosticReport->ocs_sku),  trim($covaDaignosticReport->ontario_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug, $provinceId);
        } elseif ($report->province == 'AB' || $report->province == 'Alberta') {
            return $this->DQISummaryFlag($report, trim($covaDaignosticReport->aglc_sku),  trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug, $provinceId);
        } elseif ($report->province == 'MB' || $report->province == 'Manitoba') {
            return $this->DQISummaryFlag($report, trim($covaDaignosticReport->new_brunswick_sku),  trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug, $provinceId);
        } elseif ($report->province == 'BC' || $report->province == 'British Columbia') {
            return $this->DQISummaryFlag($report, trim($covaDaignosticReport->new_brunswick_sku),  trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug, $provinceId);
        } elseif ($report->province == 'SK' || $report->province == 'Saskatchewan') {
            return $this->DQISummaryFlag($report, trim($covaDaignosticReport->new_brunswick_sku),  trim($covaDaignosticReport->saskatchewan_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug, $provinceId);
        }
    }
    private function MatchedOffer($report, $provinceName, $provinceSlug, $provinceId, $sku, $barcode, $product_name)
    {
        if (!empty($sku)) {
            $offer = $this->matchOfferSku($report->date, $sku, $provinceName, $provinceSlug, $provinceId, $report->retailer_id);
        }
        if (!empty($barcode) && empty($offer)) {
            $offer = $this->matchOfferBarcode($report->date, $barcode, $provinceName, $provinceSlug, $provinceId, $report->retailer_id);
        }
        if (!empty($product_name) && empty($offer)) {
            $offer = $this->matchOfferProductName($report->date, $product_name, $provinceName, $provinceSlug, $provinceId, $report->retailer_id);
        }
        return $offer ?? '';
    }
    private function getMatchedOffer($report, $covaDaignosticReport, $provinceName, $provinceSlug, $provinceId)
    {
        if ($report->province == 'ON' || $report->province == 'Ontario') {
            return  $this->MatchedOffer($report, $provinceName, $provinceSlug, $provinceId, trim($covaDaignosticReport->ocs_sku), trim($covaDaignosticReport->ontario_barcode_upc), $covaDaignosticReport->product_name);
        } elseif ($report->province == 'AB' || $report->province == 'Alberta') {
            return  $this->MatchedOffer($report, $provinceName, $provinceSlug, $provinceId, trim($covaDaignosticReport->aglc_sku), trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name);
        } elseif ($report->province == 'MB' || $report->province == 'Manitoba') {
            return  $this->MatchedOffer($report, $provinceName, $provinceSlug, $provinceId, trim($covaDaignosticReport->new_brunswick_sku), trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name);
        } elseif ($report->province == 'BC' || $report->province == 'British Columbia') {
            return  $this->MatchedOffer($report, $provinceName, $provinceSlug, $provinceId, trim($covaDaignosticReport->new_brunswick_sku), trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name);
        } elseif ($report->province == 'SK' || $report->province == 'Saskatchewan') {
            return  $this->MatchedOffer($report, $provinceName, $provinceSlug, $provinceId, trim($covaDaignosticReport->new_brunswick_sku), trim($covaDaignosticReport->saskatchewan_barcode_upc), $covaDaignosticReport->product_name);
        }
    }
    private function addDataCleansheet($report, $covaDaignosticReport, &$cleanSheetData)
    {
        if ($report->province == 'ON' || $report->province == 'Ontario') {
            $cleanSheetData['sku'] = trim($covaDaignosticReport->ocs_sku);
            $cleanSheetData['barcode'] = trim($covaDaignosticReport->ontario_barcode_upc);
        } elseif ($report->province == 'AB' || $report->province == 'Alberta') {
            $cleanSheetData['sku'] = trim($covaDaignosticReport->aglc_sku);
            $cleanSheetData['barcode'] = trim($covaDaignosticReport->manitoba_barcode_upc);
        } elseif ($report->province == 'MB' || $report->province == 'Manitoba') {
            $cleanSheetData['sku'] = trim($covaDaignosticReport->new_brunswick_sku);
            $cleanSheetData['barcode'] = trim($covaDaignosticReport->manitoba_barcode_upc);
        } elseif ($report->province == 'BC' || $report->province == 'British Columbia') {
            $cleanSheetData['sku'] = trim($covaDaignosticReport->new_brunswick_sku);
            $cleanSheetData['barcode'] = trim($covaDaignosticReport->manitoba_barcode_upc);
        } elseif ($report->province == 'SK' || $report->province == 'Saskatchewan') {
            $cleanSheetData['sku'] = trim($covaDaignosticReport->new_brunswick_sku);
            $cleanSheetData['barcode'] = trim($covaDaignosticReport->saskatchewan_barcode_upc);
        }
    }
}
