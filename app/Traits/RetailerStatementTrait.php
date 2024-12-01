<?php
namespace App\Traits;

use App\Models\LP;
use App\Models\Province;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\CarveOut;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\ProductVariation;
use App\Models\RetailerStatement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LpVariableFeeStructure;
use App\Jobs\ProcessEngagementPercentage;
use App\Models\Offer;

trait RetailerStatementTrait
{
    public function GenerateRetailerStatement($report, $retailer_id, $posCleanSheet)
    {
        Log::info('Starting GenerateRetailerStatement function', ['report_id' => $report->id, 'retailer_id' => $retailer_id]);
        $province_name = '';
        $province_slug = '';
        $province_id = $report->province_id;
        $this->getProvinces($report, $province_slug, $province_name);
        $cleanSheets = CleanSheet::where('report_id', $report->id)
            ->where('dqi_flag', 1)
            ->where('purchase', '>', 0)
            ->get();
        Log::info('Fetched clean sheets', ['cleanSheetCount' => $cleanSheets->count()]);
        $retailer = Retailer::find($retailer_id);
        if (!$retailer) {
            Log::error('Retailer not found', ['retailer_id' => $retailer_id]);
            return;
        }
        foreach ($cleanSheets as $cleanSheet) {
            if (!empty($cleanSheet->offer_id)) {
                $lpVariable = Offer::with('lp.user')->find($cleanSheet->offer_id);
                if ($lpVariable && $cleanSheet->purchase > 0) {
                    Log::info('Processing clean sheet', ['cleanSheet_id' => $cleanSheet->id, 'lpVariable_id' => $lpVariable->id]);
                    $retailerStatement = $this->lpVariableFeeAssign($report, $lpVariable, $cleanSheet, $retailer, $province_slug, $province_name, $retailer_id,$province_id);
                    $data = $retailerStatement->attributesToArray();
                    $data['retailer_id'] = $retailer_id;
                    try {
                        DB::table('retailer_statements')->insertGetId($data);
                        Log::info('Inserted retailer statement successfully', ['data' => $data]);
                    } catch (\Exception $e) {
                        Log::error('Error inserting retailer statement', ['error' => $e->getMessage(), 'data' => $data]);
                    }
                }
            }
        }
    }

    public function lpVariableFeeAssign($report, $lpVariable, $cleanSheet, $retailer, $province_slug, $province_name, $retailer_id,$province_id)
    {
        Log::info('Starting lpVariableFeeAssign function', ['lpVariable_id' => $lpVariable->id, 'cleanSheet_id' => $cleanSheet->id]);
        if (!$lpVariable->lps) {
            Log::error('lpVariable lps is null', ['lpVariable' => $lpVariable]);
        }
        $lpRecord = LP::find($lpVariable->lp_id);
        $lp_name = $lpRecord ? $lpRecord->name : null;
        $retailerStatement = new RetailerStatement;
        $retailerStatement->product_name = $lpVariable->product_name ?? $cleanSheet->product_name;
        $retailerStatement->sku = $lpVariable->provincial_sku ?? $cleanSheet->sku;
        $retailerStatement->barcode = $lpVariable->gtin ?? $cleanSheet->barcode;
        $retailerStatement->quantity = (int) $cleanSheet->purchase;
        $retailerStatement->offer_id = (int) $cleanSheet->offer_id;
        $retailerStatement->carveout_id = $cleanSheet->carveout_id;
        $retailerStatement->unit_cost = trim($lpVariable->unit_cost ?? $cleanSheet->report_price_og, '$') ?: '0.00';
        if (empty($cleanSheet->report_price_og) || $cleanSheet->report_price_og == '0.00') {
            $retailerStatement->unit_cost = trim($lpVariable->unit_cost, '$') ?: '0.00';
        }
        $retailerStatement->cs_unit_cost = trim($cleanSheet->average_cost ?? $lpVariable->unit_cost, '$') ?: '0.00';
        $retailerStatement->total_purchase_cost = (float)$retailerStatement->quantity * (float)$retailerStatement->unit_cost;
        $retailerStatement->fee_per = (float)trim($lpVariable->data_fee, '%') * 100;
        $retailerStatement->fee_in_dollar = (float)$retailerStatement->total_purchase_cost * $retailerStatement->fee_per / 100;
        $retailerStatement->ircc_per = 20;
        $retailerStatement->ircc_dollar = $retailerStatement->fee_in_dollar * ($retailerStatement->ircc_per / 100);
        $retailerStatement->total_fee = $retailerStatement->fee_in_dollar - $retailerStatement->ircc_dollar;
        $retailerStatement->quantity_sold = $cleanSheet->sold;
        $retailerStatement->average_price = $cleanSheet->average_price;
        $retailerStatement->opening_inventory_unit = $cleanSheet->opening_inventory_unit ?? 0;
        $retailerStatement->closing_inventory_unit = $cleanSheet->closing_inventory_unit ?? 0;
        $retailerStatement->category = $lpVariable->category;
        $retailerStatement->brand = $cleanSheet->brand;
        $retailerStatement->lp_id = $lpVariable->lp_id;
        $retailerStatement->clean_sheet_id = $cleanSheet->id;
        $retailerStatement->report_id = $report->id;
        $retailerStatement->reconciliation_date = $report->date;
        $retailerStatement->created_at = now()->format('Y-m-d H:i:s');
        $retailerStatement->updated_at = now()->format('Y-m-d H:i:s');
        $retailerStatement->flag = $cleanSheet->c_flag == 'yes' ? '1' : '0';
        $retailerStatement->carve_out = $cleanSheet->c_flag == 'yes' ? 'yes' : 'no';
        $retailerStatement->retailer_id = $retailer_id;
        $retailerStatement->province_id = $report->province_id;
        $retailerStatement->province_slug = $province_slug;
        $retailerStatement->province = $province_name;
        $retailerStatement->product_variation_id = $cleanSheet->product_variation_id;
        $retailerStatement->address_id = $cleanSheet->address_id;
        Log::info('Finished lpVariableFeeAssign', ['retailerStatement' => $retailerStatement]);
        return $retailerStatement;
    }

    public function getProvinces($report, &$province_slug, &$province_name){
        if($report){
            $province = Province::where('id', $report->province_id)->first();
            $province_name = $province->name;
            $province_slug = $province->slug;
        }
        return;
    }
}
