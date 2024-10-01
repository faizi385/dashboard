<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Retailer;
use App\Models\RetailerAddress; // Address model
use Illuminate\Http\Request;
use App\Mail\RetailerFormMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class RetailerController extends Controller
{
    public function index()
    {
        $retailers = Retailer::with('address')->get(); // Fetch all retailers with addresses
        return view('retailer.index', compact('retailers'));
    }

    public function dashboard()
    {
        return view('retailer.dashboard');
    }

    public function create()
    {
        return view('retailer.create'); 
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:retailers,email', 
            'phone' => 'nullable|string',
        ]);

        $retailer = Retailer::create($validatedData);
        $token = base64_encode($retailer->id);
        $link = route('retailer.fillForm', ['token' => $token]);

        Mail::to($validatedData['email'])->send(new RetailerFormMail($link));

        return redirect()->route('retailer.create')->with('success', 'Retailer created and email sent!');
    }

    public function showForm($token)
    {
        $retailerId = base64_decode($token);
        $retailer = Retailer::findOrFail($retailerId);

        return view('retailer.complete_form', compact('retailer'));
    }
    
    
    public function submitForm(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:retailers,email,' . $request->retailer_id,
            'phone' => 'nullable|string',
            'corporate_name' => 'nullable|string',
            'dba' => 'nullable|string',
            'password' => 'required|confirmed|min:8',
            'addresses.*.street_no' => 'nullable|string|max:50',
            'addresses.*.street_name' => 'nullable|string|max:255',
            'addresses.*.province' => 'nullable|string|max:255',
            'addresses.*.city' => 'nullable|string|max:255',
            'addresses.*.location' => 'nullable|string|max:255',
            'addresses.*.contact_person_name' => 'nullable|string|max:255',
            'addresses.*.contact_person_phone' => 'nullable|string|max:20',
        ]);
    
        // Update the retailer
        $retailer = Retailer::findOrFail($request->retailer_id);
        $retailer->update($validatedData);
    
        // Create or update the User record
        $user = User::updateOrCreate(
            ['email' => $validatedData['email']],
            [
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'password' => Hash::make($validatedData['password']),
                'phone' => $validatedData['phone'],
            ]
        );
    
        // Fetch the role by original_name
        $role = Role::where('original_name', 'Retailer')->first(); // Adjust 'Retailer' if necessary
    
        if ($role) {
            // Assign role to the user
            $user->assignRole($role->name);
        } else {
            return redirect()->back()->with('error', 'Role not found.');
        }
    
        // Clear existing addresses and create new ones
        $retailer->address()->delete();
    
        foreach ($request->input('addresses', []) as $addressData) {
            $retailer->address()->create($addressData);
        }
    
        // Redirect with success message
        return redirect()->route('login')->with('success', 'Retailer information completed successfully. Please log in.');
    }
    
    
    public function edit($id)
    {
        $retailer = Retailer::with('address')->findOrFail($id);
        return view('retailer.edit', compact('retailer'));
    }
    

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'corporate_name' => 'nullable|string|max:255',
            'dba' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'street_no' => 'nullable|string|max:50',
            'street_name' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
        ]);

        $retailer = Retailer::findOrFail($id);
        $retailer->update($request->only([
            'first_name', 'last_name', 'corporate_name', 'dba', 'phone', 'email'
        ]));

        $retailer->address()->updateOrCreate(
            ['retailer_id' => $retailer->id],
            $request->only([
                'street_no', 'street_name', 'province', 'city', 'location',
                'contact_person_name', 'contact_person_phone'
            ])
        );

        return redirect()->route('retailer.index')->with('success', 'Retailer updated successfully.');
    }

    public function destroy($id)
    {
        $retailer = Retailer::findOrFail($id);
        $retailer->delete();
        
        return redirect()->route('retailer.index')->with('success', 'Retailer deleted successfully.');
    }

    public function show($id)
    {
        $retailer = Retailer::with('address')->findOrFail($id);
        return view('retailer.show', compact('retailer'));
    }

    public function createAddress($id)
    {
        $retailer = Retailer::findOrFail($id);
        return view('retailer.create_address', compact('retailer'));
    }

    public function editAddress($id)
    {
        $retailer = Retailer::findOrFail($id);
        return view('retailer.edit_address', compact('retailer'));
    }

    public function updateAddress(Request $request, $id)
    {
        $retailer = Retailer::findOrFail($id);

        $addressData = $request->validate([
            'street_no' => 'nullable|string|max:50',
            'street_name' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
        ]);

        $retailer->address()->updateOrCreate([], $addressData);

        return redirect()->route('retailer.show', $id)->with('success', 'Address updated successfully.');
    }

    public function storeAddress(Request $request, $id)
    {
        $request->validate([
            'addresses.*.street_no' => 'required|string|max:50',
            'addresses.*.street_name' => 'required|string|max:255',
            'addresses.*.province' => 'required|string|max:255',
            'addresses.*.city' => 'required|string|max:255',
            'addresses.*.location' => 'required|string|max:255',
            'addresses.*.contact_person_name' => 'nullable|string|max:255',
            'addresses.*.contact_person_phone' => 'nullable|string|max:20',
        ]);
    
        $retailer = Retailer::findOrFail($id);
    
        foreach ($request->addresses as $addressData) {
            $retailer->address()->create($addressData);
        }
    
        return redirect()->route('retailer.show', $id)->with('success', 'Addresses added successfully.');
    }
    

}
