<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\CleanSheet;
use App\Models\Offer;
use Illuminate\Http\Request;
use App\Models\RetailerStatement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        // Initialize variables for the dashboard totals
        $totalPayoutAllRetailers = 0;
        $totalPayoutWithTaxAllRetailers = 0;
        $totalIrccDollarAllRetailers = 0;
        $totalMappedOffers = 0;
        $totalUnmappedOffers = 0;
    
        // Prepare an array to store total purchase cost by province
        $totalPurchaseCostByProvince = [
            'Alberta' => 0,
            'Ontario' => 0,
            'British Columbia' => 0,
            'Manitoba' => 0,
            'Saskatchewan' => 0,
        ];
    
        // Fetch top 5 products by purchases
        $topProducts = RetailerStatement::select('product_name', 'sku', DB::raw('SUM(quantity) as total_purchases'))
        ->groupBy('product_name', 'sku')
        ->orderByDesc('total_purchases')
        ->take(5)
        ->get();
    
        
        // Check if the user is a retailer
        if ($user->hasRole('Retailer')) {
            $statements = RetailerStatement::where('retailer_id', $user->id)
                ->where('flag', 0)
                ->where('reconciliation_date', now()->startOfMonth())
                ->get();
    
            $totalPayout = $statements->sum('total_payout');
            $totalIrccDollar = $statements->sum('ircc_dollar');
    
            $totalPayoutAllRetailers = $totalPayout;
            $totalIrccDollarAllRetailers = $totalIrccDollar;
    
            $totalPayoutWithTax = $statements->sum(function ($statement) {
                return $statement->total_payout * 1.13;
            });
            $totalPayoutWithTaxAllRetailers = $totalPayoutWithTax;
    
            foreach ($statements as $statement) {
                $province = $statement->province;
                if (isset($totalPurchaseCostByProvince[$province])) {
                    $totalPurchaseCostByProvince[$province] += $statement->total_purchase_cost;
                }
            }
        } else {
            // Super admin: Fetch all reports and calculate totals
            $reports = Report::with('retailer')->get();
        
            // Initialize total sums
            $totalPayoutAllRetailers = 0;
            $totalPayoutWithTaxAllRetailers = 0;
            $totalIrccDollarAllRetailers = 0;
            $totalPurchaseCostByProvince = [];
        
            foreach ($reports as $report) {
                $retailerId = $report->retailer_id;
                $statements = RetailerStatement::where('retailer_id', $retailerId)
                    ->where('flag', 0)
                    ->where('reconciliation_date', $date)
                    ->get();
        
                $totalPayout = 0;
                $totalPayoutWithTax = 0;
                $totalIrccDollar = 0;
        
                foreach ($statements as $statement) {
                    $payout = $statement->total_fee; // Total fee for the statement
                    $totalPayout += $payout;
        
                    // Fetch province and calculate tax amount
                    $province = $statement->report->province ?? null;
                    $taxRate = $this->getProvinceTaxRate($province);
                    $taxAmount = $payout * $taxRate; // Calculate tax
                    $payoutWithTax = $payout + $taxAmount;
                    $totalPayoutWithTax += $payoutWithTax;
        
                    $totalIrccDollar += $statement->ircc_dollar;
        
                    // Add to purchase cost by province
                    $province = $statement->province;
                    if (isset($totalPurchaseCostByProvince[$province])) {
                        $totalPurchaseCostByProvince[$province] += $statement->total_purchase_cost;
                    } else {
                        $totalPurchaseCostByProvince[$province] = $statement->total_purchase_cost;
                    }
                }
        
                // Sum up totals for all retailers
                $totalPayoutAllRetailers += $totalPayout;
                $totalPayoutWithTaxAllRetailers += $totalPayoutWithTax;
                $totalIrccDollarAllRetailers += $totalIrccDollar;
            }
        }

    $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
    $dateOffer = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');

    $totalMappedOffers = RetailerStatement::select('offer_id')->whereNotNull('offer_id')->where('reconciliation_date',$date)->distinct()->count('offer_id');
// dump(    $totalMappedOffers );
    $totalOffers = Offer::where('offer_date',$dateOffer)->count();
    // dump(  $totalOffers);
    $totalMappedOffersIds = RetailerStatement::select('offer_id')->whereNotNull('offer_id')->where('reconciliation_date',$date)->distinct()->pluck('offer_id')->toArray();
    $totalOffersIds = Offer::select('id')->where('offer_date',$dateOffer)->pluck('id')->toArray();

$totalUnmappedOffersIds = array_diff($totalOffersIds,$totalMappedOffersIds);
// dd($totalUnmappedOffersIds );
$totalUnmappedOffers = count($totalUnmappedOffersIds);
$totalDeals = Offer::count();
        return view('dashboard', compact(
            'totalPayoutAllRetailers',
            'totalPayoutWithTaxAllRetailers',
            'totalIrccDollarAllRetailers',
            'totalPurchaseCostByProvince',
            'totalMappedOffers',
            'totalUnmappedOffers',
            'topProducts' ,
            'totalDeals'// Pass top products to the view
        ));
    }


     private function getProvinceTaxRate($province)
    {
        $taxRates = [
            'Alberta' => 0.05,
            'Ontario' => 0.03,
            'Manitoba' => 0.05,
            'British Columbia' => 0.05,
            'Saskatchewan' => 0.05,
        ];

        return $taxRates[$province] ?? 0;
    }

}
