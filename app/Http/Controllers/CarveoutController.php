<?php

namespace App\Http\Controllers;

use App\Models\Lp;
use Carbon\Carbon;
use App\Models\Carveout;
use App\Models\RetailerStatement;
use App\Models\CleanSheet;
use App\Models\Province;
use App\Models\Retailer;
use Illuminate\Http\Request;

class CarveoutController extends Controller
{
    public function index($lp_id)
{
    // Get the authenticated user's LP
    $userLp = Lp::where('user_id', auth()->user()->id)->first();

    // Initialize carveouts and lp variables
    $carveouts = collect();
    $lp = null;

    // Check if the user is a Super Admin or an LP user
    if (auth()->user()->hasRole('Super Admin')) {
        // Super Admin can see carveouts for the specified LP ID or all carveouts if lp_id is 0
        $carveouts = Carveout::with(['retailer', 'lp'])
            ->when($lp_id > 0, function ($query) use ($lp_id) {
                return $query->where('lp_id', $lp_id);
            })
            ->get();

        // Fetch the LP details based on lp_id
        $lp = Lp::find($lp_id);
    } elseif ($userLp) {
        // For LP users: Fetch carveouts related to their own LP ID
        $carveouts = Carveout::with(['retailer', 'lp'])
            ->where('lp_id', $userLp->id)
            ->get();

        // Set the authenticated LP for the LP user
        $lp = $userLp;
    }

    // Fetch all retailers (optional based on view requirements)
    $retailers = Retailer::all();

    // Fetch all LPs (optional based on view requirements)
    $lps = Lp::all();

    // Fetch all provinces from the database
    $provinces = Province::all(); // Assumes you have a Province model

    // Return the view with the required data
    return view('super_admin.carveouts.index', compact('carveouts', 'retailers', 'lp_id', 'lps', 'lp', 'provinces'));
}


    public function create()
    {
        $retailers = Retailer::all(); // Fetch all retailers
        return view('super_admin.carveouts.create', compact('retailers')); // Pass retailers to the create view
    }
    public function store(Request $request)
    {
        // Get the authenticated user's LP
        $userLp = Lp::where('user_id', auth()->user()->id)->first();

        // Validate incoming request
        $request->validate([
            'province' => 'required',
            'retailer' => 'required|exists:retailers,id',
            'location' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'carveout_date' => 'required|date',
            'lp_id' => auth()->user()->hasRole('Super Admin') ? 'required|exists:lps,id' : '', // Only validate lp_id for Super Admin
        ]);

        // For LP users, assign their own LP ID automatically
        $lpId = auth()->user()->hasRole('Super Admin') ? $request->lp_id : $userLp->id;


        $carveoutExists = Carveout::where('retailer_id', $request->retailer)
            ->where('lp_id', $request->lp_id)
            ->when(!empty($request->location), function ($query) use ($request) {
                $query->where('location', $request->location);
            })
            ->when(!empty($request->sku), function ($query) use ($request) {
                $query->where('sku', $request->sku);
            })
            ->whereYear('date', Carbon::parse($request->carveout_date)->year)
            ->whereMonth('date', Carbon::parse($request->carveout_date)->month)
            ->exists();

        if ($carveoutExists) {
            return redirect()->back()->withErrors(['retailer' => 'This retailer can only have one carveout per month.']);
        }

        $province = Province::where('id',$request->province)->first();

        // dd($request->province);

        // Create the carveout
        $carveout = Carveout::create([
            'province_id' => $province->id,
            'province' => $province->name,
            'province_slug' => $province->slug,
            'retailer_id' => $request->retailer,
            'location' => $request->location,
            'sku' => $request->sku,
            'date' => Carbon::parse($request->carveout_date)->startOfMonth(),
            'lp_id' => $lpId, // Automatically assign the LP ID if the user is an LP
        ]);
        $this->carveout($carveout);
        // Redirect back to the index with success message
        return redirect()->route('carveouts.index', ['lp_id' => $lpId])->with('success', 'Carveout added successfully.');
    }

