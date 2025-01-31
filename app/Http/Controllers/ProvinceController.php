<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::all();
        return view('super_admin.provinces.index', compact('provinces'));
    }

    public function create()
    {
        return view('super_admin.provinces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
           'name' => [
    'required',
    'string',
    'max:255',
    'regex:/^[a-zA-Z\s]+$/', // Ensures only alphabets and spaces are allowed
    function ($attribute, $value, $fail) {
        $existing = Province::withTrashed()->where('name', $value)->first();
        if ($existing && !$existing->trashed()) {
            $fail('The province name has already been taken.');
        }
    },
],
'slug' => [
    'required',
    'string',
    'max:255',
    'regex:/^[a-zA-Z\s]+$/',
    function ($attribute, $value, $fail) {
        $existing = Province::withTrashed()->where('slug', $value)->first();
        if ($existing && !$existing->trashed()) {
            $fail('The province slug has already been taken.');
        }
    },
],

            'timezone_1' => 'nullable|string|max:255',
            'timezone_2' => 'nullable|string|max:255',
            'tax_value' => [
                'required',
                'numeric',
                'regex:/^\d{1,2}(\.\d{1,2})?$/', // Two digits before the decimal and up to two digits after
            ],
            'status' => 'required|boolean',
        ]);
    
        Province::create($request->all());
    
        return redirect()->route('provinces.index')->with('toast_success', 'Province created successfully.');
    }
    

    public function edit(Province $province)
    {
        return view('super_admin.provinces.edit', compact('province'));
    }

    public function update(Request $request, Province $province)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:provinces,slug,' . $province->id,
            'timezone_1' => 'nullable|string|max:255',
            'timezone_2' => 'nullable|string|max:255',
            'tax_value' => [
                'required',
                'numeric',
                'regex:/^\d{1,2}(\.\d{1,2})?$/', // Two digits before the decimal and up to two digits after
            ],
        ], [
            'tax_value.regex' => 'The tax value format is invalid Correct format: 9.00',
    
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
