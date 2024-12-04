<?php

namespace App\Http\Controllers;

use App\Models\LP;
use Carbon\Carbon;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function index()
    {
        $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        $distributors = Retailer::get(['first_name', 'last_name', 'id']);
        $distributors = $distributors->mapWithKeys(function ($distributor) {
            $fullName = $distributor->first_name . ' ' . $distributor->last_name;
            return [$distributor->id => $fullName];
        });
    
        $provinces = Province::all();
    
        $totalPurchaseCostByProvince = [
            'Alberta' => 0,
            'Ontario' => 0,
            'British Columbia' => 0,
            'Manitoba' => 0,
            'Saskatchewan' => 0,
        ];
    
        // Fetch sum of purchases for top 5 LPs
        $topLps = CleanSheet::select('lp_name', DB::raw('SUM(purchase) as total_purchase'))
            ->groupBy('lp_name')
            ->orderByDesc('total_purchase')
            ->limit(5)
            ->get();
    
        return view('performance.index', compact('distributors', 'totalPurchaseCostByProvince', 'provinces', 'topLps'));
    }
    
}
