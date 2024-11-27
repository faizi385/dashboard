<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        // Check if the user is a Retailer or LP
        if (auth()->user()->hasRole('Retailer') || auth()->user()->hasRole('LP')) {
            // Retrieve users created by the logged-in retailer or LP
            $users = User::where('created_by', auth()->id())->with('roles')->get();
        } else {
            // Retrieve all users for Super Admins
            $users = User::with('roles')->get();
        }

        return view('super_admin.users.index', compact('users'));
    }

   public function create()
{
    // Fetch roles based on the user's role
    $roles = Role::where('created_by', auth()->id())->get(['id', 'name', 'original_name']); // Include original_name for display

    $permissions = Permission::all();

    return view('super_admin.users.create', compact('roles', 'permissions'));
}

public function store(Request $request)
    {
    // Validate the incoming request
    $request->validate([
        'first_name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[a-zA-Z\s]+$/', // Only alphabets and spaces allowed
        ],
        'last_name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[a-zA-Z\s]+$/', // Only alphabets and spaces allowed
        ],
       'email' => [
            'required',
            'string',
            'email',
            'max:255',
            'unique:users,email,NULL,id,deleted_at,NULL', // Check uniqueness only for active users
            'regex:/^([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.)com$/', // Ensure email ends with .com
        ],

        'password' => [
            !isset($user) ? 'required' : 'nullable', // Password is required for new users, optional for updates
            'string',
            'min:8',
            'confirmed', // Ensure the password matches the confirmation field
        ],
        'phone' => [
            'required',
            'max:20', // Ensure the phone number does not exceed 20 characters
        ],
        'address' => 'nullable|string|max:255',
        'roles' => 'required|array', // Ensure at least one role is selected
        'permissions' => 'nullable|array',
        ], 
        [
            'first_name.regex' => 'The first name may only contain letters and spaces.',
            'last_name.regex' => 'The last name may only contain letters and spaces.',
            'email.unique' => 'This email address is already associated with another account.',
            'roles.required' => 'Please select at least one role.', // Custom message for roles validation
            'phone.regex' => 'Please enter a valid phone number format.',
        ]);
    
        // Create the user
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')), // Hash the password
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'created_by' => auth()->id(), // Log the user who created the account
        ]);

        // Assign roles if provided
        if ($request->roles) {
            $roles = Role::whereIn('original_name', $request->roles)->pluck('name')->toArray();
            $user->assignRole($roles);
        }

        // Assign permissions if provided
        if ($request->permissions) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $user->givePermissionTo($permissions);
        }

        // Redirect back with success message
        return redirect()->route('users.index')
            ->with('toast_success', 'User created successfully.');
    }



    public function edit(User $user)
    {
        // Roles are based on who created them
        $roles = auth()->user()->hasRole('Super Admin') 
            ? Role::where('created_by', auth()->id())->get() 
            : Role::where('created_by', auth()->id())->get();

        $permissions = Permission::all();
        
        return view('super_admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => [
                'required',
                'regex:/^(\+?\d{1,3}[- ]?)?\(?\d{1,4}?\)?[- ]?\d{1,4}[- ]?\d{1,4}$/', // Adjust regex for international formats
                'max:20', // Ensure the phone number does not exceed 20 characters
            ],
            'address' => 'nullable|string|max:255',
            'roles' => 'array',
            'permissions' => 'array',
            'userable_id' => 'nullable|integer', 
            'userable_type' => 'nullable|string', 
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'phone' => $request->phone,
            'address' => $request->address,
            'userable_id' => $request->input('userable_id'), 
            'userable_type' => $request->input('userable_type'), 
        ]);

        // Sync roles
        if ($request->roles) {
            $roles = Role::whereIn('id', $request->roles)->where('created_by', auth()->id())->pluck('name')->toArray();
            $user->syncRoles($roles);
        }

        // Sync permissions
        if ($request->permissions) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $user->syncPermissions($permissions);
        }

        return redirect()->route('users.index')
            ->with('toast_success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('toast_success', 'User deleted successfully.');
    }

    public function profile()
    {
        $user = Auth::user();  // Get authenticated user
        return view('profile', compact('user'));
    }

    public function settings()
    {
        $user = Auth::user();  // Get authenticated user
        return view('settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user(); // Fetch the authenticated user

        // Update the user details
        $user->update([
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
        ]);

        return redirect()->back()->with('toast_success', 'Settings updated successfully.');
    }
}
