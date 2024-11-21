<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\CleanSheet;
use Illuminate\Http\Request;
use App\Models\RetailerStatement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

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

        // Check if the user is a retailer
        if ($user->hasRole('Retailer')) {
            $statements = RetailerStatement::where('retailer_id', $user->id)->where('flag',0)->where('reconciliation_date',now()->startOfMonth())->get();
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

            foreach ($reports as $report) {
                $retailerId = $report->retailer_id;
                $statements = RetailerStatement::where('retailer_id', $retailerId)->where('flag',0)->where('reconciliation_date',now()->startOfMonth())->get();

                $totalPayout = 0;
                $totalPayoutWithTax = 0;
                $totalIrccDollar = 0;

                foreach ($statements as $statement) {
                    $payout = $statement->quantity_sold * $statement->average_price;
                    $totalPayout += $payout;

                    $taxAmount = $payout * 0.13;
                    $payoutWithTax = $payout + $taxAmount;
                    $totalPayoutWithTax += $payoutWithTax;

                    $totalIrccDollar += $statement->ircc_dollar;

                    $province = $statement->province;
                    if (isset($totalPurchaseCostByProvince[$province])) {
                        $totalPurchaseCostByProvince[$province] += $statement->total_purchase_cost;
                    }
                }

                $totalPayoutAllRetailers += $totalPayout;
                $totalPayoutWithTaxAllRetailers += $totalPayoutWithTax;
                $totalIrccDollarAllRetailers += $totalIrccDollar;
            }
        }

        // Calculate mapped and unmapped offers
        $totalMappedOffers = CleanSheet::whereNotNull('offer_id')->count();
        $totalUnmappedOffers = CleanSheet::whereNull('offer_id')->count();

        return view('dashboard', compact(
            'totalPayoutAllRetailers',
            'totalPayoutWithTaxAllRetailers',
            'totalIrccDollarAllRetailers',
            'totalPurchaseCostByProvince',
            'totalMappedOffers',
            'totalUnmappedOffers'
        ));
    }

}
