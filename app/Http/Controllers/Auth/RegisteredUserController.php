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
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', // Ensure valid email format
                'ends_with:.com' // Ensure email ends with .com
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'dba' => ['required', 'string', 'max:255'],
            'primary_contact_phone' => [
                    'required',
                    'string',
                    'max:20',
                    'min:7',
                    'regex:/^\d{7,}$/',
                ],

            'primary_contact_position' => ['required', 'string', 'max:255'],
    
          'address.address' => ['required', 'string', 'max:255'], // Ensure `address` is a single string

            'address.postal_code' => 'nullable|integer',
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
        // Assuming there's a relationship between LP and User
        if ($user) {
            // Find the LP role by its original_name
            $role = Role::where('original_name', 'LP')->first();
            if ($role) {
                // Assign the role to the user
                $user->assignRole($role->name);
            } else {
                return redirect()->back()->with('error', 'Role not found.');
            }
        }
    
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
                'address' => $validatedData['address']['address'],
                'postal_code' => $validatedData['address']['postal_code'],
                'city' => $validatedData['address']['city'],
                'province_id' => $validatedData['address']['province'], // Store the province ID
            ]
        );
        
        // Send an invitation email
        Mail::to($validatedData['email'])->send(new LpPendingStatusMail($validatedData['name'], $validatedData['email']));
    
        // Redirect to the login page with a success message
        return redirect()->route('account.created')->with('success', 'Your account has been created. You will be informed via email once the super admin approves or rejects your registration.');

    }
    
    
  

    
}
