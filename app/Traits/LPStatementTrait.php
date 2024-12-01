<?php

namespace App\Traits;

use App\Exports\LpStatementExport;
use App\Models\CarveOut;
use App\Models\Lp;
use App\Models\LpStatement;
use App\Models\Province;
use App\Models\RetailerStatement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

trait LPStatementTrait
{
    public function generateLpStatement($lp_id,$date)
    {
        try {
            $taxValue = DB::table('lp_addresses')
                ->join(DB::raw('(SELECT lp_id, MAX(id) AS max_id FROM lp_addresses GROUP BY lp_id) latest_lp'), function ($join) {
                    $join->on('lp_addresses.id', '=', 'latest_lp.max_id');
                })
                ->leftJoin('provinces', function ($join) {
                    $join->on('lp_addresses.province_id', '=', 'provinces.id');
                })
                ->select('provinces.tax_value')
                ->where('lp_addresses.lp_id', $lp_id)
                ->first();

            $taxValue = $taxValue->tax_value ?? 5;

            $retailerStatments = RetailerStatement::where('lp_id', $lp_id)->whereHas('report', function ($query) use ($date) {
                $query->whereMonth('date', Carbon::parse($date)->format('m'));
                $query->whereYear('date', Carbon::parse($date)->format('Y'));
                return $query;
            })->get();

            $lpStatements = new Collection();

            foreach ($retailerStatments as $retailerStatment) {
                $province = Province::where('id',$retailerStatment->province_id)->first();
                $province_name = $province->name;
                $province_slug = $province->slug;
                $province_id = $province->id;
                if ((int)$retailerStatment->quantity > 0 &&  (int)$retailerStatment->flag == 0) {
                    $total_fee_dollars = (((float)$retailerStatment->fee_per * (float)$retailerStatment->quantity * (float)$retailerStatment->unit_cost) / 100);
                    $lpStatement = new LpStatement([
                        'province' => $province_name,
                        'retailer' => $retailerStatment->report->location ?? $retailerStatment->report->retailer->dba,
                        'product' => $retailerStatment->product_name,
                        'category' => $retailerStatment->category,
                        'brand' => $retailerStatment->brand,
                        'sku' => $retailerStatment->sku,
                        'total_sales_quantity' => '',
                        'quantity_purchased' => $retailerStatment->quantity,
                        'unit_cost' => $retailerStatment->unit_cost,
                        'total_purchased_cost' => (float)$retailerStatment->quantity * (float)$retailerStatment->unit_cost,
                        'total_fee_percentage' => (float)$retailerStatment->fee_per,
                        'total_fee_dollars' => $total_fee_dollars,
                        'sold' => (int)$retailerStatment->quantity_sold,
                        'average_price' => $retailerStatment->average_price,
                        'opening_inventory_unit' => (int)$retailerStatment->opening_inventory_unit,
                        'closing_inventory_unit' => (int)$retailerStatment->closing_inventory_unit,
                        'retailer_dba' => $retailerStatment->report->retailer->dba,
                        'calculated_value' => ($total_fee_dollars + (($total_fee_dollars * $taxValue) / 100))
                    ]);

                    $lpStatements->push($lpStatement);
                }
            }

            $sortedCollection = $lpStatements->sortBy('product')
                ->sortBy('retailer')
                ->sortBy('retailer_dba')
                ->sortBy('provice');

            return $sortedCollection;
        } catch (\Exception $e) {
            Log::debug('Error Occurred: '.json_encode($e->getMessage()));
        }
    }
}

