<?php

// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\Retailer;
// use App\Models\RetailerAddress;

// class RetailerPortalController extends Controller
// {
//     public function manageInfo()
//     {
//         $retailer = Auth::user();
    
//         // Ensure that $addresses is always a collection, even if it's null
//         $addresses = $retailer->address ?? collect(); 
    
//         return view('retailer_portal.manage_info', compact('retailer', 'addresses'));
//     }
    
    
    
    
//     public function editProfile()
//     {
//         $retailer = Auth::user();
//         return view('retailer_portal.edit_profile', compact('retailer'));
//     }

//     public function updateProfile(Request $request)
//     {
//         $request->validate([
//             'first_name' => 'required|string|max:255',
//             'last_name' => 'required|string|max:255',
//             'email' => 'required|email|max:255',
//             'phone' => 'required|string|max:255',
//             'corporate_name' => 'nullable|string|max:255',
//             'dba' => 'nullable|string|max:255',
//         ]);

//         $retailer = Auth::user();
//         $retailer->update($request->only([
//             'first_name',
//             'last_name',
//             'email',
//             'phone',
//             'corporate_name',
//             'dba'
//         ]));

//         return redirect()->route('retailer.manageInfo')->with('success', 'Profile updated successfully.');
//     }

//     public function storeAddress(Request $request)
//     {
//         $request->validate([
//             'addresses.*.street_no' => 'required|string|max:255',
//             'addresses.*.street_name' => 'required|string|max:255',
//             'addresses.*.province' => 'required|string|max:255',
//             'addresses.*.city' => 'required|string|max:255',
//             'addresses.*.location' => 'required|string|max:255',
//             'addresses.*.contact_person_name' => 'nullable|string|max:255',
//             'addresses.*.contact_person_phone' => 'nullable|string|max:255',
//         ]);

//         $retailer = Auth::user();

//         foreach ($request->input('addresses', []) as $addressData) {
//             $retailer->address()->create($addressData);
//         }

//         return redirect()->route('retailer.manageInfo')->with('success', 'Addresses added successfully.');
//     }

//     public function addLocation()
//     {
//         return view('retailer_portal.add_location');
//     }

//     public function storeLocation(Request $request)
//     {
//         $request->validate([
//             'street_no' => 'required|string|max:255',
//             'street_name' => 'required|string|max:255',
//             'province' => 'required|string|max:255',
//             'city' => 'required|string|max:255',
//             'location' => 'required|string|max:255',
//             'contact_person_name' => 'nullable|string|max:255',
//         ]);
    
//         $retailer = Auth::user();
    
//         // Update the existing address or create a new one if none exists
//         $retailer->address()->updateOrCreate(
//             ['retailer_id' => $retailer->id], // Assuming you have a foreign key
//             $request->only([
//                 'street_no',
//                 'street_name',
//                 'province',
//                 'city',
//                 'location',
//                 'contact_person_name'
//             ])
//         );
    
//         return redirect()->route('retailer.manageInfo')->with('success', 'Location updated successfully.');
//     }
    
// }
