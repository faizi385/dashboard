<?php

namespace App\Traits;

use App\Models\Lp;
use Carbon\Carbon;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\ProductVariation;
use App\Models\ProfitTechReport;
use App\Helpers\GeneralFunctions;
use Illuminate\Support\Facades\Log;

trait ProfitTechIntegration
{
    /**
     * Process ProfitTech reports and save to CleanSheet.
     *
     * @param array $reports
     * @return void
     */
    public function mapProfitTechCatalouge($profitTechReport, $report)
    {
        Log::info('Processing ProfitTech reports:', ['report' => $report]);
        $cleanSheetData = []; $cleanSheetData['report_price_og'] = '0.00'; $cleanSheetData['product_price'] = '0.00'; $cleanSheetData['average_cost'] = '0.00';
        $cleanSheetData['offer_gtin_matched'] = '0'; $cleanSheetData['offer_sku_matched'] = '0';
        $cleanSheetData['address_id'] = $report->address_id; $cleanSheetData['created_at'] = now(); $cleanSheetData['updated_at'] = now();
        $retailer_id = $profitTechReport->report->retailer_id ?? null;
        $location = $profitTechReport->report->location ?? null;

        if (!$retailer_id) {
            Log::warning('Retailer ID not found for report:', ['report_id' => $report->id]);
        }
        $sku = $profitTechReport->product_sku;
        $gtin = null;
        $productName = null;
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
            $product = $this->matchICSku($profitTechReport->product_sku, $provinceName, $provinceSlug, $provinceId, $lpId);
        }
        if ($product) {
            $cleanSheetData['retailer_id'] = $retailer_id;
            $cleanSheetData['pos_report_id'] = $profitTechReport->id;
            $cleanSheetData['retailer_name'] = $retailerName ?? null;
            $cleanSheetData['thc_range'] = $product->thc_range;
            $cleanSheetData['cbd_range'] = $product->cbd_range;
            $cleanSheetData['size_in_gram'] = $product->product_size;
            $cleanSheetData['location'] = $location;
            $cleanSheetData['province'] = $provinceName;
            $cleanSheetData['province_slug'] = $provinceSlug;
            $cleanSheetData['province_id'] =  $provinceId ;
            $cleanSheetData['sku'] = $sku;
            $cleanSheetData['product_name'] =  $productName;
            $cleanSheetData['category'] = $product->category;
            $cleanSheetData['brand'] = $product->brand;
            $cleanSheetData['sold'] = $profitTechReport->quantity_sold_instore_units ?? '0';
            $cleanSheetData['purchase'] = $profitTechReport->quantity_purchased_units ?? '0';
            $cleanSheetData['average_price'] = $this->profitech_averge_price($profitTechReport, $provinceName);
            $cleanSheetData['report_price_og'] = '0.00';
            if($product->price_per_unit != "0.00" && ((float)$product->price_per_unit > 0.00 || (float)$product->price_per_unit < 0.00)) {
                $cleanSheetData['product_price'] = GeneralFunctions::formatAmountValue($product->price_per_unit) ?? '0.00';
            }
            else{
                $cleanSheetData['product_price'] = "0.00";
            }
            $cleanSheetData['barcode'] = $gtin;
            $cleanSheetData['report_id'] = $report->id;

            if($profitTechReport->quantity_purchased_units_transfer > 0 || $profitTechReport->other_additions_units){
                $cleanSheetData['transfer_in'] = $profitTechReport->quantity_purchased_units_transfer + $profitTechReport->other_additions_units ;
            }
            else{
                $cleanSheetData['transfer_in'] = 0 ;
            }
            if($profitTechReport->quantity_sold_units_transfer> 0 || $profitTechReport->other_reductions_units >0){
                $cleanSheetData['transfer_out'] = $profitTechReport->quantity_sold_units_transfer + $profitTechReport->other_reductions_units ;
            }
            else{
                $cleanSheetData['transfer_out'] = 0;
            }
            $cleanSheetData['pos'] = $report->pos;
            $cleanSheetData['reconciliation_date'] = $report->date;
            $cleanSheetData['opening_inventory_unit'] = $profitTechReport->opening_inventory_units ?? '0';
            $cleanSheetData['closing_inventory_unit'] = $profitTechReport->closing_inventory_units ?? '0';
            $cleanSheetData['product_variation_id'] = $product->id;
            $cleanSheetData['dqi_per'] = 0.00;
            $cleanSheetData['dqi_fee'] = 0.00;
            list($cleanSheetData, $offer) = $this->DQISummaryFlag($cleanSheetData,$report, $profitTechReport->product_sku, '', '', $provinceName, $provinceSlug, $provinceId,$lpId );
            if (!empty($offer)) {
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['average_cost'] = GeneralFunctions::formatAmountValue($offer->unit_cost) ?? "0.00";
                if ((int)$cleanSheetData['purchase'] > 0) {
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    $cleanSheetData['carveout_id'] = $checkCarveout->id ?? null;
                } else {
                    $cleanSheetData['c_flag'] = '';
                    $cleanSheetData['carveout_id'] = null;
                }
                $cleanSheetData['dqi_flag'] = 1;
                $cleanSheetData['flag'] = '3';
                $calculatedDQI = $this->calculateDQI($cleanSheetData['purchase'],$cleanSheetData['average_cost'],$offer->data_fee);
                $cleanSheetData['dqi_per'] = $calculatedDQI['dqi_per'];
                $cleanSheetData['dqi_fee'] = $calculatedDQI['dqi_fee'];
                $cleanSheetData['comment'] = 'Record found in the Product Catalog and Offer';
            } else {
                $cleanSheetData['offer_id'] = null;
                $cleanSheetData['carveout_id'] = null;
                $cleanSheetData['c_flag'] = '';
                $cleanSheetData['dqi_flag'] = 0;
                $cleanSheetData['average_cost'] = 0;
                $cleanSheetData['flag'] = '1';
                $cleanSheetData['comment'] = 'Record found in the Product Catalog';
            }
        } else {
            Log::warning('Product not found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin, 'report_data' => $report]);
            $offer = null;
            if (!empty($sku)) {
                $offer = $this->matchOfferSku($report->date,$sku,$provinceName,$provinceSlug,$provinceId,$report->retailer_id,$lpId);
            }
            if ($offer) {
                $cleanSheetData['retailer_id'] = $retailer_id;
                $cleanSheetData['offer_id'] = $offer->id;
                $cleanSheetData['pos_report_id'] = $profitTechReport->id;
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
                $cleanSheetData['sold'] = $profitTechReport->quantity_sold_instore_units ?? '0';
                $cleanSheetData['purchase'] = $profitTechReport->quantity_purchased_units ?? '0';
                if((int) $cleanSheetData['purchase'] > 0){
                    $checkCarveout = $this->checkCarveOuts($report,$provinceId, $provinceSlug, $provinceName,$lpId,$lpName,$offer->provincial_sku);
                    $cleanSheetData['c_flag'] = $checkCarveout ? 'yes' : 'no';
                    $cleanSheetData['carveout_id'] = $checkCarveout->id ?? null;
                }
                else{
                    $cleanSheetData['c_flag'] = '';
                    $cleanSheetData['carveout_id'] = null;
                }

                $cleanSheetData['average_price'] = $this->profitech_averge_price($profitTechReport, $provinceName);
                $profitechAverageCost = GeneralFunctions::formatAmountValue(trim(str_replace('$', '', trim($offer->unit_cost)))) ?? '0.00';
                $cleanSheetData['average_cost'] = $profitechAverageCost;
                $cleanSheetData['report_price_og'] = '0.00';
                $cleanSheetData['product_price'] = '0.00';
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                if($profitTechReport->quantity_purchased_units_transfer > 0 || $profitTechReport->other_additions_units){
                    $cleanSheetData['transfer_in'] = $profitTechReport->quantity_purchased_units_transfer + $profitTechReport->other_additions_units ;
                }
                else{
                    $cleanSheetData['transfer_in'] = 0 ;
                }
                if($profitTechReport->quantity_sold_units_transfer> 0 || $profitTechReport->other_reductions_units >0){
                    $cleanSheetData['transfer_out'] = $profitTechReport->quantity_sold_units_transfer + $profitTechReport->other_reductions_units ;
                }
                else{
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $profitTechReport->opening_inventory_units ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $profitTechReport->closing_inventory_units ?? '0';
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
                $cleanSheetData['pos_report_id'] = $profitTechReport->id;
                $cleanSheetData['retailer_name'] = $retailerName;
                $cleanSheetData['thc_range'] = null;
                $cleanSheetData['cbd_range'] = null;
                $cleanSheetData['size_in_gram'] = null;
                $cleanSheetData['location'] = $location;
                $cleanSheetData['province'] = $provinceName;
                $cleanSheetData['province_slug'] = $provinceSlug;
                $cleanSheetData['province_id'] =  $provinceId ;
                $cleanSheetData['sku'] = $sku;
                $cleanSheetData['product_name'] =  $productName;
                $cleanSheetData['category'] = null;
                $cleanSheetData['brand'] = null;
                $cleanSheetData['sold'] = $profitTechReport->quantity_sold_instore_units ?? '0';
                $cleanSheetData['purchase'] = $profitTechReport->quantity_purchased_units ?? '0';
                $cleanSheetData['average_price'] = $profitTechReport->average_price;
                $cleanSheetData['average_cost'] = '0.00';
                $cleanSheetData['report_price_og'] = '0.00';
                $cleanSheetData['product_price'] = '0.00';
                $cleanSheetData['barcode'] = $gtin;
                $cleanSheetData['report_id'] = $report->id;
                $cleanSheetData['c_flag'] = '';
                if($profitTechReport->transfer > 0){
                    $cleanSheetData['transfer_in'] = $profitTechReport->transfer;
                    $cleanSheetData['transfer_out'] = 0;
                }
                elseif($profitTechReport->transfer < 0){
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = str_replace('-','',$profitTechReport->transfer);
                }
                else{
                    $cleanSheetData['transfer_in'] = 0;
                    $cleanSheetData['transfer_out'] = 0;
                }
                $cleanSheetData['pos'] = $report->pos;
                $cleanSheetData['reconciliation_date'] = $report->date;
                $cleanSheetData['opening_inventory_unit'] = $profitTechReport->opening_inventory_units ?? '0';
                $cleanSheetData['closing_inventory_unit'] = $profitTechReport->closing_inventory_units ?? '0';
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


    /**
     * Save data to CleanSheet.
     *
     * @param array $cleanSheetData
     * @return void
     */
    public function saveToCleanSheet($cleanSheetData)
    {
        try {
            CleanSheet::insert($cleanSheetData);
            Log::info('Data saved to CleanSheet successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving data to CleanSheet:', ['error' => $e->getMessage()]);
        }
    }
    public function profitech_averge_price($profitTechReport, $provinceName){

        $quantitysoldunits =  (double)trim($profitTechReport->opening_inventory_units);
        $quantitysoldvalue = \App\Helpers\GeneralFunctions::formatAmountValue($profitTechReport->opening_inventory_value);;
        if ($quantitysoldunits == '0' || $quantitysoldunits == '0.00') {
            $quantitysoldunits = 1;
        }

        if($quantitysoldunits < 0 && $quantitysoldvalue < 0){
            $average_price = $quantitysoldvalue / $quantitysoldunits;
        } else {
            $average_price = (double)($quantitysoldvalue) / (double)($quantitysoldunits);
        }

        if($average_price == 0 || $average_price == '0.00'){
            $average_price = '0.00';
        }

        return (double)$average_price;
    }
    public function profitech_averge_cost($product,$createdAt,$provinceName,$provinceSlug,$lpId){
        $average_cost = 0.00;
        $createdAtMonth = Carbon::parse($createdAt)->addMonth()->format('m');
        $createdAtYear = Carbon::parse($createdAt)->addMonth()->format('Y');
        $lpOffer = ProductVariation::whereMonth('created_at', $createdAtMonth)->whereYear('created_at', $createdAtYear)
            ->where('provincial_sku', $product->sku)
            ->where('province', $provinceName)
            ->where('lp_id',$lpId)
            ->first();
        if(empty($lpOffer)){
            $lpOffer = ProductVariation::whereMonth('created_at', $createdAtMonth)->whereYear('created_at', $createdAtYear)
                ->where('provincial_sku', $product->sku)
                ->where('province', $provinceSlug)
                ->where('lp_id',$lpId)
                ->first();
        }
        if(!empty($lpOffer)){
            $average_cost =  \App\Helpers\GeneralFunctions::formatAmountValue($lpOffer->unit_cost);
        }
        if($average_cost == '0.00' || $average_cost == '0') {
            $average_cost =  trim(str_replace('$', '', trim($product->price_per_unit)));
        }

        return $average_cost;
    }
}
