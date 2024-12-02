<?php

namespace App\Http\Controllers;

use App\Models\Lp;
use App\Models\Offer;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Province;
use App\Models\Retailer;
use App\Models\CleanSheet;
use App\Models\RetailerStatement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\OffersExport;
use App\Traits\GenerateRSAdd;
use App\Imports\OffersImport;
use Maatwebsite\Excel\Facades\Excel;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('month');
        if(!empty($date)){
            $date = Carbon::parse($date)->format('Y-m-01');
        }
        else{
            $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        }
        $lps = Lp::all();
        $lp = Lp::where('user_id', $user->id)->first();
        $provinces = Province::where('status',1)->get();
        if ($lp) {
            $offers = Offer::where('lp_id', $lp->id)->where('offer_date',$date)->get();
        } else {
            $offers = collect();
        }
        return view('LP_portal.offers.index', compact('offers', 'lp', 'lps','provinces','date'));
    }

    public function allOffers(Request $request)
    {
        $date = $request->get('month');
        if(!empty($date)){
            $date = Carbon::parse($date)->format('Y-m-01');
        }
        else{
            $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        }
        $lps = Lp::all();

        $provinces = Province::where('status',1)->get();
        $lp = null;
        $offers = Offer::where('offer_date',$date)->get();

        return view('super_admin.offers.all-offers', compact('offers', 'lp', 'lps','provinces','date'));
    }

    public function allOffersLPWise(Request $request)
    {
        $date = $request->get('month');
        if(!empty($date)){
            $date = Carbon::parse($date)->format('Y-m-01');
        }
        else{
            $date = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-01');
        }
        $lps = Lp::all();

        $lpId = $request->get('lp_id');
        $provinces = Province::where('status',1)->get();
        if ($lpId) {
            $lp = Lp::findOrFail($lpId);
            $offers = Offer::where('lp_id', $lpId)->where('offer_date',$date)->get();
        }

        return view('super_admin.offers.index', compact('offers', 'lp', 'lps','provinces','date'));
    }


    public function edit($id)
    {
        $offer = Offer::findOrFail($id);

        // Fetch all retailers from the database
        $retailers = Retailer::all(); // Replace `Retailer` with the correct model for your retailers

        return view('super_admin.offers.edit', compact('offer', 'retailers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
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

        $this->deleteRetailerStatement($offer);
        $this->update_clean_sheet($offer);

        $offer->delete();
        return redirect()->route('offers.index')->with('success', 'Offer deleted successfully.');
    }
    public function deleteRetailerStatement($offer)
    {
        RetailerStatement::where('offer_id', $offer->id)->delete();
    }

    public function update_clean_sheet($offer)
    {
        $clean_sheets = CleanSheet::where('offer_id', $offer->id)->get();
        foreach ($clean_sheets as $clean_sheet) {
            $statementModel = CleanSheet::find($clean_sheet->id);
            if ($statementModel) {
                if($statementModel->flag == 2){
                    $statementModel->update(['flag' => '0','comments'=>'Record not found in the Master Catalog and Offer', 'dqi_flag'=>'0','dqi_per'=>null,'dqi_fee'=>null,'offer_id'=>null,'c_flag'=>'na','carveout_id'=>null]);
                }
                if($statementModel->flag == 3){
                    $statementModel->update(['flag' => '1' ,'comments'=>'Record found in the Master Catalog', 'dqi_flag'=>'0','dqi_per'=>null,'dqi_fee'=>null,'offer_id'=>null,'c_flag'=>'na','carveout_id'=>null]);
                }
            }
        }
    }

    // Show the offer creation form with LPs and Retailers data
    public function create(Request $request)
{
    $lps = Lp::all(); // Fetch LPs
    $retailers = Retailer::all(); // Fetch Retailers
    $provinces = Province::where('status',1)->get();
    // Check if lp_id is passed through the request, if not set $lp as null
    $lp = $request->lp_id ? Lp::find($request->lp_id) : null;

    return view('super_admin.offers.create', compact('lps', 'retailers', 'lp','provinces'));
}


    // Export all offers to an Excel file
    public function export()
    {
        return Excel::download(new OffersExport, 'offers.xlsx');
    }

    // Handle the bulk import of offers with LP selection
    public function import(Request $request)
    {
        // Validate the uploaded file, LP selection, and selected month
        $request->validate([
            'offerExcel' => 'required|file|mimes:xlsx,xls,csv',
            'lp_id' => 'required|exists:lps,id', // Ensure the selected LP exists
            'source' => 'required|integer', // Ensure source is included
            'month' => 'required|in:current,next', // Ensure a valid month is selected
        ]);

        $lpId = $request->lp_id;
        $province = $request->province;
        $source = $request->source;
        $selectedMonth = $request->month; // Capture the selected month
        $lpName = Lp::where('id', $lpId)->value('name');

        // Handle Offer Imports
        if ($request->hasFile('offerExcel')) {
            $filePath = $request->file('offerExcel')->store('uploads');

            try {
                // Import Offers and check for errors
                $import = new OffersImport($selectedMonth, $lpId, $source, $lpName,$province);
                Excel::import($import, $filePath);

                // Check for any import errors (if the OffersImport tracks errors)
                $importErrors = $import->getErrors(); // Retrieve any header or data errors

                // dd($importErrors['count']);

                if(isset($importErrors['count']) && $importErrors['count'] > 0){
                    return redirect()->back()->with('toast_success', 'Offers imported successfully for the selected LP! But '.$importErrors['count'].' Offers Already existed.');
                }

                if (!empty($importErrors)) {
                    // If there are errors, show them in a single message
                    $errorMessage = implode(', ', $importErrors);
                    return redirect()->back()->with('error', $errorMessage);
                }
            } catch (\Exception $e) {
                // Catch exceptions (such as missing headers) and display an error message
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->withErrors('The offer file is required.');
        }

        // Redirect back with a success message if no errors were found
        return redirect()->back()->with('toast_success', 'Offers imported successfully for the selected LP!');
    }


    public function store(Request $request)
    {

        $rules = [
            'product_name' => 'required|string|max:255',
            'provincial_sku' => 'required|string|max:255',
            'gtin' => 'required|digits_between:1,13',
            'province_id' => 'nullable|exists:provinces,id',
            'general_data_fee' => 'required|numeric|min:0',
            'exclusive_data_fee' => 'nullable|numeric|min:0',
            'unit_cost' => 'required|numeric',
            'category' => 'required|string|max:255|regex:/^[^\d]*$/',
            'brand' => 'required|string|max:255|regex:/^[^\d]*$/',
            'case_quantity' => 'required|integer',
            'offer_start' => 'required|date',
            'offer_end' => 'required|date|after_or_equal:offer_start',
            'product_size' => 'required|integer',
            'thc_range' => 'required|string|max:255',
            'cbd_range' => 'required|string|max:255',
            'lp_id' => 'required|exists:lps,id',
            'offer_date' => 'nullable|date',
            'product_link' => 'nullable|url|max:255',
            'comment' => 'nullable|string|max:255',
            'source' => 'required|integer',
        ];

        if (!is_null($request->exclusive_data_fee)) {
            $rules['exclusive_data_fee'] = 'required|numeric|min:0';
            $rules['retailer_ids'] = 'required|array';
            $rules['retailer_ids.*'] = 'exists:retailers,id';
        }

        if ($request->has('exclusive_retailer_ids')) {
            $rules['exclusive_retailer_ids'] = 'required|array';
            $rules['exclusive_retailer_ids.*'] = 'exists:retailers,id';
        }
        $validatedData = $request->validate($rules);

        $this->storeProduct($validatedData);
        if ($request->has('exclusive_retailer_ids')) {
            foreach ($request->exclusive_retailer_ids as $retailerId) {
                $exclusiveOfferData = $this->prepareOfferData($request, $retailerId, $request->general_data_fee);
                $exclusiveOfferData['source'] = $request->source;
                $offer = Offer::create($exclusiveOfferData);
                $retailerStatementImpact = (new class
                {
                    use GenerateRSAdd;
                })->generateRS($offer->id);
            }

            return redirect()->route('offers.create')->with('success', 'Exclusive offers for specific retailers added successfully.');
        }
        if ($request->has('exclusive_data_fee') && $request->has('retailer_ids')) {
            $generalOfferData = $this->prepareOfferData($request, null, $request->general_data_fee);
            $generalOfferData['source'] = $request->source;
            $offer  = Offer::create($generalOfferData);
            $retailerStatementImpact = (new class
            {
                use GenerateRSAdd;
            })->generateRS($offer->id);

            if (isset($request->retailer_ids)) {
                foreach ($request->retailer_ids as $retailerId) {
                    $exclusiveOfferData = $this->prepareOfferData($request, $retailerId, $request->exclusive_data_fee);
                    $exclusiveOfferData['source'] = $request->source;
                    $offer = Offer::create($exclusiveOfferData);
                    $retailerStatementImpact = (new class
                    {
                        use GenerateRSAdd;
                    })->generateRS($offer->id);
                }
            }

            return redirect()->route('offers.create')->with('success', 'General and exclusive offers added successfully.');
        }
        $generalOfferData = $this->prepareOfferData($request, null, $request->general_data_fee);
        $generalOfferData['source'] = $request->source;
        $offer = Offer::create($generalOfferData);
        $retailerStatementImpact = (new class
        {
            use GenerateRSAdd;
        })->generateRS($offer->id);
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
    {  $lpName = LP::find($request->lp_id)->name;
        return [
            'product_name' => $request->product_name,
            'provincial_sku' => $request->provincial_sku,
            'gtin' => $request->gtin,
            'province_id' => $request->province_id,
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
            'lp_name' => $lpName,
           'offer_date' => Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d'),
            'data_fee' => $dataFee,
            'retailer_id' => $retailerId,
        ];
    }

    private function storeProduct($data)
    {
        $existingProduct = Product::where('gtin', $data['gtin'])->first();

        if (!$existingProduct) {
            $product = Product::create([
                'product_name' => $data['product_name'],
                'provincial_sku' => $data['provincial_sku'],
                'gtin' => $data['gtin'],
                'province_id' => $data['province_id'],
                'category' => $data['category'],
                'brand' => $data['brand'],
                'lp_id' => $data['lp_id'],
                'product_size' => $data['product_size'],
                'thc_range' => $data['thc_range'],
                'cbd_range' => $data['cbd_range'],
                'comment' => $data['comment'],
                'product_link' => $data['product_link'],
                'unit_cost' => $data['unit_cost'],
            ]);
        } else {
            $product = $existingProduct;
        }

        $existingVariation = ProductVariation::where('provincial_sku', $data['provincial_sku'])
                                    ->where('gtin', $data['gtin'])
                                    ->first();
        if ($existingVariation) {
            if ($existingVariation->province_id !== $data['province_id']) {
                ProductVariation::create([
                    'product_name' => $data['product_name'],
                    'provincial_sku' => $data['provincial_sku'],
                    'gtin' => $data['gtin'],
                    'province_id' => $data['province_id'],
                    'category' => $data['category'],
                    'brand' => $data['brand'],
                    'lp_id' => $data['lp_id'],
                    'product_size' => $data['product_size'],
                    'thc_range' => $data['thc_range'],
                    'cbd_range' => $data['cbd_range'],
                    'comment' => $data['comment'],
                    'product_link' => $data['product_link'],
                    'price_per_unit' => $data['unit_cost'],
                ]);
            }
            return;
        }
        ProductVariation::create([
            'product_name' => $data['product_name'],
            'provincial_sku' => $data['provincial_sku'],
            'gtin' => $data['gtin'],
            'province_id' => $data['province_id'],
            'category' => $data['category'],
            'brand' => $data['brand'],
            'lp_id' => $data['lp_id'],
            'product_size' => $data['product_size'],
            'thc_range' => $data['thc_range'],
            'cbd_range' => $data['cbd_range'],
            'comment' => $data['comment'],
            'product_link' => $data['product_link'],
            'price_per_unit' => $data['unit_cost'],
        ]);
    }



}
