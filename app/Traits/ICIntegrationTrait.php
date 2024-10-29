<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Carveout;
use App\Models\CleanSheet;
use Illuminate\Support\Facades\Log;

trait ICIntegrationTrait
{
    public function saveToCleanSheet(array $cleanSheetData)
    {
        try {
            CleanSheet::create($cleanSheetData);
            Log::info('Data saved to CleanSheet successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving data to CleanSheet:', ['error' => $e->getMessage()]);
        }
    }

    public function matchICBarcode(string $barcode)
    {
        $product = Product::where('gtin', $barcode)->first();

        if ($product) {
            Log::info('Product matched by barcode:', ['barcode' => $barcode, 'product_id' => $product->id]);
            return $product;
        }

        Log::warning('No product found for barcode:', ['barcode' => $barcode]);
        return null;
    }

    public function matchICSku(string $sku)
    {
        $product = Product::where('provincial_sku', $sku)->first();

        if ($product) {
            Log::info('Product matched by SKU:', ['sku' => $sku, 'product_id' => $product->id]);
            return $product;
        }

        Log::warning('No product found for SKU:', ['sku' => $sku]);
        return null;
    }

    public function matchICBarcodeSku(string $sku, string $gtin)
    {
        $product = Product::where('provincial_sku', $sku)
            ->where('gtin', $gtin)
            ->first();

        if ($product) {
            Log::info('Product matched by SKU and GTIN:', [
                'sku' => $sku,
                'gtin' => $gtin,
                'product_id' => $product->id
            ]);
            return $product;
        }

        Log::warning('No product found for SKU and GTIN:', ['sku' => $sku, 'gtin' => $gtin]);
        return null;
    }

    public function matchOfferBarcode(string $gtin)
    {
        $offer = Offer::where('gtin', $gtin)->first();

        if ($offer) {
            Log::info('Offer matched by GTIN:', ['gtin' => $gtin, 'offer_id' => $offer->id]);
            return $offer;
        }

        Log::warning('No offer found for GTIN:', ['gtin' => $gtin]);
        return null;
    }

    public function matchOfferSku(string $sku)
    {
        $offer = Offer::where('provincial_sku', $sku)->first();

        if ($offer) {
            Log::info('Offer matched by SKU:', ['sku' => $sku, 'offer_id' => $offer->id]);
            return $offer;
        }

        Log::warning('No offer found for SKU:', ['sku' => $sku]);
        return null;
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

    public function matchOfferProductName($productName)
    {
        return Offer::where('product_name', 'LIKE', '%' . $productName . '%')->first();
    }

    protected function calculateDqiFee($greenlineReport, $item)
    {
        return ($greenlineReport->sold ?? 0) * ($item->average_cost ?? 0);
    }

    protected function calculateDqiPer($greenlineReport, $item)
    {
        return ($greenlineReport->sold ?? 0) / ($item->average_price ?? 1);
    }

    public function DQISummaryFlag($sku = null, $gtin = null)
    {
        $offer = null;

        if (!empty($gtin) && !empty($sku)) {
            $offer = $this->matchOfferProduct($sku, $gtin); 
        } elseif (!empty($gtin)) {
            $offer = $this->matchOfferBarcode($gtin); 
        } elseif (!empty($sku)) {
            $offer = $this->matchOfferSku($sku); 
        }

        return $offer;
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
