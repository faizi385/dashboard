<?php

namespace App\Http\Controllers\Auth;

use App\Models\LP;
use App\Models\User;
use App\Mail\LpFormMail;
use App\Models\Province;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Mail\LpPendingStatusMail;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
{
    // Fetch all provinces from the database
    $provinces = Province::all();

    // Pass the provinces to the view
    return view('auth.register', compact('provinces'));
}


    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
   
    
    public function store(Request $request): RedirectResponse
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'dba' => ['required', 'string', 'max:255'],
            'primary_contact_phone' => ['required', 'string', 'max:20'],
            'primary_contact_position' => ['required', 'string', 'max:255'],
    
            'address.street_number' => 'nullable|string|max:50',
            'address.street_name' => 'nullable|string|max:255',
            'address.postal_code' => 'nullable|string|max:20',
            'address.city' => 'required|string|max:255',
            'address.province' => 'nullable|exists:provinces,id',
        ]);
        
        // Split name into first and last name
        $nameParts = explode(' ', $validatedData['name'], 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';
        
        // Create User for the LP
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validatedData['email'],
            'phone' => $validatedData['primary_contact_phone'],
            'password' => Hash::make($validatedData['password']),
        ]);
        
        // Assign the "lp" role to the user
      
    
        // Fire the Registered event
        event(new Registered($user));
    
        // Create the LP
        $lp = LP::create([
            'name' => $validatedData['name'], // Full name of LP
            'user_id' => $user->id,
            'dba' => $validatedData['dba'],
            'primary_contact_email' => $validatedData['email'],
            'primary_contact_phone' => $validatedData['primary_contact_phone'],
            'primary_contact_position' => $validatedData['primary_contact_position'],
            'status' => 'requested', // Set initial status
        ]);
        
        // Store the address in the lp_addresses table using updateOrCreate
        $lp->address()->updateOrCreate(
            ['lp_id' => $lp->id], // Ensure the address is linked to the correct LP
            [
                'street_number' => $validatedData['address']['street_number'],
                'street_name' => $validatedData['address']['street_name'],
                'postal_code' => $validatedData['address']['postal_code'],
                'city' => $validatedData['address']['city'],
                'province_id' => $validatedData['address']['province'], // Store the province ID
            ]
        );
        
        // Send an invitation email
        Mail::to($validatedData['email'])->send(new LpPendingStatusMail($validatedData['name'], $validatedData['email']));
    
        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'Your account has been created. You will be informed via email once the super admin approves or rejects your registration.');

    }
    
    
  

    
}