    public function carveout($data)
    {
        $date = Carbon::parse($data->date);
        $carveout = $data;
        $RetailerStatements = [];

        $RetailerStatements = RetailerStatement::where('lp_id', $carveout->lp_id)
            ->where('province_id',$carveout->province_id)
            ->where('retailer_id', $carveout->retailer_id)
            ->whereMonth('reconciliation_date', $date->format('m'))
            ->whereYear('reconciliation_date', $date->format('Y'))
            ->when(!empty($carveout->sku), function ($query) use ($carveout) {
                $query->where('sku',$carveout->sku);
            })
            ->when(!empty($carveout->location), function ($query) use ($carveout) {
                $query->where('address_id',$carveout->location);
            })
            ->get();

        foreach ($RetailerStatements as $key => $r) {
            $r->update([
                'flag' => 1,
                'carve_out' => 'yes',
                'carveout_id' => $carveout->id
            ]);
        }

        $this->update_cleanSheet_flag($carveout);
    }
    public function update_cleanSheet_flag($carveout){

        $date = Carbon::parse($carveout->date);
        $clean_sheets = [];

        $clean_sheets = CleanSheet::where('lp_id', $carveout->lp_id)
            ->where('province_id',$carveout->province_id)
            ->where('retailer_id', $carveout->retailer_id)
            ->whereMonth('reconciliation_date', $date->format('m'))
            ->whereYear('reconciliation_date', $date->format('Y'))
            ->when(!empty($carveout->sku), function ($query) use ($carveout) {
                $query->where('sku',$carveout->sku);
            })
            ->when(!empty($carveout->location), function ($query) use ($carveout) {
                $query->where('address_id',$carveout->location);
            })
            ->get();

        foreach ($clean_sheets as $key => $r) {
                $r->update([
                    'c_flag' => 'yes',
                    'carveout_id' => $carveout->id
                ]);
            }

    }

    public function edit($id)
    {
        // Fetch the carveout to be edited
        $carveout = Carveout::findOrFail($id); // Throws 404 if not found
        $retailers = Retailer::all(); // Fetch all retailers
        $lps = Lp::all(); // Fetch all LPs

        return view('super_admin.carveouts.edit', compact('carveout', 'retailers', 'lps')); // Pass data to the edit view
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'province' => 'required',
            'retailer' => 'required|exists:retailers,id',
            'location' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'carveout_date' => 'required|date',
            'lp_id' => 'required|exists:lps,id', // Ensure lp_id exists
        ]);

        // Find the carveout to be updated
        $carveout = Carveout::findOrFail($id);

        // Check if a carveout already exists for the retailer in the same month, excluding the current carveout
        $carveoutExists = Carveout::where('retailer_id', $request->retailer)
            ->whereYear('date', Carbon::parse($request->carveout_date)->year)
            ->whereMonth('date', Carbon::parse($request->carveout_date)->month)
            ->where('id', '!=', $carveout->id) // Exclude the current carveout
            ->exists();

        if ($carveoutExists) {
            return redirect()->back()->withErrors(['retailer' => 'This retailer can only have one carveout per month.']);
        }

        // Update the carveout
        $carveout->update([
            'province' => $request->province,
            'retailer_id' => $request->retailer,
            'location' => $request->location,
            'sku' => $request->sku,
            'date' => $request->carveout_date,
            'lp_id' => $request->lp_id,
        ]);

        // Redirect back to the index with success message
        return redirect()->route('carveouts.index', ['lp_id' => $request->lp_id])->with('success', 'Carveout updated successfully.');
    }

    public function destroy($id)
    {
        // Find the carveout to be deleted
        $carveout = Carveout::findOrFail($id);

        $RetailerStatements = RetailerStatement::where('carveout_id',$carveout->id)->get();
        if (count($RetailerStatements) > 0) {
            foreach ($RetailerStatements as $key => $r) {
                $r->update([
                    'flag' => 0,
                    'carve_out' => 'no',
                    'carveout_id' => null
                ]);
            }
        }
        $clean_sheets = CleanSheet::where('carveout_id',$carveout->id)->get();
        if (count($clean_sheets) > 0) {

            foreach ($clean_sheets as $key => $r) {
                    $r->update([
                        'c_flag' => 'no',
                        'carveout_id' => null
                    ]);
                }
        }

        // Delete the carveout
        $carveout->delete();

        // Redirect back to the index with success message
        return redirect()->back()->with('success', 'Carveout deleted successfully.');
    }
}
