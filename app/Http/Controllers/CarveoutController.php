<?php

namespace App\Http\Controllers;

use App\Models\Lp;
use App\Models\Carveout;
use Illuminate\Http\Request;
use App\Models\Retailer;

class CarveoutController extends Controller
{
    public function index($lp_id)
{
    // If lp_id is 0 (Super Admin), fetch all carveouts
    if ($lp_id == 0) {
        $carveouts = Carveout::with(['retailer', 'lp'])->get(); // Fetch all carveouts
    } else {
        $carveouts = Carveout::with(['retailer', 'lp'])->where('lp_id', $lp_id)->get(); // Fetch carveouts for the specified LP
    }

    $retailers = Retailer::all(); // Fetch all retailers
    $lps = Lp::all(); // Fetch all LPs

    return view('carveouts.index', compact('carveouts', 'retailers', 'lp_id', 'lps'));
}

    

    public function create()
    {
        $retailers = Retailer::all(); // Fetch all retailers
        return view('carveouts.create', compact('retailers')); // Pass retailers to the create view
    }

    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'province' => 'required',
            'retailer' => 'required|exists:retailers,id',
            'location' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'carveout_date' => 'required|date',
            'lp_id' => 'required|exists:lps,id', // Add lp_id validation
        ]);

        // Create the carveout
        Carveout::create([
            'province' => $request->province,
            'retailer_id' => $request->retailer,
            'location' => $request->location,
            'sku' => $request->sku,
            'date' => $request->carveout_date,
            'lp_id' => $request->lp_id, // Add lp_id here
        ]);

        // Redirect back to the index with success message
        return redirect()->route('carveouts.index', ['lp_id' => $request->lp_id])->with('success', 'Carveout added successfully.');
    }

    public function edit($id)
    {
        // Fetch the carveout to be edited
        $carveout = Carveout::findOrFail($id); // Throws 404 if not found
        $retailers = Retailer::all(); // Fetch all retailers
        $lps = Lp::all(); // Fetch all LPs

        return view('carveouts.edit', compact('carveout', 'retailers', 'lps')); // Pass data to the edit view
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
