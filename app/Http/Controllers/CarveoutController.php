<?php

namespace App\Http\Controllers;

use App\Models\Lp;
use App\Models\Carveout;
use Illuminate\Http\Request;
use App\Models\Retailer;
use Carbon\Carbon;

class CarveoutController extends Controller
{
    public function index($lp_id)
    {
        // Get the authenticated user's LP
        $userLp = Lp::where('user_id', auth()->user()->id)->first();

        // Check if the user is a Super Admin or an LP
        if (auth()->user()->hasRole('Super Admin')) {
            // Super Admin can see carveouts for the specified LP ID or all carveouts if lp_id is 0
            $carveouts = Carveout::with(['retailer', 'lp'])
                ->when($lp_id > 0, function ($query) use ($lp_id) {
                    return $query->where('lp_id', $lp_id);
                })
                ->get();

            // Set the $lp variable for the view based on the selected lp_id
            $lp = Lp::find($lp_id);
        } else {
            // For LP users: Fetch carveouts related to their LP ID
            if ($userLp) {
                $carveouts = Carveout::with(['retailer', 'lp'])
                    ->where('lp_id', $userLp->id)
                    ->get();

                // Set the $lp variable to the authenticated LP
                $lp = $userLp; // Use the authenticated LP
            } else {
                // Handle case where no LP is associated with the user (optional)
                $carveouts = collect(); // Return an empty collection
                $lp = null; // No LP found
            }
        }

        // Fetch all retailers (optional based on view requirements)
        $retailers = Retailer::all();

        // Fetch all LPs (optional based on view requirements)
        $lps = Lp::all();

        return view(' super_admin.carveouts.index', compact('carveouts', 'retailers', 'lp_id', 'lps', 'lp')); // Pass $lp to the view
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
            'location' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'carveout_date' => 'required|date',
            'lp_id' => auth()->user()->hasRole('Super Admin') ? 'required|exists:lps,id' : '', // Only validate lp_id for Super Admin
        ]);
    
        // For LP users, assign their own LP ID automatically
        $lpId = auth()->user()->hasRole('Super Admin') ? $request->lp_id : $userLp->id;
    
        // Check if a carveout already exists for the retailer in the same month
        $carveoutExists = Carveout::where('retailer_id', $request->retailer)
            ->whereYear('date', Carbon::parse($request->carveout_date)->year)
            ->whereMonth('date', Carbon::parse($request->carveout_date)->month)
            ->exists();
    
        if ($carveoutExists) {
            return redirect()->back()->withErrors(['retailer' => 'This retailer can only have one carveout per month.']);
        }
    
        // Create the carveout
        Carveout::create([
            'province' => $request->province,
            'retailer_id' => $request->retailer,
            'location' => $request->location,
            'sku' => $request->sku,
            'date' => $request->carveout_date,
            'lp_id' => $lpId, // Automatically assign the LP ID if the user is an LP
        ]);
    
        // Redirect back to the index with success message
        return redirect()->route('carveouts.index', ['lp_id' => $lpId])->with('success', 'Carveout added successfully.');
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
            'location' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
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

        // Delete the carveout
        $carveout->delete();

        // Redirect back to the index with success message
        return redirect()->back()->with('success', 'Carveout deleted successfully.');
    }
}
