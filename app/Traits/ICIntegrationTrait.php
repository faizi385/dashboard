<?php

namespace App\Traits;

use App\Helpers\GeneralFunctions;
use App\Models\InternalMasterCatalouge;
use App\Models\Lp;
use App\Models\LpVariableFeeStructure;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\Offer;
use App\Models\Carveout;
use App\Models\CleanSheet;
use App\Models\Retailer;
use App\Traits\TechPosIntegration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\GreenlineICIntegration;

trait ICIntegrationTrait
{
    use CovaICIntegration, GreenlineICIntegration, TechPosIntegration,ProfitTechIntegration,BarnetIntegration,OtherPOSIntegration,TendyIntegration,IdealIntegration,GlobalTillIntegration;
    public function covaMasterCatalouge($covaDaignosticReport, $report)
    {
        return $this->mapCovaCatalouge($covaDaignosticReport, $report);
    }
    public function greenlineMasterCatalouge($greenlineReport, $report)
    {
        return $this->mapGreenlineCatalouge($greenlineReport, $report);
    }
    public function techposMasterCatalouge($techPOSReport, $report)
    {
        return $this->mapTechPosCatalouge($techPOSReport, $report);
    }
    public function profitTechMasterCatalouge($profitTechReport, $report)
    {
        return $this->mapProfitTechCatalouge($profitTechReport, $report);
    }
    public function barnetMasterCatalog($barnetReport,$report)
    {
        return $this->mapBarnetCatalouge($barnetReport,$report);
    }
    public function otherPOSMasterCatalog($OtherPOSReport,$report)
    {
        return $this->  mapOtherPosCatalouge($OtherPOSReport,$report);
    }
    public function tendyMasterCatalog($tendyDaignosticReport,$report)
    {
        return $this-> mapTendyPosCatalouge($tendyDaignosticReport,$report);
    }
    public function idealMasterCatalogue($idealDaignosticReport,$report)
    {
        return $this->mapIdealCatalouge($idealDaignosticReport,$report);
    }
    public function globaltillMasterCatalogue($gobatellDiagnosticReport,$report)
    {
        return $this->mapGlobaltillMasterCatalouge($gobatellDiagnosticReport,$report);
    }

    public function matchICBarcode($barcode, $provinceName, $provinceSlug, $provinceId, $lpId)
    {
        // Match the barcode based on the lpId
        $product = $this->matchBarcode($barcode, $lpId); // Ensure the matchBarcode method is updated to handle lpId

        // If a product is found, filter by province-related conditions
        if (!empty($product)) {
            $product = $this->matchProvince($product, $provinceName, $provinceSlug, $provinceId);
        }

        return $product;
    }

    public function matchBarcode($barcode, $lpId)
    {
        $GeneralFunction = new GeneralFunctions;
        $Filterbarcode = $GeneralFunction->CleanGTIN($barcode);

        // Match by GTIN and lpId
        $product = ProductVariation::where('gtin', $Filterbarcode)
            ->where('lp_id', $lpId) // Filter by lpId
            ->get();

        // If no product is found, try matching with a '00' prefix
        if ($product->isEmpty()) {
            $product = ProductVariation::where('gtin', '00' . $Filterbarcode)
                ->where('lp_id', $lpId) // Filter by lpId
                ->get();
        }

        return $product;
    }

    public function matchICSku($sku, $provinceName, $provinceSlug, $provinceId, $lpId)
    {
        // Attempt to match based on SKU and lpId
        $product = ProductVariation::where('provincial_sku', trim($sku))
            ->where('lp_id', $lpId) // Filter by lpId
            ->get();

        // If products are found, filter by province-related conditions
        if (!empty($product)) {
            $product = $this->matchProvince($product, $provinceName, $provinceSlug, $provinceId);
        }

        return $product;
    }

    public function matchProvince($product, $provinceName, $provinceSlug, $provinceId)
    {
        $product = $product->where('province', trim($provinceName))->first();
        return $product;
    }
    public function matchICBarcodeSku($barcode, $sku, $provinceName, $provinceSlug, $provinceId, $lpId)
    {
        $GeneralFunction = new GeneralFunctions;
        $Filterbarcode = $GeneralFunction->CleanGTIN($barcode);

        // First attempt: Match based on SKU, GTIN, and lpId
        $product = ProductVariation::where('provincial_sku', trim($sku))
            ->where('gtin', $barcode)
            ->where('lp_id', $lpId) // Filter by lpId
            ->first();

        // If no product is found, attempt with cleaned barcode
        if (empty($product)) {
            $product = ProductVariation::where('gtin', $Filterbarcode)
                ->where('provincial_sku', trim($sku))
                ->where('lp_id', $lpId) // Filter by lpId
                ->first();

            // If still no product is found, attempt with barcode prefixed with '00'
            if (empty($product)) {
                $product = ProductVariation::where('gtin', '00' . $Filterbarcode)
                    ->where('provincial_sku', trim($sku))
                    ->where('lp_id', $lpId) // Filter by lpId
                    ->first();
            }
        }

        // If no product is found, attempt based on SKU and province name
        if (empty($product)) {
            $product = ProductVariation::where('provincial_sku', trim($sku))
                ->where('province', $provinceName)
                ->where('lp_id', $lpId) // Filter by lpId
                ->first();
        }

        return $product;
    }

