<?php

namespace App\Traits;

use App\Helpers\GeneralFunctions;
use App\Models\CleanSheet;
use App\Models\CovaSalesReport;
use App\Models\InternalMasterCatalouge;
use App\Models\LpVariableFeeStructure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait CovaICIntegration
{

    public function mapCovaMasterCatalouge($covaDaignosticReport, $retailerReportSubmission)
    {
        $provinceDetails = $this->getRetailerProvince($retailerReportSubmission);
        $provinceName = $provinceDetails['province_name'];
        $provinceSlug = $provinceDetails['province_slug'];
        $retailerName = $this->getRetailerName($retailerReportSubmission->retailer_id);
        $cleanSheet = [];
        $cleanSheet['report_price_og'] = '0.00';
        if ($retailerReportSubmission->province == 'ON' || $retailerReportSubmission->province == 'Ontario') {
            $CovaGtinMatchedData =  $this->getICMatchedData('ocs_sku', trim($covaDaignosticReport->ocs_sku) ?? '', 'ontario_barcode_upc', trim($covaDaignosticReport->ontario_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug);
            if(!empty($CovaGtinMatchedData) && empty($covaDaignosticReport->ocs_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$CovaGtinMatchedData->sku])->first();
            } else {
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$covaDaignosticReport->ocs_sku])->first();
            }
        } elseif ($retailerReportSubmission->province == 'AB' || $retailerReportSubmission->province == 'Alberta') {
            $CovaGtinMatchedData =  $this->getICMatchedData('aglc_sku', trim($covaDaignosticReport->aglc_sku) ?? '', 'manitoba_barcode_upc', trim($covaDaignosticReport->manitoba_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug);
            if(!empty($CovaGtinMatchedData) && empty($covaDaignosticReport->aglc_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$CovaGtinMatchedData->sku])->first();
            } else {
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$covaDaignosticReport->aglc_sku])->first();
            }
        } elseif ($retailerReportSubmission->province == 'MB' || $retailerReportSubmission->province == 'Manitoba') {
            $CovaGtinMatchedData =  $this->getICMatchedData('new_brunswick_sku', trim($covaDaignosticReport->new_brunswick_sku) ?? '', 'manitoba_barcode_upc', trim($covaDaignosticReport->manitoba_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug);
            if(!empty($CovaGtinMatchedData) && empty($covaDaignosticReport->new_brunswick_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$CovaGtinMatchedData->sku])->first();
            } else{
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$covaDaignosticReport->new_brunswick_sku])->first();
            }
        } elseif ($retailerReportSubmission->province == 'BC' || $retailerReportSubmission->province == 'British Columbia') {
            $CovaGtinMatchedData =  $this->getICMatchedData('new_brunswick_sku', trim($covaDaignosticReport->new_brunswick_sku) ?? '', 'manitoba_barcode_upc', trim($covaDaignosticReport->manitoba_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug);
            if(!empty($CovaGtinMatchedData) && empty($covaDaignosticReport->new_brunswick_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$CovaGtinMatchedData->sku])->first();
            } else {
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$covaDaignosticReport->new_brunswick_sku])->first();
            }
        } elseif ($retailerReportSubmission->province == 'SK' || $retailerReportSubmission->province == 'Saskatchewan') {
            $CovaGtinMatchedData =  $this->getICMatchedData('new_brunswick_sku', trim($covaDaignosticReport->new_brunswick_sku) ?? '', 'saskatchewan_barcode_upc', trim($covaDaignosticReport->saskatchewan_barcode_upc) ?? '', 'product_name', $covaDaignosticReport->product_name ?? '', $provinceName, $provinceSlug);
            if(!empty($CovaGtinMatchedData) && empty($covaDaignosticReport->new_brunswick_sku)){
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$CovaGtinMatchedData->sku])->first();
            } else {
                $CovaSalesSummaryReport =  CovaSalesReport::where('cova_diagnostic_report_id', $covaDaignosticReport->id)->whereRaw("FIND_IN_SET(?, sku)", [$covaDaignosticReport->new_brunswick_sku])->first();
            }
        }
        if (!empty($CovaGtinMatchedData)) {
            $lpId = $this->getlpID($CovaGtinMatchedData->lp_name);
            $cleanSheet['retailer_id'] = $retailerReportSubmission->retailer_id;
            $cleanSheet['retailer_name'] = $retailerName;
            $cleanSheet['thc_range'] = $CovaGtinMatchedData->thc_range;
            $cleanSheet['cbd_range'] = $CovaGtinMatchedData->cbd_range;
            $cleanSheet['size_in_grams_g'] = $CovaGtinMatchedData->size_in_grams_g;
            $cleanSheet['location'] = $retailerReportSubmission->location;
            $cleanSheet['province'] = $provinceName;
            $cleanSheet['province_slug'] = $provinceSlug;
            $cleanSheet['sku'] = $CovaGtinMatchedData->sku;
            $cleanSheet['product_name'] = $CovaGtinMatchedData->product_name;
            $cleanSheet['category'] = $CovaGtinMatchedData->category;
            $cleanSheet['brand'] = $CovaGtinMatchedData->brand_name ?? "0";
            $cleanSheet['sold'] = $covaDaignosticReport->quantity_sold_units ?? "0";
            $cleanSheet['purchased'] = $covaDaignosticReport->quantity_purchased_units;
            $cleanSheet['report_id'] = $covaDaignosticReport->id;
            if ($CovaSalesSummaryReport && !empty($CovaSalesSummaryReport->average_retail_price)) {
                $cleanSheet['average_price']  = $CovaSalesSummaryReport->average_retail_price ?? "0.00";
            } else {
                $cleanSheet['average_price'] = "0.00";
            }
            $cleanSheet['average_cost'] = "0.00";
            if ($CovaGtinMatchedData !== null && isset($CovaSalesSummaryReport->total_Cost) && !empty((double)$CovaSalesSummaryReport->net_sold)) {
                $cleanSheet['average_cost'] = $this->avgCostForCova($CovaSalesSummaryReport);
                $cleanSheet['report_price_og'] = $cleanSheet['average_cost'];
//            } elseif ((!empty((double)$CovaSalesSummaryReport->total_Cost) && !empty((int)$CovaSalesSummaryReport->net_sold)) &&   $cleanSheet['average_cost'] == 0) {
            }
            if (isset($CovaGtinMatchedData->price_per_unit) && (double)$CovaGtinMatchedData->price_per_unit != 0 &&  $cleanSheet['average_cost'] == 0) {
                $cleanSheet['average_cost'] = GeneralFunctions::formatAmountValue($CovaGtinMatchedData->price_per_unit);
            }
            $cleanSheet['barcode'] = $CovaGtinMatchedData->gtin;
            $cleanSheet['variable'] = null;
            $cleanSheet['retailerReportSubmission_id'] = $covaDaignosticReport->retailerReportSubmission_id;
            if ($covaDaignosticReport->other_additions_units > 0) {
                $cleanSheet['transfer_in'] = $covaDaignosticReport->other_additions_units;
            } else {
                $cleanSheet['transfer_in'] = 0;
            }
            if ($covaDaignosticReport->other_reductions_units < 0) {
                $cleanSheet['transfer_out'] = str_replace('-', '', $covaDaignosticReport->other_reductions_units);
            } else {
                $cleanSheet['transfer_out'] = 0;
            }
            $cleanSheet['pos'] = $retailerReportSubmission->pos;
            $cleanSheet['reconciliation_date'] = $retailerReportSubmission->date;
            $cleanSheet['opening_inventory_units'] = $covaDaignosticReport->opening_inventory_units ?? "0";
            $cleanSheet['closing_inventory_units'] = $covaDaignosticReport->closing_inventory_units ?? "0";
            $cleanSheet['ic_id'] = $CovaGtinMatchedData->internal_id;
            $cleanSheet['dqi_per'] = '';
            $cleanSheet['dqi_fee'] = '';
            $offers = $this->getDQIMatchedData($retailerReportSubmission, $covaDaignosticReport, $provinceName, $provinceSlug);
            if (!empty($offers)) {
                $cleanSheet['offer_id'] = $offers->id;
                $cleanSheet['lp_id'] = $offers->lp_id;
                $cleanSheet['lp_name'] = $offers->lp;
                if((int) $cleanSheet['purchased'] > 0){
                    $checkCarveout = $this->checkCarveOuts($retailerReportSubmission, $provinceSlug, $provinceName,$offers->lp_id,$offers->lp,$offers->provincial);
                    $cleanSheet['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheet['c_flag'] = '';
                }
                $cleanSheet['dqi_flag'] = 1;
                $cleanSheet['flag'] = '3';
                /**************************************************************************/
                $TotalQuantityGet = $cleanSheet['purchased'];
                $TotalUnitCostGet = $cleanSheet['average_cost'];
                /***************************************************************************/
                $TotalPurchaseCostMake = (float)$TotalQuantityGet * (float)$TotalUnitCostGet;
                $FinalDQIFEEMake = (float)trim($offers->data, '%') * 100;
                $FinalFeeInDollar = (float)$TotalPurchaseCostMake * $FinalDQIFEEMake / 100;
                $cleanSheet['dqi_per'] = $FinalDQIFEEMake;
                $cleanSheet['dqi_fee'] = number_format($FinalFeeInDollar,2);
                /**************************************************************************/
                $cleanSheet['comments'] = 'Record found in the Master Catalog and Offer';
                if ($cleanSheet['average_cost'] == '0.00') {
                    $cleanSheet['average_cost'] = GeneralFunctions::formatAmountValue($offers->unit_cost) ?? "0.00";
                }
            } else {
                $cleanSheet['offer_id'] = null;
                $cleanSheet['lp_id'] = $lpId;
                $cleanSheet['lp_name'] = $CovaGtinMatchedData->lp_name;
                $cleanSheet['c_flag'] = '';
                $cleanSheet['dqi_flag'] = 0;
                $cleanSheet['flag'] = '1';
                $cleanSheet['comments'] = 'Record found in the Master Catalog';
            }
        } else {
            $lpOffer = $this->getMatchedOffer($retailerReportSubmission, $covaDaignosticReport, $provinceName, $provinceSlug);
            if (!empty($lpOffer)) {
                $cleanSheet['retailer_id'] = $retailerReportSubmission->retailer_id;
                $cleanSheet['offer_id'] = $lpOffer->id;
                $cleanSheet['lp_id'] = $lpOffer->lp_id;
                $cleanSheet['retailer_name'] = $retailerName;
                $cleanSheet['lp_name'] = $lpOffer->lp;
                $cleanSheet['thc_range'] = $lpOffer->thc;
                $cleanSheet['cbd_range'] = $lpOffer->cbd;
                $cleanSheet['size_in_grams_g'] = null;
                $cleanSheet['location'] = $retailerReportSubmission->location;
                $cleanSheet['province'] = $provinceName;
                $cleanSheet['province_slug'] = $provinceSlug;
                $cleanSheet['sku'] = $lpOffer->provincial;
                $cleanSheet['product_name'] = $lpOffer->product_name;
                $cleanSheet['category'] = $lpOffer->category;
                $cleanSheet['brand'] = $lpOffer->brand;
                $cleanSheet['sold'] = $covaDaignosticReport->quantity_sold_units ?? "0";
                $cleanSheet['purchased'] = $covaDaignosticReport->quantity_purchased_units;
                if((int) $cleanSheet['purchased'] > 0){
                    $checkCarveout = $this->checkCarveOuts($retailerReportSubmission, $provinceSlug, $provinceName,$lpOffer->lp_id,$lpOffer->lp,$lpOffer->provincial);
                    $cleanSheet['c_flag'] = $checkCarveout ? 'yes' : 'no';
                }
                else{
                    $cleanSheet['c_flag'] = '';
                }
                $cleanSheet['report_id'] = $covaDaignosticReport->id;
                if ($CovaSalesSummaryReport && !empty($CovaSalesSummaryReport->average_retail_price)) {
                    $cleanSheet['average_price']  = $CovaSalesSummaryReport->average_retail_price ?? "0.00";
                } else {
                    $cleanSheet['average_price'] = "0.00";
                }
                $cleanSheet['average_cost'] = "0.00";
                if (isset($CovaSalesSummaryReport->total_Cost) && $CovaSalesSummaryReport->total_Cost != null && isset($CovaSalesSummaryReport->net_sold) && $CovaSalesSummaryReport->net_sold != null) {
                    if (!empty((double)$CovaSalesSummaryReport->total_Cost) && !empty((double)$CovaSalesSummaryReport->net_sold)) {
                        $cleanSheet['average_cost'] = $this->avgCostForCova($CovaSalesSummaryReport);
                        $cleanSheet['report_price_og'] = $cleanSheet['average_cost'];
                    }
                }
                if (isset($lpOffer->unit_cost) && (double)$lpOffer->unit_cost != 0 &&  $cleanSheet['average_cost'] == 0) {
                    $cleanSheet['average_cost'] = GeneralFunctions::formatAmountValue($lpOffer->unit_cost);
                }
                $cleanSheet['barcode'] = $lpOffer->GTin;
                $cleanSheet['variable'] = null;
                $cleanSheet['retailerReportSubmission_id'] = $retailerReportSubmission->id;
                if ($covaDaignosticReport->other_additions_units > 0) {
                    $cleanSheet['transfer_in'] = $covaDaignosticReport->other_additions_units;
                } else {
                    $cleanSheet['transfer_in'] = 0;
                }

                if ($covaDaignosticReport->other_reductions_units < 0) {
                    $cleanSheet['transfer_out'] = str_replace('-', '', $covaDaignosticReport->other_reductions_units);
                } else {
                    $cleanSheet['transfer_out'] = 0;
                }
                $cleanSheet['pos'] = $retailerReportSubmission->pos;
                $cleanSheet['reconciliation_date'] = $retailerReportSubmission->date;
                $cleanSheet['opening_inventory_units'] = $covaDaignosticReport->opening_inventory_units ?? "0";
                $cleanSheet['closing_inventory_units'] = $covaDaignosticReport->closing_inventory_units ?? "0";
                $cleanSheet['flag'] = '2';
                $cleanSheet['comments'] = 'Record found in the Offers';
                $cleanSheet['dqi_flag'] = 1;
                $cleanSheet['ic_id'] = null;
                /**************************************************************************/
                $TotalQuantityGet = $cleanSheet['purchased'];
                $TotalUnitCostGet = $cleanSheet['average_cost'];
                /***************************************************************************/
                $TotalPurchaseCostMake = (float)$TotalQuantityGet * (float)$TotalUnitCostGet;
                $FinalDQIFEEMake = (float)trim($lpOffer->data, '%') * 100;
                $FinalFeeInDollar = (float)$TotalPurchaseCostMake * $FinalDQIFEEMake / 100;
                $cleanSheet['dqi_per'] = $FinalDQIFEEMake;
                $cleanSheet['dqi_fee'] = number_format($FinalFeeInDollar,2);
                /**************************************************************************/

                $this->insertOfferInInternalCatalouge($lpOffer, $this->avgCostForCova($CovaSalesSummaryReport), $provinceName);
            } else {
                $this->addDataCleansheet($retailerReportSubmission, $covaDaignosticReport,  $cleanSheet);
                $cleanSheet['retailer_id'] = $retailerReportSubmission->retailer_id;
                $cleanSheet['offer_id'] = null;
                $cleanSheet['lp_id'] = null;
                $cleanSheet['retailer_name'] = $retailerName;
                $cleanSheet['lp_name'] = null;
                $cleanSheet['thc_range'] = null;
                $cleanSheet['cbd_range'] = null;
                $cleanSheet['size_in_grams_g'] = null;
                $cleanSheet['location'] = $retailerReportSubmission->location;
                $cleanSheet['province'] = $provinceName;
                $cleanSheet['province_slug'] = $provinceSlug;
                $cleanSheet['product_name'] = $covaDaignosticReport->product_name;
                $cleanSheet['category'] = $CovaSalesSummaryReport->category ?? '';
                $cleanSheet['brand'] =  $CovaSalesSummaryReport->brand ?? '';
                $cleanSheet['sold'] = $covaDaignosticReport->quantity_sold_units ?? "0";
                $cleanSheet['purchased'] = $covaDaignosticReport->quantity_purchased_units;
//                if((int) $cleanSheet['purchased'] > 0){
//                    $checkCarveout = $this->checkCarveOuts($retailerReportSubmission, $provinceSlug, $provinceName,null,null,null);
//                    $cleanSheet['c_flag'] = $checkCarveout ? 'yes' : 'no';
//                }
//                else{
                $cleanSheet['c_flag'] = '';
//                }
                $cleanSheet['report_id'] = $covaDaignosticReport->id;
                if ($CovaSalesSummaryReport && !empty($CovaSalesSummaryReport->average_retail_price)) {
                    $cleanSheet['average_price']  = $CovaSalesSummaryReport->average_retail_price ?? "0.00";
                } else {
                    $cleanSheet['average_price'] = "0.00";
                }
                $cleanSheet['average_cost'] = $this->avgCostForCova($CovaSalesSummaryReport);
                $cleanSheet['report_price_og'] = $cleanSheet['average_cost'];
                $cleanSheet['variable'] = null;
                $cleanSheet['retailerReportSubmission_id'] = $retailerReportSubmission->id;
                if ($covaDaignosticReport->other_additions_units > 0) {
                    $cleanSheet['transfer_in'] = $covaDaignosticReport->other_additions_units;
                } else {
                    $cleanSheet['transfer_in'] = 0;
                }

                if ($covaDaignosticReport->other_reductions_units < 0) {
                    $cleanSheet['transfer_out'] = str_replace('-', '', $covaDaignosticReport->other_reductions_units);
                } else {
                    $cleanSheet['transfer_out'] = 0;
                }
                $cleanSheet['pos'] = $retailerReportSubmission->pos;
                $cleanSheet['reconciliation_date'] = $retailerReportSubmission->date;
                $cleanSheet['opening_inventory_units'] = $covaDaignosticReport->opening_inventory_units ?? "0";
                $cleanSheet['closing_inventory_units'] = $covaDaignosticReport->closing_inventory_units ?? "0";
                $cleanSheet['flag'] = '0';
                $cleanSheet['comments'] = 'Record not found in the Master Catalog And Offer';
                $cleanSheet['dqi_flag'] = 0;
                $cleanSheet['ic_id'] = null;
                $cleanSheet['dqi_per'] = '';
                $cleanSheet['dqi_fee'] = '';
            }
        }
//        $cleanSheet->save();
//        $updateCleanSheetID = DB::table('cova_diagnostic_reports')->where('id', $covaDaignosticReport->id)->update(['clean_sheet_id' => $cleanSheet->id, 'entry_status' => 'done']);

        $cleanSheet['sold'] = $this->sanitizeNumeric($cleanSheet['sold']);
        $cleanSheet['purchased'] = $this->sanitizeNumeric($cleanSheet['purchased']);
        $cleanSheet['average_price'] = $this->sanitizeNumeric($cleanSheet['average_price']);
        $cleanSheet['report_price_og'] = $this->sanitizeNumeric($cleanSheet['report_price_og']);
        $cleanSheet['average_cost'] = $this->sanitizeNumeric($cleanSheet['average_cost']);

        if ($cleanSheet['average_cost'] === 0.0 || $cleanSheet['average_cost'] === 0) {
            $cleanSheet['average_cost'] = GeneralFunctions::checkAvgCostCleanSheet($cleanSheet['sku'],$cleanSheet['province']);
        }

        return $cleanSheet;
    }
    private function avgCostForCova($CovaSalesSummaryReport)
    {
        //        if (!empty($CovaSalesSummaryReport->total_Cost) && !empty($CovaSalesSummaryReport->net_sold) && $CovaSalesSummaryReport->net_sold != 0) {
        //            $net_sold = str_replace('$', '', trim($CovaSalesSummaryReport->net_sold));
        //            $total_Cost = str_replace('$', '', trim($CovaSalesSummaryReport->total_Cost));
        //        } else {
        //            $net_sold = 1;
        //            $total_Cost = 0;
        //        }
        //        return ($total_Cost != 0) ? (float)$total_Cost / (float)$net_sold : "0.00";
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
    private function getICMatchedData($sku_key, $sku_value, $barcode_key, $barcode_value, $product_key, $product_value, $provinceName, $provinceSlug)
    {
        if (!empty($barcode_value) && !empty($sku_value)) {
            $CovaGtinMatchedData = $this->matchICBarcodeSku($barcode_value, $sku_value, $provinceName, $provinceSlug);
        } elseif (!empty($barcode_value) && empty($sku_value)) {
            $CovaGtinMatchedData = $this->matchICBarcode($barcode_value, $provinceName, $provinceSlug);
        } elseif (!empty($sku_value)) {
            $CovaGtinMatchedData = $this->matchICSku($sku_value, $provinceName, $provinceSlug);
        } else {
            $CovaGtinMatchedData = $this->matchICProductName($product_value, $provinceName, $provinceSlug);
        }
        if (empty($CovaGtinMatchedData) && !empty($barcode_value)) {
            $CovaGtinMatchedData = $this->matchICBarcode($barcode_value, $provinceName, $provinceSlug);
        }
        return  $CovaGtinMatchedData ?? '';
    }
    private function getDQIMatchedData($retailerReportSubmission, $covaDaignosticReport, $provinceName, $provinceSlug)
    {
        if ($retailerReportSubmission->province == 'ON' || $retailerReportSubmission->province == 'Ontario') {
            return $this->DQISummaryFlag($retailerReportSubmission, trim($covaDaignosticReport->ocs_sku),  trim($covaDaignosticReport->ontario_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug);
        } elseif ($retailerReportSubmission->province == 'AB' || $retailerReportSubmission->province == 'Alberta') {
            return $this->DQISummaryFlag($retailerReportSubmission, trim($covaDaignosticReport->aglc_sku),  trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug);
        } elseif ($retailerReportSubmission->province == 'MB' || $retailerReportSubmission->province == 'Manitoba') {
            return $this->DQISummaryFlag($retailerReportSubmission, trim($covaDaignosticReport->new_brunswick_sku),  trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug);
        } elseif ($retailerReportSubmission->province == 'BC' || $retailerReportSubmission->province == 'British Columbia') {
            return $this->DQISummaryFlag($retailerReportSubmission, trim($covaDaignosticReport->new_brunswick_sku),  trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug);
        } elseif ($retailerReportSubmission->province == 'SK' || $retailerReportSubmission->province == 'Saskatchewan') {
            return $this->DQISummaryFlag($retailerReportSubmission, trim($covaDaignosticReport->new_brunswick_sku),  trim($covaDaignosticReport->saskatchewan_barcode_upc), $covaDaignosticReport->product_name, $provinceName, $provinceSlug);
        }
    }
    private function MatchedOffer($retailerReportSubmission, $provinceName, $provinceSlug, $sku, $barcode, $product_name)
    {
        if ((!empty($sku) && !empty($barcode)) || (!empty($sku) && empty($barcode))) {
            $lpOffer = $this->matchOfferSku($retailerReportSubmission->date, $sku, $provinceName, $provinceSlug,$retailerReportSubmission->retailer_id);
        } elseif (empty($sku) && !empty($barcode)) {
            $lpOffer = $this->matchOfferBarcode($retailerReportSubmission->date, $barcode, $provinceName, $provinceSlug,$retailerReportSubmission->retailer_id);
        } elseif (empty($sku) && empty($barcode) && !empty($product_name)) {
            $lpOffer = $this->matchOfferProductName($retailerReportSubmission->date, $product_name, $provinceName, $provinceSlug,$retailerReportSubmission->retailer_id);
        } elseif ((!empty($sku))) {
            $lpOffer = $this->matchOfferSku($retailerReportSubmission->date, $sku, $provinceName, $provinceSlug,$retailerReportSubmission->retailer_id);
        } elseif (empty($sku) && !empty($product_name)) {
            $lpOffer = $this->matchOfferProductName($retailerReportSubmission->date, $product_name, $provinceName, $provinceSlug,$retailerReportSubmission->retailer_id);
        }
        return $lpOffer ?? '';
    }
    private function getMatchedOffer($retailerReportSubmission, $covaDaignosticReport, $provinceName, $provinceSlug)
    {
        if ($retailerReportSubmission->province == 'ON' || $retailerReportSubmission->province == 'Ontario') {
            return  $this->MatchedOffer($retailerReportSubmission, $provinceName, $provinceSlug, trim($covaDaignosticReport->ocs_sku), trim($covaDaignosticReport->ontario_barcode_upc), $covaDaignosticReport->product_name);
        } elseif ($retailerReportSubmission->province == 'AB' || $retailerReportSubmission->province == 'Alberta') {
            return  $this->MatchedOffer($retailerReportSubmission, $provinceName, $provinceSlug, trim($covaDaignosticReport->aglc_sku), trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name);
        } elseif ($retailerReportSubmission->province == 'MB' || $retailerReportSubmission->province == 'Manitoba') {
            return  $this->MatchedOffer($retailerReportSubmission, $provinceName, $provinceSlug, trim($covaDaignosticReport->new_brunswick_sku), trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name);
        } elseif ($retailerReportSubmission->province == 'BC' || $retailerReportSubmission->province == 'British Columbia') {
            return  $this->MatchedOffer($retailerReportSubmission, $provinceName, $provinceSlug, trim($covaDaignosticReport->new_brunswick_sku), trim($covaDaignosticReport->manitoba_barcode_upc), $covaDaignosticReport->product_name);
        } elseif ($retailerReportSubmission->province == 'SK' || $retailerReportSubmission->province == 'Saskatchewan') {
            return  $this->MatchedOffer($retailerReportSubmission, $provinceName, $provinceSlug, trim($covaDaignosticReport->new_brunswick_sku), trim($covaDaignosticReport->saskatchewan_barcode_upc), $covaDaignosticReport->product_name);
        }
    }
    private function addDataCleansheet($retailerReportSubmission, $covaDaignosticReport, &$cleanSheet)
    {
        if ($retailerReportSubmission->province == 'ON' || $retailerReportSubmission->province == 'Ontario') {
            $cleanSheet['sku'] = trim($covaDaignosticReport->ocs_sku);
            $cleanSheet['barcode'] = trim($covaDaignosticReport->ontario_barcode_upc);
        } elseif ($retailerReportSubmission->province == 'AB' || $retailerReportSubmission->province == 'Alberta') {
            $cleanSheet['sku'] = trim($covaDaignosticReport->aglc_sku);
            $cleanSheet['barcode'] = trim($covaDaignosticReport->manitoba_barcode_upc);
        } elseif ($retailerReportSubmission->province == 'MB' || $retailerReportSubmission->province == 'Manitoba') {
            $cleanSheet['sku'] = trim($covaDaignosticReport->new_brunswick_sku);
            $cleanSheet['barcode'] = trim($covaDaignosticReport->manitoba_barcode_upc);
        } elseif ($retailerReportSubmission->province == 'BC' || $retailerReportSubmission->province == 'British Columbia') {
            $cleanSheet['sku'] = trim($covaDaignosticReport->new_brunswick_sku);
            $cleanSheet['barcode'] = trim($covaDaignosticReport->manitoba_barcode_upc);
        } elseif ($retailerReportSubmission->province == 'SK' || $retailerReportSubmission->province == 'Saskatchewan') {
            $cleanSheet['sku'] = trim($covaDaignosticReport->new_brunswick_sku);
            $cleanSheet['barcode'] = trim($covaDaignosticReport->saskatchewan_barcode_upc);
        }
    }
}
