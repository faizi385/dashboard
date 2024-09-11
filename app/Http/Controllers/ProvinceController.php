<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::all();
        return view('provinces.index', compact('provinces'));
    }

    public function create()
    {
        return view('provinces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:provinces',
            'slug' => 'required|string|max:255|unique:provinces',
            'timezone_1' => 'string|max:255',
            'timezone_2' => 'string|max:255',
            'tax_value' => 'required|numeric',
            'status' => 'required|boolean',
        ]);

        Province::create($request->all());

        return redirect()->route('provinces.index')->with('toast_success', 'Province created successfully.');
    }

    public function edit(Province $province)
    {
        return view('provinces.edit', compact('province'));
    }

    public function update(Request $request, Province $province)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:provinces,slug,' . $province->id,
            'timezone_1' => 'required|string|max:255',
            'timezone_2' => 'required|string|max:255',
            'tax_value' => 'required|numeric',
            'status' => 'required|boolean',
        ]);

        $province->update($request->all());

        return redirect()->route('provinces.index')->with('toast_success', 'Province updated successfully.');
    }
    public function updateStatus(Request $request, Province $province)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);
    
        $province->status = $request->input('status');
        $province->save();
    
        return response()->json(['message' => 'Status updated successfully.']);
    }
    
    public function destroy(Province $province)
    {
        $province->delete();
        return redirect()->route('provinces.index')->with('toast_success', 'Province deleted successfully.');
    }
}