    public function matchOfferBarcode($date, $barcode, $provinceName, $provinceSlug, $provinceId, $retailerId, $lpId)
    {
        $GeneralFunction = new GeneralFunctions;
        $barcode = $GeneralFunction->CleanGTIN($barcode);
        $barcode = '00' . $barcode;

        // First attempt: Match based on offer date, barcode, province, retailer and lp_id
        $offer = Offer::where('offer_date', $date)
            ->where('GTin', $barcode)
            ->where('province_id', $provinceId)
            ->where('retailer_id', $retailerId)
            ->where('lp_id', $lpId) // Filter by LP
            ->first();

        // If no offer is found, attempt without matching the lp_id
        if (empty($offer)) {
            $offer = Offer::where('offer_date', $date)
                ->where('GTin', $barcode)
                ->where('province_id', $provinceId)
                ->where('lp_id', $lpId)
                ->first();
        }

        // If still no offer found, attempt with a different matching logic
        if (empty($offer)) {
            $offer = $this->matchOfferBarcodeWithOutZero($date, $barcode, $provinceName, $provinceSlug, $provinceId, $retailerId, $lpId);
        }

        return $offer;
    }

    public function matchOfferBarcodeWithOutZero($date, $barcode, $provinceName, $provinceSlug, $provinceId, $retailerId, $lpId)
    {
        $GeneralFunction = new GeneralFunctions;
        $barcode = $GeneralFunction->CleanGTIN($barcode);

        // Attempt to match with lp_id
        $offer = Offer::where('offer_date', $date)
            ->where('gtin', $barcode)
            ->where('province_id', $provinceId)
            ->where('retailer_id', $retailerId)
            ->where('lp_id', $lpId) // Filter by LP
            ->first();

        // If no offer found, attempt without the lp_id
        if (empty($offer)) {
            $offer = Offer::where('offer_date', $date)
                ->where('gtin', $barcode)
                ->where('province_id', $provinceId)
                ->where('lp_id', $lpId)
                ->first();
        }

        return $offer;
    }

    public function matchOfferSku($date, $sku, $provinceName, $provinceSlug, $provinceId, $retailerId, $lpId)
    {
        $sku = trim($sku);


        $offer = Offer::where('offer_date', $date)
            ->where('provincial_sku', $sku)
            ->where('province_id', $provinceId)
            ->where('retailer_id', $retailerId)
            ->where('lp_id', $lpId)
            ->first();


        if (empty($offer)) {
            $offer = Offer::where('offer_date', $date)
                ->where('provincial_sku', $sku)
                ->where('province_id', $provinceId)
                ->where('lp_id', $lpId)
                ->first();
        }

        return $offer;
    }

    public function matchOfferProductName($date, $productName, $provinceName, $provinceSlug, $provinceId, $retailerId, $lpId)
    {
        // Attempt to match by product name and lp_id
        $offer = Offer::where('offer_date', $date)
            ->where('product_name', $productName)
            ->where('province_id', $provinceId)
            ->where('retailer_id', $retailerId)
            ->where('lp_id', $lpId) // Filter by LP
            ->first();

        // If no offer is found, attempt without the lp_id
        if (empty($offer)) {
            $offer = Offer::where('offer_date', $date)
                ->where('product_name', $productName)
                ->where('province_id', $provinceId)
                ->where('lp_id', $lpId)
                ->first();
        }

        return $offer;
    }

    public function matchICProductName($productName, $provinceName, $provinceSlug, $provinceId, $lpId)
    {
        // Match by product name and lpId
        $product = ProductVariation::where('product_name', $productName)
            ->where('lp_id', $lpId) // Filter by lpId
            ->get();

        if (!empty($product)) {
            $product = $this->matchProvince($product, $provinceName, $provinceSlug, $provinceId);
        }

        return $product;
    }

    public function DQISummaryFlag($report, $sku, $gtin, $productName, $provinceName, $provinceSlug, $provinceId ,$lpId)
    {
        $offer = null;
        if (!empty($sku)) {
            $offer = $this->matchOfferSku($report->date, $sku, $provinceName, $provinceSlug, $provinceId, $report->retailer_id,$lpId);
        }
        if (empty($offer) && !empty($barcode)) {
            $offer = $this->matchOfferBarcode($report->date, $barcode, $provinceName, $provinceSlug,$provinceId, $report->retailer_id,$lpId);
        }
        if (empty($offer) && !empty($productName)) {
            $offer = $this->matchOfferProductName($report->date, $productName, $provinceName, $provinceSlug, $provinceId, $report->retailer_id,$lpId);
        }
        if (!empty($offer)) {
            return $offer;
        }
        if (empty($offer)) {
            return null;
        }

        return $offer;
    }

