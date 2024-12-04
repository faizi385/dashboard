<?php

namespace App\Http\Controllers;

use App\Models\LP;
use Carbon\Carbon;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use Illuminate\Http\Request;
use App\Models\RetailerStatement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
   
        $lp = LP::where('user_id', Auth::user()->id)->first();
        $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        $distributors = Retailer::where('lp_id', $lp->id)->get(['first_name', 'last_name', 'id']);
        

        $distributors = $distributors->mapWithKeys(function ($distributor) {
            $fullName = $distributor->first_name . ' ' . $distributor->last_name;
            return [$distributor->id => $fullName];
        });

        $provinces = Province::all();
        $monthName = Carbon::parse($date)->format('F Y'); 
    
     
        $availedRetailers = RetailerStatement::where('lp_id', $lp->id)
            ->whereNotNull('offer_id')
            ->distinct('retailer_id')
            ->count('retailer_id');
    
     
        $nonAvailedRetailers = Retailer::where('lp_id', $lp->id)
            ->whereNotIn('id', RetailerStatement::where('lp_id', $lp->id)->whereNotNull('offer_id')->pluck('retailer_id'))
            ->count();
    
        $dealPurchases = CleanSheet::where('lp_id', $lp->id)
            ->whereNotNull('offer_id')  
            ->sum('purchase');  
    
        $nonDealPurchases = CleanSheet::where('lp_id', $lp->id)
            ->whereNull('offer_id')  
            ->sum('purchase');
    
        $provinceOffers = DB::table('offers')
            ->select('province_id', DB::raw('count(*) as total_offers'))
            ->where('lp_id', $lp->id)
            ->groupBy('province_id')
            ->pluck('total_offers', 'province_id');
        
   
        $offerProvinces = DB::table('provinces')
            ->whereIn('id', array_keys($provinceOffers->toArray()))
            ->pluck('name', 'id')
            ->toArray();
        
  
        $offerProvinceLabels = array_values($offerProvinces); 
        $offerData = array_values($provinceOffers->toArray()); 
    
     
        return view('analytics.index', compact(
            'availedRetailers', 
            'nonAvailedRetailers', 
            'dealPurchases', 
            'nonDealPurchases', 
            'monthName', 
            'offerProvinceLabels', 
            'offerData', 
            'distributors', 
            'provinces'
        ));
    }

    
    public function getAvailedVsNonAvailed(Request $request)
    {
        $province = $request->input('province');  
        $date = $request->input('date', Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01')); 
        $lp = LP::where('user_id', Auth::user()->id)->first();
        if (!$province) {
            Log::error('Province is missing in request.');
            return response()->json(['error' => 'Province is required'], 400);
        }
    
        Log::info('Fetching data for province', ['province' => $province]);
    
        try {
 
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();
    
           
            $allRetailerIds = Retailer::where('lp_id', $lp->id)
            ->pluck('id')
            ->toArray();
    
      
            $availedRetailerIds = RetailerStatement::whereHas('retailer.address', function ($query) use ($province) {
                $query->where('province', $province);
            })
            
            ->whereNotNull('offer_id')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]) 
            ->distinct('retailer_id')
            ->pluck('retailer_id')
            ->toArray();
    
 
            $availedCount = count($availedRetailerIds);
            $notAvailedCount = count(array_diff($allRetailerIds, $availedRetailerIds));
    
            Log::info('Availed vs Non-Availed Data', [
                'availed' => $availedCount,
                'not_availed' => $notAvailedCount,
            ]);
    
            return response()->json([
                'availed' => $availedCount,
                'notAvailed' => $notAvailedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching availed vs non-availed data', [
                'error' => $e->getMessage(),
                'province' => $province,
            ]);
            return response()->json(['error' => 'Error fetching data'], 500);
        }
    }
    
    public function getChartData(Request $request)
{
    $province = $request->input('province', 'all'); 
    $date = $request->input('date', Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01'));
    $lp = LP::where('user_id', Auth::user()->id)->first();

    try {
      
        Log::info('Received filters', [
            'province' => $province,
            'date' => $date,
            'lp_id' => $lp->id ?? 'No LP found',
        ]);

   
        if ($date) {
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();
        } else {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }


        Log::info('Date range', [
            'startOfMonth' => $startOfMonth->toDateString(),
            'endOfMonth' => $endOfMonth->toDateString(),
        ]);

      
        $query = DB::table('offers')
            ->join('provinces', 'offers.province_id', '=', 'provinces.id')
            ->where('offers.lp_id', $lp->id)
            ->whereBetween('offers.created_at', [$startOfMonth, $endOfMonth])
            ->select('provinces.name as province_name', DB::raw('COUNT(offers.id) as total_offers'))
            ->groupBy('provinces.name');

    
        if ($province !== 'all') {
            $query->where('provinces.id', $province); 
            Log::info('Applying province filter', ['province_id' => $province]);
        }

       
        Log::info('Generated query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        $data = $query->get();

     
        Log::info('Fetched data', ['data' => $data]);

 
        $labels = $data->pluck('province_name')->toArray();
        $totals = $data->pluck('total_offers')->toArray();

      
        Log::info('Response data', [
            'labels' => $labels,
            'totals' => $totals,
        ]);

        return response()->json([
            'labels' => $labels,
            'data' => $totals,
        ]);
    } catch (\Exception $e) {

        Log::error('Error fetching chart data', [
            'error' => $e->getMessage(),
            'province' => $province,
            'date' => $date,
        ]);

        return response()->json(['error' => 'Error fetching data'], 500);
    }
}


    
    
    
public function getProvincesByDistributor(Request $request)
{
    $distributorId = $request->input('distributor_id');
    
    try {
  
        $provinces = DB::table('retailer_addresses')
            ->join('provinces', 'retailer_addresses.province', '=', 'provinces.id') 
            ->where('retailer_addresses.retailer_id', $distributorId)
            ->distinct()
            ->select('provinces.id', 'provinces.name') 
            ->get();

   
        return response()->json($provinces);
    } catch (\Exception $e) {
     
        Log::error('Error fetching provinces by distributor', [
            'error' => $e->getMessage(),
            'distributor_id' => $distributorId,
        ]);
        return response()->json(['error' => 'Error fetching provinces'], 500);
    }
}

}
