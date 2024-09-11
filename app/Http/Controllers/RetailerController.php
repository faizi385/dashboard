<?php

namespace App\Http\Controllers;

use App\Models\Retailer;
use Illuminate\Http\Request;

class RetailerController extends Controller
{
    public function dashboard()
    {
        // Logic for Retailer dashboard
        return view('retailer.dashboard');
    }
    public function create()
{
    return view('retailer.create');  // Corrected path
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:retailers,email',
            'phone' => 'required|string|max:20',
        ]);

        Retailer::create($validated);

        return redirect()->route('retailer.create')->with('success', 'Retailer created successfully.');
    }
    // Add other methods for Retailer functionality
}
