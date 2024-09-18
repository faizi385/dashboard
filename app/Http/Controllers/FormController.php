<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
    public function submitRetailerForm(Request $request)
    {
        // Validate and handle the submitted form data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'corporate_name' => 'nullable|string',
            'dba' => 'nullable|string',
            'password' => 'nullable|string|confirmed',
            'address.street_no' => 'nullable|string',
            'address.street_name' => 'nullable|string',
            'address.province' => 'nullable|string',
            'address.city' => 'nullable|string',
            'address.location' => 'nullable|string',
            'contact_person.name' => 'nullable|string',
            'contact_person.phone' => 'nullable|string',
        ]);

        // Process the form data as needed

        return redirect()->back()->with('success', 'Form submitted successfully!');
    }
}
