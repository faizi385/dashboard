<?php

namespace App\Http\Controllers;

use App\Models\Lp;
use App\Models\Offer;
use App\Models\Retailer;
use Illuminate\Http\Request;
use App\Exports\OffersExport;
use App\Imports\OffersImport;
use Maatwebsite\Excel\Facades\Excel;

class OfferController extends Controller
{
    public function create()
    {
        $lps = Lp::all(); // Fetch LPs
        $retailers = Retailer::all(); // Fetch Retailers

        return view('offers.create', compact('lps', 'retailers'));
    }



    public function export()
    {
        return Excel::download(new OffersExport, 'offers.xlsx');
    }



    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'offerExcel' => 'required|file|mimes:xlsx,xls,csv',
        ]);
    
        // Import the offers using the OffersImport class
        Excel::import(new OffersImport, $request->file('offerExcel'));
    
        // Redirect back with a success message
        return redirect()->back()->with('toast_success', 'Offers imported successfully!');
    }
    





    // Handle single offer creation with validation
 
}
