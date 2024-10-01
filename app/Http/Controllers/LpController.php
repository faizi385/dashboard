<?php
namespace App\Http\Controllers;

use App\Models\Lp;
use App\Models\Role;
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
        return view('lp.dashboard'); // LP dashboard view
    }
    
    public function index()
    {
        $lps = Lp::all();
        return view('lp.index', compact('lps'));
    }

    public function show($id)
    {
        $lps = Lp::all();
        $lp = Lp::with('address')->findOrFail($id);
        return view('lp.show', compact('lp',"lps"));
    }

    public function create()
    {
        return view('lp.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/'], // Disallow numeric characters
            'dba' => 'required|string|max:255',
            'primary_contact_email' => 'required|string|email|max:255|unique:users,email',
            'primary_contact_phone' => 'required|string|max:20',
            'primary_contact_position' => ['required', 'string', 'max:255', 'regex:/^[^\d]+$/'], // Disallow numeric characters
            'password' => 'nullable|string|min:8',
        ]);
    
        // Create User for the LP
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['primary_contact_email'],
            'phone' => $validatedData['primary_contact_phone'],
            'password' => Hash::make($validatedData['password'] ?? 'defaultPassword'), 
        ]);
    
        // Create LP record and associate with the newly created user
        $lp = Lp::create(array_merge(
            $validatedData,
            ['user_id' => $user->id]
        ));
    
        // Send email notification
        Mail::to($validatedData['primary_contact_email'])->send(new LpFormMail($lp));
    
        return redirect()->route('lp.create')->with('success', 'LP created and email sent!');
    }
    
    
    public function completeForm($id)
    {
        $lp = Lp::findOrFail($id);
        return view('lp.complete_form', compact('lp'));
    }

    public function submitCompleteForm(Request $request)
    {
        // Validate the request data
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
    
        // Find the LP based on the provided ID
        $lp = Lp::findOrFail($request->lp_id);
    
        // Create or update the User record for the LP
        $user = User::updateOrCreate(
            ['email' => $validatedData['primary_contact_email']], // Unique identifier
            [
                'name' => $validatedData['name'],
                'phone' => $validatedData['primary_contact_phone'],
                'password' => Hash::make($validatedData['password']),
            ]
        );
    
        // Fetch the role by original name (Ensure the role exists)
        $role = Role::where('original_name', 'LP')->first(); // Adjust 'LP' if necessary
        if ($role) {
            // Assign role to the user
            $user->assignRole($role->name);
        } else {
            return redirect()->back()->with('error', 'Role not found.');
        }
    
        // Update LP details with the correct user_id
        $lp->update([
            'name' => $validatedData['name'],
            'dba' => $validatedData['dba'],
            'primary_contact_email' => $validatedData['primary_contact_email'],
            'primary_contact_phone' => $validatedData['primary_contact_phone'],
            'primary_contact_position' => $validatedData['primary_contact_position'],
            'user_id' => $user->id,  // Set the user_id to the newly created/updated user
        ]);
    
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
    
        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'Your account has been created. Please log in to continue.');
    }
    
    

    public function edit(Lp $lp)
    {
        return view('lp.edit', compact('lp'));
    }

    public function update(Request $request, Lp $lp)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'dba' => 'required|string|max:255',
            'primary_contact_email' => 'required|email|max:255',
            'primary_contact_phone' => 'required|string|max:20',
            'primary_contact_position' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        // Update LP record
        $lp->update([
            'name' => $validatedData['name'],
            'dba' => $validatedData['dba'],
            'primary_contact_email' => $validatedData['primary_contact_email'],
            'primary_contact_phone' => $validatedData['primary_contact_phone'] ?? null,
            'primary_contact_position' => $validatedData['primary_contact_position'] ?? null,
        ]);

        // Update user password if provided
        if (!empty($validatedData['password'])) {
            $user = User::where('email', $lp->primary_contact_email)->first();
            if ($user) {
                $user->password = Hash::make($validatedData['password']);
                $user->save();
            }
        }

        return redirect()->route('lp.index')->with('toast_success', 'LP updated successfully.');
    }

    public function destroy(Lp $lp)
    {
        $lp->delete();
        return redirect()->route('lp.index')->with('toast_success', 'LP deleted successfully.');
    }
}
