<?php

namespace App\Http\Controllers\Auth;

use App\Models\LP;
use App\Models\LpAddress;
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
        $validatedData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
                'ends_with:.com', 
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
            'address.address' => ['required', 'string', 'max:255'], 
            'address.postal_code' => ['nullable', 'digits_between:4,10'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.province' => ['nullable', 'exists:provinces,id'],
        ]);
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['primary_contact_phone'],
            'password' => Hash::make($validatedData['password']),
        ]);
        
        if ($user) {
            $role = Role::where('original_name', 'LP')->first();
            if ($role) {
                $user->assignRole($role->name);
            } else {
                return redirect()->back()->with('error', 'Role not found.');
            }
        }
    
        event(new Registered($user));

        $lpname = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        $lp = LP::create([
            'name' => $lpname,
            'user_id' => $user->id,
            'dba' => $validatedData['dba'],
            'primary_contact_email' => $validatedData['email'],
            'primary_contact_phone' => $validatedData['primary_contact_phone'],
            'primary_contact_position' => $validatedData['primary_contact_position'],
            'status' => 'requested', 
        ]);
        LpAddress::updateOrCreate(
    [
                'lp_id' => $lp->id
            ],
            [
                'address' => $validatedData['address']['address'],
                'postal_code' => $validatedData['address']['postal_code'],
                'city' => $validatedData['address']['city'],
                'province_id' => $validatedData['address']['province'], 
            ]
        );
        
        Mail::to($validatedData['email'])->send(new LpPendingStatusMail($lpname, $validatedData['email']));
    
        return redirect()->route('account.created')->with('success', 'Your account has been created. You will be informed via email once the super admin approves or rejects your registration.');

    }
    
    
  

    
}
