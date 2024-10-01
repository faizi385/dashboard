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
    public function index(Request $request)
{
    // Get the currently authenticated user
    $user = auth()->user();
    $lps = Lp::all(); // Get all LPs for super admin view

    // Check if the user is an LP
    if ($user->hasRole('LP')) {
        // Get the LP ID associated with the logged-in user
        $lp = Lp::where('user_id', $user->id)->first();

        if ($lp) {
            // Fetch offers created by this LP
            $offers = Offer::where('lp_id', $lp->id)->get();
        } else {
            $offers = collect(); // Empty collection if no LP found
        }
    } else {
        // Super admin: Fetch all offers or filter by specific LP if lp_id is provided
        $lpId = $request->get('lp_id');

        if ($lpId) {
            $lp = Lp::findOrFail($lpId); // Fetch the LP details
            $offers = Offer::where('lp_id', $lpId)->get(); // Fetch offers for the LP
        } else {
            $lp = null;
            $offers = Offer::all(); // Fetch all offers for super admin
        }
    }

    return view('offers.index', compact('offers', 'lp', 'lps'));
}


    public function edit($id)
{
    $offer = Offer::findOrFail($id); // Fetch the offer by ID
    return view('offers.edit', compact('offer')); // Return the edit view with the offer data
}

public function update(Request $request, $id)
{
    $request->validate([
        'province' => 'required|string',
        'product_name' => 'required|string',
        'category' => 'required|string',
        'brand' => 'required|string',
        'provincial_sku' => 'required|string',
        'offer_start' => 'required|date',
        'offer_end' => 'required|date',
        'gtin' => 'required|string',
        'data_fee' => 'required|numeric',
        'unit_cost' => 'required|numeric',
    ]);

    $offer = Offer::findOrFail($id);
    $offer->update($request->all());

    return redirect()->route('offers.index')->with('success', 'Offer updated successfully.');
}
public function destroy($id)
{
    $offer = Offer::findOrFail($id);
    $offer->delete();

    return redirect()->route('offers.index')->with('success', 'Offer deleted successfully.');
}

    // Show the offer creation form with LPs and Retailers data
    public function create()
    {
        $lps = Lp::all(); // Fetch LPs
        $retailers = Retailer::all(); // Fetch Retailers

        return view('offers.create', compact('lps', 'retailers'));
    }

    // Export all offers to an Excel file
    public function export()
    {
        return Excel::download(new OffersExport, 'offers.xlsx');
    }

    // Handle the bulk import of offers with LP selection
    public function import(Request $request)
    {
        // Validate the uploaded file and LP selection
        $request->validate([
            'offerExcel' => 'required|file|mimes:xlsx,xls,csv',
            'lp_id' => 'required|exists:lps,id', // Ensure the selected LP exists
        ]);
    
        // Retrieve the LP ID from the request
        $lpId = $request->lp_id;
    
        // Use a custom import class that associates offers with the selected LP
        Excel::import(new OffersImport($lpId), $request->file('offerExcel'));
    
        // Redirect back with a success message
        return redirect()->back()->with('toast_success', 'Offers imported successfully for the selected LP!');
    }
    public function store(Request $request)
    {
        // Base validation rules for common fields
        $rules = [
            'product_name' => 'required|string|max:255',
            'provincial_sku' => 'required|string|max:255',
            'gtin' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'general_data_fee' => 'nullable|numeric|min:0',
            'exclusive_data_fee' => 'nullable|numeric|min:0', // Keep as nullable
            'unit_cost' => 'required|numeric',
            'category' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'case_quantity' => 'required|integer',
            'offer_start' => 'required|date',
            'offer_end' => 'required|date',
            'product_size' => 'required|integer',
            'thc_range' => 'required|string|max:255',
            'cbd_range' => 'required|string|max:255',
            'lp_id' => 'required|exists:lps,id',
            'offer_date' => 'required|date',
            'product_link' => 'nullable|url|max:255',
            'comment' => 'nullable|string|max:255',
        ];
    
        // Add conditional validation for the first checkbox (Add Exclusive Offer)
        if ($request->has('exclusive_offer') && $request->exclusive_offer) {
            $rules['exclusive_data_fee'] = 'required|numeric|min:0'; // Required only when this checkbox is checked
            $rules['retailer_ids'] = 'required|array';
            $rules['retailer_ids.*'] = 'exists:retailers,id';
        }
    
        // Add conditional validation for the second checkbox (Make Exclusive to Specific Retailers)
        if ($request->has('makeExclusiveOfferCheckbox') && $request->makeExclusiveOfferCheckbox) {
            $rules['exclusive_retailer_ids'] = 'required|array';
            $rules['exclusive_retailer_ids.*'] = 'exists:retailers,id';
        }
    
        // Validate the request with conditional rules
        $validatedData = $request->validate($rules);
    
        // Handle retailer-specific exclusive offer (Second Checkbox)
        if ($request->has('makeExclusiveOfferCheckbox') && $request->makeExclusiveOfferCheckbox) {
            // Use general data fee for retailer-specific exclusive offers
            foreach ($request->exclusive_retailer_ids as $retailerId) {
                $exclusiveOfferData = $this->prepareOfferData($request, $retailerId, $request->general_data_fee);
                Offer::create($exclusiveOfferData);
            }
    
            return redirect()->route('offers.create')->with('success', 'Exclusive offers for specific retailers added successfully.');
        }
    
        // Handle general and exclusive offers (First Checkbox)
        if ($request->has('exclusive_offer') && $request->exclusive_offer) {
            $generalOfferData = $this->prepareOfferData($request, null, $request->general_data_fee);
            Offer::create($generalOfferData);
    
            // Create exclusive offers for selected retailers
            if (isset($request->retailer_ids)) {
                foreach ($request->retailer_ids as $retailerId) {
                    $exclusiveOfferData = $this->prepareOfferData($request, $retailerId, $request->exclusive_data_fee);
                    Offer::create($exclusiveOfferData);
                }
            }
    
            return redirect()->route('offers.create')->with('success', 'General and exclusive offers added successfully.');
        }
    
        // Default case: Create general offer
        $generalOfferData = $this->prepareOfferData($request, null, $request->general_data_fee);
        Offer::create($generalOfferData);
    
        return redirect()->route('offers.create')->with('success', 'General offer added successfully.');
    }
    
    
    /**
     * Prepare offer data for general or exclusive offers
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $retailerId
     * @param float|null $dataFee
     * @return array
     */
    private function prepareOfferData($request, $retailerId = null, $dataFee = null)
    {
        return [
            'product_name' => $request->product_name,
            'provincial_sku' => $request->provincial_sku,
            'gtin' => $request->gtin,
            'province' => $request->province,
            'unit_cost' => $request->unit_cost,
            'category' => $request->category,
            'brand' => $request->brand,
            'case_quantity' => $request->case_quantity,
            'offer_start' => $request->offer_start,
            'offer_end' => $request->offer_end,
            'product_size' => $request->product_size,
            'thc_range' => $request->thc_range,
            'cbd_range' => $request->cbd_range,
            'comment' => $request->comment,
            'product_link' => $request->product_link,
            'lp_id' => $request->lp_id,
            'offer_date' => $request->offer_date,
            'data_fee' => $dataFee, // General or exclusive data fee
            'retailer_id' => $retailerId, // Set retailer ID for exclusive offer, null for general offer
        ];
    }
}
