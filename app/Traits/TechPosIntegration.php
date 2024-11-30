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
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00'; $cleanSheetData['product_price'] = '0.00'; $cleanSheetData['average_cost'] = '0.00';
        $cleanSheetData['offer_gtin_matched'] = '0'; $cleanSheetData['offer_sku_matched'] = '0';
        $cleanSheetData['address_id'] = $report->address_id; $cleanSheetData['created_at'] = now(); $cleanSheetData['updated_at'] = now();
        $retailer_id = $techPOSReport->report->retailer_id ?? null;
        $location = $techPOSReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $techPOSReport->sku;
        $gtin = null;
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

        $lp = Lp::where('id',$retailer->lp_id)->first();
        $cleanSheetData['lp_id'] = $lpId = $retailer->lp_id;
        $cleanSheetData['lp_name'] = $lpName = $lp->name;

        if (!empty($sku)) {
            $product = $this->matchICSku($techPOSReport->sku,$provinceName,$provinceSlug,$provinceId, $lpId );
        }
//        if (!empty($productName) && empty($product)){
//            $product = $this->matchICProductName($techPOSReport->productname,$provinceName,$provinceSlug,$provinceId, $lpId );
//        }
        if ($product) {
            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $techPOSReport->id;
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
            $cleanSheetData['sold'] = $techPOSReport->quantitysoldinstoreunits ?? '0';
            $cleanSheetData['purchase'] = $techPOSReport->quantitypurchasedunits ?? '0';
            $cleanSheetData['average_price'] = $this->techpos_averge_price($techPOSReport);
            $techPOSReportCost = \App\Helpers\GeneralFunctions::formatAmountValue($techPOSReport->costperunit) ?? '0';
            if ($techPOSReportCost != "0.00" && $techPOSReportCost != '0' && (float)$techPOSReportCost != 0.00) {
                $cleanSheetData['report_price_og'] = $techPOSReportCost;
            }
            else{
                $cleanSheetData['report_price_og'] = "0.00";
            }
            if($product->price_per_unit != "0.00") {
                $cleanSheetData['product_price'] = GeneralFunctions::formatAmountValue($product->price_per_unit) ?? '0.00';
            }
            else{
                $cleanSheetData['product_price'] = "0.00";
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
            $cleanSheetData['opening_inventory_unit'] = $techPOSReport->openinventoryunits ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $techPOSReport->closinginventoryunits ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            list($cleanSheetData, $offer) = $this->DQISummaryFlag($cleanSheetData,$report,$techPOSReport->sku,'',$techPOSReport->productname,$provinceName,$provinceSlug,$provinceId,$lpId );
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
                $cleanSheetData['flag'] = '1';
                $cleanSheetData['comment'] = 'Record found in the Product Catalog';
            }
        } else {
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id, $lpId );
                $cleanSheetData['offer_sku_matched'] = '1';
            }
//            if (!empty($productName) && empty($offer)) {
//                $offer = $this->matchOfferProductName($report->date,$productName,$provinceName,$provinceSlug,$provinceId,$report->retailer_id, $lpId );
//            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $techPOSReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['thc_range'] = $offer->thc_range;
                $cleanSheetData['cbd_range'] = $offer->cbd_range;
                $cleanSheetData['size_in_gram'] = $offer->product_size;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $offer->province;
                $cleanSheetData['province_slug'] = $offer->province_slug;
                $cleanSheetData['province_id'] =  $provinceId ;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] = $productName;
                $cleanSheetData['category'] = $offer->category;
                $cleanSheetData['brand'] = $offer->brand;
                $cleanSheetData['sold'] = $techPOSReport->sold;
                $cleanSheetData['purchase'] = $techPOSReport->purchased ?? '0';
                $cleanSheetData['average_price'] = $this->techpos_averge_price($techPOSReport);
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    $cleanSheetData['carveout_id'] = $checkCarveout->id ?? null;
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                    $cleanSheetData['carveout_id'] = null;
                }
                $techPOSReportCost =\App\Helpers\GeneralFunctions::formatAmountValue($techPOSReport->costperunit) ?? '0';
                if ($techPOSReportCost != "0.00" && $techPOSReportCost != '0' && (float)$techPOSReportCost != 0.00) {
                    $cleanSheetData['report_price_og'] = $techPOSReportCost;
                }
                else{
                    $cleanSheetData['report_price_og'] = "0.00";
                }
                $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($offer->unit_cost) ?? '0.00';
                $cleanSheetData['product_price'] = "0.00";
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
                $cleanSheetData['opening_inventory_unit'] = $techPOSReport->openinventoryunits ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $techPOSReport->closinginventoryunits ?? '0';
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
                $cleanSheetData['pos_report_id'] = $techPOSReport->id;
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
                $cleanSheetData['sold'] = $techPOSReport->sold;
                $cleanSheetData['purchase'] = $techPOSReport->purchased ?? '0';
                $cleanSheetData['average_price'] = $techPOSReport->average_price;
                $cleanSheetData['report_price_og'] = $techPOSReport->average_cost;
                $cleanSheetData['average_cost'] = '0.00';
                $cleanSheetData['product_price'] = "0.00";
                $cleanSheetData['barcode'] = $gtin;
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
                $cleanSheetData['opening_inventory_unit'] = $techPOSReport->openinventoryunits ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $techPOSReport->closinginventoryunits ?? '0';
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

    public function techpos_averge_price($techPOSReport){

        $quantitysoldunits =  (double)trim($techPOSReport->quantitysoldinstoreunits);
        $quantitysoldvalue =  \App\Helpers\GeneralFunctions::formatAmountValue($techPOSReport->quantitysoldinstorevalue);
        if ($quantitysoldunits == '0' || $quantitysoldunits == '0.00') {
            $quantitysoldunits = 1;
        }
        $average_price = $quantitysoldvalue / $quantitysoldunits;

        return $average_price;
    }
}