    function sanitizeNumeric($value) {
        $removeCommaValue = str_replace(',','',$value);
        $removeCommaValue = str_replace('$','',$removeCommaValue);
        $removeCommaValue = trim($removeCommaValue);
        if (is_numeric($removeCommaValue)) {
            return strpos($removeCommaValue, '.') !== false ? (float)$removeCommaValue : (int)$removeCommaValue;
        }

        return 0;
    }

    public function checkCarveOuts($report, $province_id, $province_slug, $province_name,  $lpID ,$lpName, $sku)
    {
        $date = $report->date;
        $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
            ->where(function ($query) use ($lpID) {
                $query->where('lp_id', $lpID);
            })
            ->where(function ($query) use ($province_id) {
                $query->where('province_id', $province_id);
            })
            ->where(function ($query) use ($date) {
                $query->where('date', $date);

            })
            ->first();
            if ($checkCarveout) {
                if((!empty($checkCarveout->sku) || $checkCarveout->sku != null) && (!empty($checkCarveout->location) || $checkCarveout->location != null)){
                    $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                        ->where(function ($query) use ($lpID) {
                            $query->where('lp_id', $lpID);
                        })
                        ->where(function ($query) use ($province_id) {
                            $query->where('province_id', $province_id);
                        })
                        ->where(function ($query) use ($date) {
                            $query->where('date', $date);

                        })
                        ->where('sku',$sku)
                        ->where('location',$report->address_id)
                        ->first();
                }
                else if($checkCarveout->sku != null || !empty($checkCarveout->sku)){
                    $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                        ->where(function ($query) use ($lpID) {
                            $query->where('lp_id', $lpID);
                        })
                        ->where(function ($query) use ($province_id) {
                            $query->where('province_id', $province_id);
                        })
                        ->where(function ($query) use ($date) {
                            $query->where('date', $date);

                        })
                        ->where('sku',$sku)
                        ->first();
                }

                else if($checkCarveout->location != null || !empty($checkCarveout->location)){
                    $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                        ->where(function ($query) use ($lpID) {
                            $query->where('lp_id', $lpID);
                        })
                        ->where(function ($query) use ($province_id) {
                            $query->where('province_id', $province_id);
                        })
                        ->where(function ($query) use ($date) {
                            $query->where('date', $date);

                        })
                        ->where('location',$report->address_id)
                        ->first();
                }
            }

            if(!$checkCarveout || empty($checkCarveout)) {
                $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                    ->where(function ($query) use ($lpID) {
                        $query->where('lp_id', $lpID);
                    })
                    ->where(function ($query) use ($province_id) {
                        $query->where('province_id', $province_id);
                    })
                    ->where(function ($query) use ($date) {
                        $query->where('date', $date);

                    })
                    ->first();
                if ($checkCarveout) {
                    if((!empty($checkCarveout->sku) || $checkCarveout->sku != null) && (!empty($checkCarveout->loction) || $checkCarveout->location != null)){
                        $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                            ->where(function ($query) use ($lpID) {
                                $query->where('lp_id', $lpID);
                            })
                            ->where(function ($query) use ($province_id) {
                                $query->where('province_id', $province_id);
                            })
                            ->where(function ($query) use ($date) {
                                $query->where('date', $date);

                            })
                            ->where('sku',$sku)
                            ->where('location',$report->address_id)
                            ->first();
                    }
                    else if($checkCarveout->sku != null || !empty($checkCarveout->sku)){
                        $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                            ->where(function ($query) use ($lpID) {
                                $query->where('lp_id', $lpID);
                            })
                            ->where(function ($query) use ($province_id) {
                                $query->where('province_id', $province_id);
                            })
                            ->where(function ($query) use ($date) {
                                $query->where('date', $date);

                            })
                            ->where('sku',$sku)
                            ->first();
                    }

                    else if($checkCarveout->location != null || !empty($checkCarveout->location)){
                        $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                            ->where(function ($query) use ($lpID) {
                                $query->where('lp_id', $lpID);
                            })
                            ->where(function ($query) use ($province_id) {
                                $query->where('province_id', $province_id);
                            })
                            ->where(function ($query) use ($date) {
                                $query->where('date', $date);

                            })
                            ->where('location',$report->address_id)
                            ->first();
                    }
                }
            }
            return $checkCarveout;
    }
    public function calculateDQI($purchase,$average_cost,$lpOfferDataFee){
        $TotalQuantityGet = $purchase;
        $TotalUnitCostGet = $average_cost;
        $calculations = [];
        $TotalPurchaseCostMake = (float)$TotalQuantityGet * (float)$TotalUnitCostGet;
        $FinalDQIFEEMake = (float)trim($lpOfferDataFee, '%') * 100;
        $FinalFeeInDollar = (float)$TotalPurchaseCostMake * $FinalDQIFEEMake / 100;
        $calculations['dqi_per'] = $FinalDQIFEEMake;
        $calculations['dqi_fee'] = $FinalFeeInDollar;

        return $calculations;
    }
}
