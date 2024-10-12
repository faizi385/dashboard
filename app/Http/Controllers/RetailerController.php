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
        return view(' super_admin.retailer.index', compact('retailers'));
    }

    public function dashboard()
    {
        $retailer = auth()->user()->retailer; // Fetch the retailer linked to the logged-in user
    
        if (!$retailer) {
            return redirect()->route('home')->with('error', 'No associated retailer found.');
        }
    
        return view('super_admin.retailer.dashboard', compact('retailer'));
    }
    

    public function create()
    {
        return view('super_admin.retailer.create'); 
    }

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',  // Only allow letters and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',  // Only allow letters and spaces
            ],
            'email' => [
                'required',
                'email',
                'unique:retailers,email',
                'regex:/^[\w\.-]+@[\w\.-]+\.\w{2,4}$/'  // Example regex for standard email formats
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+?\d{1,3}\s*\(?\d{3}?\)?\s*\d{3}[-\s]?\d{4}$/'  // Accepts formats like +1 (425) 274-9782
            ],
        ]);
    
        // Add user_id and status to the validated data
        $validatedData['user_id'] = auth()->id();  // Add the authenticated user's ID
        $validatedData['status'] = 'requested'; // Set initial status
    
        // Create the retailer
        $retailer = Retailer::create($validatedData);
    
        // Generate a token and the link
        $token = base64_encode($retailer->id);
        $link = route('retailer.fillForm', ['token' => $token]);
    
        // Send an email with the link
        Mail::to($validatedData['email'])->send(new RetailerFormMail($link));
    
        // Redirect back with a success message
        return redirect()->route('retailer.create')->with('success', 'Retailer created and email sent!');
    }
    
    
    
    public function showForm($token)
    {
        $retailerId = base64_decode($token);
        $retailer = Retailer::findOrFail($retailerId);

        return view('super_admin.retailer.complete_form', compact('retailer'));
    }
    
    public function submitForm(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:retailers,email,' . $request->retailer_id,
        'phone' => 'required|string',
        'corporate_name' => 'nullable|string',
        'dba' => 'required|string',
        'password' => 'required|confirmed|min:8',
        'addresses.*.street_no' => 'nullable|string|max:50',
        'addresses.*.street_name' => 'nullable|string|max:255',
        'addresses.*.province' => 'nullable|string|max:255',
        'addresses.*.city' => 'nullable|string|max:255',
        'addresses.*.location' => 'nullable|string|max:255',
        'addresses.*.contact_person_name' => 'nullable|string|max:255',
        'addresses.*.contact_person_phone' => 'nullable|string|max:20',
    ]);

    // Find the retailer based on the provided ID
    $retailer = Retailer::findOrFail($request->retailer_id);

    // Update retailer details with the validated data
    $retailer->update([
        'corporate_name' => $validatedData['corporate_name'],
        'dba' => $validatedData['dba'],
        'status' => 'approved', // Change status to 'approved'
    ]);

    // Create or update the User record for the retailer
    $user = User::updateOrCreate(
        ['email' => $validatedData['email']], // Unique identifier
        [
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'],
        ]
    );

    // Fetch the role by original name (ensure the role exists)
    $role = Role::where('original_name', 'Retailer')->first(); // Adjust 'Retailer' if necessary

    if ($role) {
        // Assign the role to the user
        $user->assignRole($role->name);
    } else {
        return redirect()->back()->with('error', 'Role not found.');
    }

    // Clear existing addresses and create new ones
    $retailer->address()->delete();

    foreach ($request->input('addresses', []) as $addressData) {
        $retailer->address()->create($addressData);
    }

    // Redirect to the login page with a success message
    return redirect()->route('login')->with('success', 'Retailer information completed successfully. Please log in.');
}

    
    public function edit($id)
    {
        $retailer = Retailer::with('address')->findOrFail($id);
        return view('super_admin.retailer.edit', compact('retailer'));
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
            'first_name', 'last_name', 'corporate_name', 'dba', 'phone', 'email','status'
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
        return view('super_admin.retailer.show', compact('retailer'));
    }

    public function createAddress($id)
    {
        $retailer = Retailer::findOrFail($id);
        return view('super_admin.retailer.create_address', compact('retailer'));
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
        // Validate the request with custom error messages
        $request->validate([
            'addresses.*.street_no' => 'required|string|max:50',
            'addresses.*.street_name' => 'required|string|max:255',
            'addresses.*.province' => 'required|string|max:255',
            'addresses.*.city' => 'required|string|max:255',
            'addresses.*.location' => 'required|string|max:255',
            'addresses.*.contact_person_name' => 'nullable|string|max:255',
            'addresses.*.contact_person_phone' => 'nullable|string|max:20',
        ], [
            'addresses.*.street_no.required' => 'Street No is required.',
            'addresses.*.street_name.required' => 'Street Name is required.',
            'addresses.*.province.required' => 'Province is required.',
            'addresses.*.city.required' => 'City is required.',
            'addresses.*.location.required' => 'Location is required.',
            'addresses.*.contact_person_name.max' => 'Contact Person Name cannot exceed 255 characters.',
            'addresses.*.contact_person_phone.max' => 'Contact Person Phone cannot exceed 20 characters.',
        ]);
    
        // Find the retailer or fail
        $retailer = Retailer::findOrFail($id);
    
        // Loop through the addresses and create them
        foreach ($request->addresses as $addressData) {
            $retailer->address()->create($addressData);
        }
    
        // Redirect back with a success message
        return redirect()->route('retailer.show', $id)->with('success', 'Addresses added successfully.');
    }
    
    

}
