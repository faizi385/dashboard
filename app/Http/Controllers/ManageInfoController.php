<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lp;
use Illuminate\Support\Facades\Auth;

class ManageInfoController extends Controller
{
    public function index()
    {
        // Get the logged-in user's email
        $userEmail = Auth::user()->email;
    
        // Fetch the LP information along with its associated address using the user's email
        $lp = Lp::where('primary_contact_email', $userEmail)
                ->with('address') // Ensure the address relationship is loaded
                ->first();
    
        // Handle the case where no LP is found
        if (!$lp) {
            return redirect()->route('home')->with('error', 'No LP information found for your account.');
        }
    
        return view('LP_portal.manage-info.index', compact('lp'));
    }
    
    
    public function update(Request $request)
    {
        // Get the LP based on the logged-in user's email
        $lp = Lp::where('primary_contact_email', Auth::user()->email)->first();
    
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',    
            'email' => 'required|email|max:255',
            'phone' => 'required|string',
            'address.*.street_name' => 'nullable|string|max:255',
            'address.*.postal_code' => 'nullable|numeric',
            'address.*.city' => 'nullable|string|max:255',
        ]);
        
        // Update LP information
        $lp->update([
            'name' => $request->name,
            'primary_contact_email' => $request->email,
            'primary_contact_phone' => $request->phone,
        ]);
    
        // Update LP address
        if ($lp->address) {
            foreach ($lp->address as $address) {
                $addressData = $request->input('address.' . $address->id);
                if ($addressData) {
                    $address->update([
                        'street_name' => $addressData['street_name'],
                        'postal_code' => $addressData['postal_code'],
                        'city' => $addressData['city'],
                    ]);
                }
            }
        }
    
        return redirect()->route('manage-info.index')->with('success', 'Information updated successfully.');
    }
    
}
