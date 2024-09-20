<?php

namespace App\Http\Controllers;

use App\Models\Lp;
use App\Models\User;
use App\Mail\LpFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LpController extends Controller
{
    public function dashboard()
    {
        // Return the view for the LP dashboard
        return view('lp.dashboard'); // Make sure you have a 'dashboard.blade.php' in the 'resources/views/lp' directory
    }
    
    public function index()
    {
        // Retrieve all LPs (or filter based on permissions if needed)
        $lps = Lp::all();
        return view('lp.index', compact('lps'));
    }

    public function show($id)
    {
        $lp = Lp::with('address')->findOrFail($id);
        return view('lp.show', compact('lp'));
    }

    public function create()
    {
        return view('lp.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'dba' => 'nullable|string|max:255',
            'primary_contact_email' => 'required|string|email|max:255',
            'primary_contact_phone' => 'nullable|string|max:20',
            'primary_contact_position' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8', // Ensure password is handled
        ]);
    
        // Create a new LP record and assign the user_id
        $lp = Lp::create(array_merge(
            $validatedData,
            ['user_id' => Auth::id()] // Assign the current authenticated user's ID
        ));
    
        // Send the email with the form link
        Mail::to($validatedData['primary_contact_email'])->send(new LpFormMail($lp));
    
        // Redirect to the LP index page with a success message
        return redirect()->route('lp.create')->with('success', 'LP created and email sent!');
    }
    

    public function completeForm($id)
    {
        $lp = Lp::findOrFail($id);
        return view('lp.complete_form', compact('lp'));
    }

    public function submitCompleteForm(Request $request)
{
    $validatedData = $request->validate([
        'lp_id' => 'required|exists:lps,id',
        'name' => 'required|string|max:255',
        'dba' => 'nullable|string|max:255',
        'primary_contact_email' => 'required|string|email|max:255',
        'primary_contact_phone' => 'nullable|string|max:20',
        'primary_contact_position' => 'nullable|string|max:255',
        'password' => 'required|string|confirmed|min:8',
        'address.street_number' => 'nullable|string|max:50',
        'address.street_name' => 'nullable|string|max:255',
        'address.postal_code' => 'nullable|string|max:20',
        'address.city' => 'required|string|max:255',
    ]);

    $lp = Lp::findOrFail($request->lp_id);

    // Update LP details, including user_id if necessary
    $lp->update([
        'name' => $validatedData['name'],
        'dba' => $validatedData['dba'],
        'primary_contact_email' => $validatedData['primary_contact_email'],
        'primary_contact_phone' => $validatedData['primary_contact_phone'],
        'primary_contact_position' => $validatedData['primary_contact_position'],
        'user_id' => Auth::id(),  // Ensure the user_id is set here
    ]);

    // Create or update the User record
    $user = User::updateOrCreate(
        ['email' => $validatedData['primary_contact_email']], // Unique identifier
        [
            'name' => $validatedData['name'],
            'phone' => $validatedData['primary_contact_phone'],
            'password' => Hash::make($validatedData['password']),
        ]
    );

    // Assign role to the user
    $user->assignRole('lp');

    // Log in the user
    Auth::login($user);

    // Create or update the address
    $lp->address()->updateOrCreate(
        ['lp_id' => $lp->id],
        [
            'street_number' => $validatedData['address']['street_number'],
            'street_name' => $validatedData['address']['street_name'],
            'postal_code' => $validatedData['address']['postal_code'],
            'city' => $validatedData['address']['city'],
        ]
    );

    // Redirect with success message
    return redirect()->route('lp.dashboard')->with('success', 'LP details updated and user account created. Welcome to your dashboard!');
}


    public function edit(Lp $lp)
    {
        return view('lp.edit', compact('lp'));
    }

    public function update(Request $request, Lp $lp)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'dba' => 'nullable|string|max:255',
            'primary_contact_email' => 'required|email|max:255',
            'primary_contact_phone' => 'nullable|string|max:20',
            'primary_contact_position' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8', // Ensure password validation if provided
        ]);

        // Update the LP record with the validated data
        $lp->update([
            'name' => $validatedData['name'],
            'dba' => $validatedData['dba'],
            'primary_contact_email' => $validatedData['primary_contact_email'],
            'primary_contact_phone' => $validatedData['primary_contact_phone'] ?? null,  // Handle null value
            'primary_contact_position' => $validatedData['primary_contact_position'] ?? null,  // Handle null value
        ]);

        // Update the user password if provided
        if (!empty($validatedData['password'])) {
            $user = User::where('email', $lp->primary_contact_email)->first();
            if ($user) {
                $user->password = Hash::make($validatedData['password']);
                $user->save();
            }
        }

        // Redirect to the LP index page with a success message
        return redirect()->route('lp.index')
            ->with('toast_success', 'LP updated successfully.');
    }

    public function destroy(Lp $lp)
    {
        $lp->delete();
        return redirect()->route('lp.index')
            ->with('toast_success', 'LP deleted successfully.');
    }
}
