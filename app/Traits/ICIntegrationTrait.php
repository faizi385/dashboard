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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\GreenlineICIntegration;

trait ICIntegrationTrait
{
    use CovaICIntegration, GreenlineICIntegration;
    public function covaMasterCatalouge($covaDaignosticReport, $report)
    {
        return $this->mapCovaMasterCatalouge($covaDaignosticReport, $report);
    }
    public function greenlineMasterCatalouge($greenlineReports, $report)
    {
        return $this->mapGreenlineCatalouge($greenlineReports, $report);
    }
    public function saveToCleanSheet(array $cleanSheetData)
    {
        try {
            CleanSheet::create($cleanSheetData);
            Log::info('Data saved to CleanSheet successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving data to CleanSheet:', ['error' => $e->getMessage()]);
        }
    }

    public function matchICBarcode($barcode, $provinceName, $provinceSlug, $provinceId)
    {
        $product = $this->matchBarcode($barcode);
        if (!empty($product)) {
            $product = $this->matchProvince($product, $provinceName, $provinceSlug, $provinceId);
        }
        return $product;
    }

    public function matchBarcode($barcode)
    {
        $GeneralFunction = new GeneralFunctions;
        $Filterbarcode = $GeneralFunction->CleanGTIN($barcode);
        $product = ProductVariation::where('gtin', $Filterbarcode)->get();
        if (empty($product)) {
            $product = ProductVariation::where('gtin', '00' . $Filterbarcode)->get();
        }
        return $product;
    }

