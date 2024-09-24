<?php

// app/Http/Controllers/OfferController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfferController extends Controller
{
    // Handle bulk upload of offers
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'offerExcel' => 'required|mimes:xlsx,xls,csv',
        ]);

        // Logic to parse and process the Excel file goes here

        return redirect()->back()->with('success', 'Bulk offers uploaded successfully.');
    }
    public function create()
    {
        return view('offers.create');
    }

    // Handle single offer creation
    public function store(Request $request)
    {
        $request->validate([
            'offerName' => 'required|string|max:255',
            'offerDetails' => 'required|string',
            'offerExpiry' => 'required|date',
        ]);

        // Logic to save single offer goes here

        return redirect()->back()->with('success', 'Single offer added successfully.');
    }
}
