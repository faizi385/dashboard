<?php

namespace App\Traits;

use App\Models\CleanSheet;
use App\Models\Offer;
use App\Models\Province;
use App\Models\Report;
use App\Models\RetailerStatement;
use App\Models\User;
use Carbon\Carbon;
use App\Traits\RetailerStatementTrait;

trait GenerateRSAdd {
    public function generateRS($offerID) {
        $offer = Offer::with('lp.user')->where('id', $offerID)->first();

         $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');

        $SelectedOfferDatePass = Carbon::parse($offer->offer_date)->startOfMonth()->format('Y-m-01');

        if($date != $SelectedOfferDatePass){
            return;
        }
        if ($offer->lp == null) {
            dd($offer);
        }


        if ($offer->lp->user == null) {
            dd($offer);
        }

        $province_name = '';
        $province_slug = '';
        // $province = Province::where('id', $offer->province_id)->first();
        // if($offer){
        //     $offer->province = $province->name;
        // }
        (new class
        {
            use RetailerStatementTrait;
        })->getProvinces($offer, $province_slug, $province_name);

        $cleanSheets = CleanSheet::where('sku', $offer->provincial_sku)
            ->where('province', $province_name)
            ->where('province_slug', $province_slug)
            ->whereIn('flag', ['0', '1'])
            ->whereHas('report', function ($query) use ($date, $offer) {
                $query->whereYear('date', '=', date('Y', strtotime($date)))
                    ->whereMonth('date', '=', date('m', strtotime($date)))
                    ->when(!empty($offer->retailer_id), function ($query) use ($offer) {
                        $query->where('retailer_id', $offer->retailer_id);
                    });
            })
            ->get();
        if ($cleanSheets->isEmpty()) {
            $cleanSheets = CleanSheet::where('barcode', $offer->gtin)
                ->where('province', $province_name)
                ->where('province_slug', $province_slug)
                ->whereIn('flag', ['0', '1'])
                ->whereHas('report', function ($query) use ($date, $offer) {
                    $query->whereYear('date', '=', date('Y', strtotime($date)))
                        ->whereMonth('date', '=', date('m', strtotime($date)))
                        ->when(!empty($offer->retailer_id), function ($query) use ($offer) {
                            $query->where('retailer_id', $offer->retailer_id);
                        });
                })
                ->get();

        }

        if ($cleanSheets->isEmpty()) {
            $cleanSheets = CleanSheet::where('product_name', $offer->product_name)
                ->where('province', $province_name)
                ->where('province_slug', $province_slug)
                ->whereIn('flag', ['0', '1'])
                ->whereHas('report', function ($query) use ($date, $offer) {
                    $query->whereYear('date', '=', date('Y', strtotime($date)))
                        ->whereMonth('date', '=', date('m', strtotime($date)))
                        ->when(!empty($offer->retailer_id), function ($query) use ($offer) {
                            $query->where('retailer_id', $offer->retailer_id);
                        });
                })
                ->get();
        }

        if (!empty($cleanSheets) && count($cleanSheets) > 0) {
            foreach ($cleanSheets as $cleanSheet) {
                $retailerRport = Report::where('id', $cleanSheet->report_id)->first();
                $SKU = $offer->provincial_sku ? $offer->provincial_sku : $cleanSheet->sku;

                $checkCarveout = (new class {
                    use ICIntegrationTrait;
                })->checkCarveOuts($retailerRport, $offer->province_id, $province_slug, $province_name, $offer->lp_id, $offer->lp_name, $SKU);

                if ((int)$cleanSheet->purchase > 0) {
                    $TotalQuantityGet = $cleanSheet->purchase;
                    $TotalUnitCostGet = $cleanSheet->average_cost;
                    $FinalDQIFEE = $offer->data_fee;

                    $calculatedDQI = (new class {
                        use ICIntegrationTrait;
                    })->calculateDQI($TotalQuantityGet, $TotalUnitCostGet, $FinalDQIFEE);
                    $FinalDQIFEEMake = $calculatedDQI['dqi_per'];
                    $FinalFeeInDollar = $calculatedDQI['dqi_fee'];

                    if ((int)$cleanSheet->purchase > 0 && $checkCarveout) {
                        $cleanSheet->update([
                            'c_flag' => 'yes',
                            'carveout_id' => $checkCarveout->id,
                        ]);
                    } elseif ((int)$cleanSheet->purchase > 0) {
                        $cleanSheet->update([
                            'c_flag' => 'no',
                            'carveout_id' => null,
                        ]);
                    } elseif ((int)$cleanSheet->purchase == 0 && $checkCarveout) {
                        $cleanSheet->update([
                            'c_flag' => 'yes carveout',
                            'carveout_id' => $checkCarveout->id,
                        ]);
                    } else {
                        $cleanSheet->update([
                            'c_flag' => 'no carveout',
                            'carveout_id' => null,
                        ]);
                    }

                } else {
                    $FinalDQIFEEMake = '';
                    $FinalFeeInDollar = '';
                }

                $cleanSheet->update([
                    'lp_id' => $offer->lp_id,
                    'lp_name' => $offer->lp->name ?? null,
//                    'offer_lp' => $offer->lp_name ?? null,
                    'flag' => '3',
                    'dqi_flag' => '1',
                    'dqi_per' => $FinalDQIFEEMake,
                    'dqi_fee' => $FinalFeeInDollar,
                    'offer_id' => $offerID,
                    'average_cost' => trim($offer->unit_cost, '$')

                ]);
                if((int)$cleanSheet->purchase>0) {
                    $retailerStatment = new RetailerStatement;

                    $retailerStatment->lp_id = $offer->lp ? $offer->lp->id : $offer->lp_id;
                    $retailerStatment->product_name = $offer->product_name ? $offer->product_name : $cleanSheet->product_name;
                    $retailerStatment->sku = $offer->provincial_sku ? $offer->provincial_sku : $cleanSheet->sku;
                    $retailerStatment->barcode = $offer->gtin ? $offer->gtin : $cleanSheet->barcode;
                    $retailerStatment->quantity = (int)$cleanSheet->purchase ? $cleanSheet->purchase : 0;
                    $retailerStatment->unit_cost = $cleanSheet->report_price_og ? trim($cleanSheet->report_price_og, '$') : trim($offer->unit_cost, '$') ?? '0.00';
                    if (empty($cleanSheet->report_price_og) || $cleanSheet->report_price_og == '' || $cleanSheet->report_price_og == '0.00' || $cleanSheet->report_price_og == '0') {
                        $retailerStatment->unit_cost = trim($offer->unit_cost, '$') ?? '0.00';
                    }
                    $retailerStatment->cs_unit_cost = $cleanSheet->average_cost ? trim($cleanSheet->average_cost, '$') : trim($offer->unit_cost, '$') ?? '0.00';
                    $retailerStatment->total_purchase_cost = (float)$retailerStatment->quantity * (float)$retailerStatment->unit_cost;
                    $retailerStatment->fee_per = (float)trim($offer->data_fee, '%') * 100;
                    $retailerStatment->fee_in_dollar = (float)$retailerStatment->total_purchase_cost * $retailerStatment->fee_per / 100;
                    $retailerStatment->ircc_per = '20';
                    $retailerStatment->ircc_dollar = $retailerStatment->fee_in_dollar * (int)$retailerStatment->ircc_per / 100;
                    $retailerStatment->total_fee = $retailerStatment->fee_in_dollar - $retailerStatment->ircc_dollar;
                    $retailerStatment->quantity_sold = $cleanSheet->sold;
                    $retailerStatment->average_price = $cleanSheet->average_price;
                    $retailerStatment->opening_inventory_unit = !empty($cleanSheet->opening_inventory_unit) ? $cleanSheet->opening_inventory_unit : 0;
                    $retailerStatment->closing_inventory_unit = !empty($cleanSheet->closing_inventory_unit) ? $cleanSheet->closing_inventory_unit : 0;
                    $retailerStatment->category = $offer->category;
                    $retailerStatment->brand = $cleanSheet->brand;
                    // $retailerStatment->lp_id = $offer->lp_id;
                    $retailerStatment->clean_sheet_id = $cleanSheet->id;
                    $retailerStatment->report_id = $cleanSheet->report_id;
                    $retailerStatment->reconciliation_date = $cleanSheet->reconciliation_date;
                    $retailerStatment->created_at = now()->format('Y-m-d H:i:s');
                    $retailerStatment->updated_at = now()->format('Y-m-d H:i:s');
                    $retailerStatment->flag = $checkCarveout ? '1' : '0';
                    $retailerStatment->carve_out = $checkCarveout ? 'yes' : 'no';
                    $retailerStatment->carveout_id = $checkCarveout ? $checkCarveout->id : null;
                    $retailerStatment->save();
                }
            }
        }
    }
}