    public function matchICSku($sku, $provinceName, $provinceSlug, $provinceId)
    {
        $product = ProductVariation::where('provincial_sku', trim($sku))->get();
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

    public function matchICBarcodeSku($barcode, $sku, $provinceName, $provinceSlug, $provinceId)
    {
        $GeneralFunction = new GeneralFunctions;
        $Filterbarcode = $GeneralFunction->CleanGTIN($barcode);
        $product = ProductVariation::where('provincial_sku', trim($sku))->where('gtin', $barcode)->first();
        if (empty($product)) {
            $product = ProductVariation::where('gtin', $Filterbarcode)->where('provincial_sku', trim($sku))->first();
            if (empty($product)) {
                $product = ProductVariation::where('gtin', '00' . $Filterbarcode)->where('provincial_sku', trim($sku))->first();
            }
        }
        if (!($product)) {
            $product = ProductVariation::where('provincial_sku', trim($sku))->where('province', $provinceName)->first();
        }

        return $product;
    }

    public function matchOfferBarcode($date, $barcode, $provinceName, $provinceSlug, $provinceId, $retailerId)
    {
        $GeneralFunction = new GeneralFunctions;
        $barcode = $GeneralFunction->CleanGTIN($barcode);
        $barcode = '00'.$barcode;

        $offer = Offer::where('offer_date', $date)
            ->where('GTin', $barcode)
            ->where('province_id', $provinceId)
            ->where('retailer_id', $retailerId)
            ->first();
        if(empty($offer)) {
            $offer = Offer::where('offer_date', $date)
                ->where('GTin', $barcode)
                ->where('province_id', $provinceId)
                ->first();
        }
        if(empty($offer)){
            $offer = $this->matchOfferBarcodeWithOutZero($date, $barcode, $provinceName, $provinceSlug, $provinceId, $retailerId);
        }


        return $offer;
    }

    public function matchOfferBarcodeWithOutZero($date, $barcode, $provinceName, $provinceSlug, $provinceId, $retailerId)
    {
        $GeneralFunction = new GeneralFunctions;
        $barcode = $GeneralFunction->CleanGTIN($barcode);

        $offer = Offer::where('offer_date', $date)
            ->where('gtin', $barcode)
            ->where('province_id', $provinceId)
            ->where('retailer_id', $retailerId)
            ->first();
        if(empty($offer)) {
            $offer = Offer::where('offer_date', $date)
                ->where('gtin', $barcode)
                ->where('province_id', $provinceId)
                ->first();
        }
        return $offer;
    }

    public function matchOfferSku($date, $sku, $provinceName, $provinceSlug, $provinceId, $retailerId)
    {
        $sku = trim($sku);

        $offer = Offer::where('offer_date', $date)
            ->where('provincial_sku', $sku)
            ->where('province_id', $provinceId)
            ->where('retailer_id', $retailerId)
            ->first();
        if(empty($offer)) {
            $offer = Offer::where('offer_date', $date)
                ->where('provincial_sku', $sku)
                ->where('province_id', $provinceId)
                ->first();
        }
        return $offer;
    }

    public function matchOfferProduct(string $sku, string $gtin)
    {
        $offer = Offer::where('provincial_sku', $sku)
            ->orWhere('gtin', $gtin)
            ->first();

        if ($offer) {
            Log::info('Offer matched by SKU and GTIN:', [
                'sku' => $sku,
                'gtin' => $gtin,
                'offer_id' => $offer->id
            ]);
            return $offer;
        }

        Log::warning('No offer found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin]);
        return null;
    }

    protected function matchICProductName($productName)
    {
        return Product::where('product_name', 'LIKE', "%{$productName}%")->first();
    }

    public function matchOfferProductName($date, $productName, $provinceName, $provinceSlug, $provinceId, $retailerId)
    {
        $offer = Offer::where('offer_date', $date)
            ->where('product_name', $productName)
            ->where('province_id', $provinceId)
            ->where('retailer_id', $retailerId)
            ->first();
        if(empty($offer)) {
            $offer = Offer::where('offer_date', $date)
                ->where('product_name', $productName)
                ->where('province_id', $provinceId)
                ->first();
        }
        return $offer;
    }

    protected function calculateDqiFee($greenlineReport, $item)
    {
        return ($greenlineReport->sold ?? 0) * ($item->average_cost ?? 0);
    }

    protected function calculateDqiPer($greenlineReport, $item)
    {
        return ($greenlineReport->sold ?? 0) / ($item->average_price ?? 1);
    }

    public function DQISummaryFlag($report, $sku, $gtin, $productName, $provinceName, $provinceSlug, $provinceId)
    {
        $offer = null;
//        if (!empty($gtin) && !empty($sku)) {
//            $offer = $this->matchOfferProduct($sku, $gtin);
//        } elseif (!empty($gtin)) {
//            $offer = $this->matchOfferBarcode($gtin);
//        } elseif (!empty($sku)) {
//            $offer = $this->matchOfferSku($sku);
//        }

        if (!empty($sku)) {
            $offer = $this->matchOfferSku($report->date, $sku, $provinceName, $provinceSlug, $provinceId, $report->retailer_id);
        }
        if (empty($offer) && !empty($barcode)) {
            $offer = $this->matchOfferBarcode($report->date, $barcode, $provinceName, $provinceSlug,$provinceId, $report->retailer_id);
        }
        if (empty($offer) && !empty($productName)) {
            $offer = $this->matchOfferProductName($report->date, $productName, $provinceName, $provinceSlug, $provinceId, $report->retailer_id);
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

    function getRetailerProvince($retailerReportSubmission)
    {
        if ($retailerReportSubmission->province == 'ON' || $retailerReportSubmission->province == 'Ontario') {
            $provinceDetail['province_name'] = 'Ontario';
            $provinceDetail['province_slug'] = 'ON';
        } elseif ($retailerReportSubmission->province == 'MB' || $retailerReportSubmission->province == 'Manitoba') {
            $provinceDetail['province_name'] = 'Manitoba';
            $provinceDetail['province_slug'] = 'MB';
        } elseif ($retailerReportSubmission->province == 'BC' || $retailerReportSubmission->province == 'British Columbia') {
            $provinceDetail['province_name'] = 'British Columbia';
            $provinceDetail['province_slug'] = 'BC';
        } elseif ($retailerReportSubmission->province == 'AB' || $retailerReportSubmission->province == 'Alberta') {
            $provinceDetail['province_name'] = 'Alberta';
            $provinceDetail['province_slug'] = 'AB';
        } elseif ($retailerReportSubmission->province == 'SK' || $retailerReportSubmission->province == 'Saskatchewan') {
            $provinceDetail['province_name'] = 'Saskatchewan';
            $provinceDetail['province_slug'] = 'SK';
        }
        return $provinceDetail;
    }

    public function getRetailerName($retailerId)
    {
        $retialer = Retailer::where('id',$retailerId)->first();
        if($retialer){
            return $retialer->DBA;
        }
        return '';
    }

    public function getlpID($lpName)
    {
        $lp = Lp::where('DBA',$lpName)->first();
        if($lp){
            return $lp->id;
        }
        return null;
    }

    public function checkCarveOuts($report, $province_id, $province_name,  $lpID ,$lpName, $sku,$product)
    {
        if($lpName == null || $lpID == null){
            return null;
        }

       $lp_name = Product::find($product->id)->lp->name ?? null;

        if($lp_name == null || empty($lp_name)){
            return null;
        }
        $date = $report->date;


        $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
            ->where(function ($query) use ($lpName, $lp_name) {
                $query->where('lp', $lp_name->name)
                    ->orWhere('lp', $lpName);
            })
            ->where(function ($query) use ($province_id, $province_name) {
                $query->where('location', $province_id);
            })
            ->where(function ($query) use ($date) {
                $query->where('date', $date);

            })
            ->first();
            if ($checkCarveout) {
                if((!empty($checkCarveout->sku) || $checkCarveout->sku != null) && (!empty($checkCarveout->address_id) || $checkCarveout->address_id != null)){
                    $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                                        ->where(function ($query) use ($lpName, $lp_name) {
                                            $query->where('lp', $lp_name->name)
                                                ->orWhere('lp', $lpName);
                                        })
                                        ->where(function ($query) use ($province_id, $province_name) {
                                            $query->where('location', $province_id);
                                        })
                                        ->where(function ($query) use ($date) {
                                            $query->where('date', $date);

                                        })
                                        ->where('sku',$sku)
                                        ->where('address',$report->address_id)
                                        ->first();
                }
                else if($checkCarveout->sku != null || !empty($checkCarveout->sku)){
                    $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                                                ->where(function ($query) use ($lpName, $lp_name) {
                                                    $query->where('lp', $lp_name->name)
                                                        ->orWhere('lp', $lpName);
                                                })
                                                ->where(function ($query) use ($province_id, $province_name) {
                                                    $query->where('location', $province_id);
                                                })
                                                ->where(function ($query) use ($date) {
                                                    $query->where('date', $date);

                                                })
                                                ->where('sku',$sku)
                                                ->first();
                }

                else if($checkCarveout->address != null || !empty($checkCarveout->address)){
                    $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                                                    ->where(function ($query) use ($lpName, $lp_name) {
                                                        $query->where('lp', $lp_name->name)
                                                            ->orWhere('lp', $lpName);
                                                    })
                                                    ->where(function ($query) use ($province_id, $province_name) {
                                                        $query->where('location', $province_id);
                                                    })
                                                    ->where(function ($query) use ($date) {
                                                        $query->where('date', $date);

                                                    })
                                                    ->where('address',$report->address_id)
                                                    ->first();
                }
            }

            if(!$checkCarveout || empty($checkCarveout)) {
                $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                                                    ->where(function ($query) use ($lpName, $lp_name) {
                                                        $query->where('lp', $lp_name->name)
                                                            ->orWhere('lp', $lpName);
                                                    })
                                                    ->where(function ($query) use ($province_id, $province_name) {
                                                        $query->where('location', $province_name);
                                                    })
                                                    ->where(function ($query) use ($date) {
                                                        $query->where('date', $date);

                                                    })
                                                    ->first();
                if ($checkCarveout) {
                    if((!empty($checkCarveout->sku) || $checkCarveout->sku != null) && (!empty($checkCarveout->address_id) || $checkCarveout->address_id != null)){
                        $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                                                        ->where(function ($query) use ($lpName, $lp_name) {
                                                            $query->where('lp', $lp_name->name)
                                                                ->orWhere('lp', $lpName);
                                                        })
                                                        ->where(function ($query) use ($province_id, $province_name) {
                                                            $query->where('location', $province_name);
                                                        })
                                                        ->where(function ($query) use ($date) {
                                                            $query->where('date', $date);

                                                        })
                                                        ->where('sku',$sku)
                                                        ->where('address',$report->address_id)
                                                        ->first();
                    }
                    else if($checkCarveout->sku != null || !empty($checkCarveout->sku)){
                        $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                                                        ->where(function ($query) use ($lpName, $lp_name) {
                                                            $query->where('lp', $lp_name->name)
                                                                ->orWhere('lp', $lpName);
                                                        })
                                                        ->where(function ($query) use ($province_id, $province_name) {
                                                            $query->where('location', $province_name);
                                                        })
                                                        ->where(function ($query) use ($date) {
                                                            $query->where('date', $date);

                                                        })
                                                        ->where('sku',$sku)
                                                        ->first();
                    }

                    else if($checkCarveout->address != null || !empty($checkCarveout->address)){
                        $checkCarveout = Carveout::where('retailer_id', $report->retailer_id)
                                                        ->where(function ($query) use ($lpName, $lp_name) {
                                                            $query->where('lp', $lp_name->name)
                                                                ->orWhere('lp', $lpName);
                                                        })
                                                        ->where(function ($query) use ($province_id, $province_name) {
                                                            $query->where('location', $province_name);
                                                        })
                                                        ->where(function ($query) use ($date) {
                                                            $query->where('date', $date);

                                                        })
                                                        ->where('address',$report->address_id)
                                                        ->first();
                    }
                }
            }
            return $checkCarveout;

    }







}
