<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Filter roles based on user role
        if ($user->hasRole(['Retailer', 'LP'])) {
            // Only roles created by the logged-in Retailer or LP
            $roles = Role::where('created_by', $user->id)->get();
        } elseif ($user->hasRole('Super Admin')) {
            // Super Admin can view all roles created by them
            $roles = Role::where('created_by', $user->id)->get();
        } else {
            $roles = Role::all();
        }

        return view('super_admin.roles.index', compact('roles'));
    }

    public function create()
    {
        // Get the logged-in user's ID
        $userId = Auth::id();

        // Get only the permissions created by the logged-in user
//        $permissions = Permission::where('created_by', $userId)->get();
        $permissions = Permission::get();

        // Pass the filtered permissions to the view
        return view('super_admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'permissions' => 'nullable|array|min:1', // Validate that permissions are an array and must have at least one item
            'permissions.*' => 'exists:permissions,id', // Ensure all permissions exist in the permissions table
        ]);

        // Check if permissions are provided before role creation
        if (empty($request->permissions)) {
            return redirect()->back()->with('error', 'Please select at least one permission.'); // Return error if no permissions
        }

        // Create a unique role name for the creator
        $roleName = $request->name . '_' . auth()->id();
        $existingRole = Role::where('name', $roleName)->where('created_by', auth()->id())->first();

        if ($existingRole) {
            return redirect()->back()->withErrors(['name' => 'A role with this name already exists for the current user.']);
        }

        $origin_name = Auth::user()->userRole[0]->origin_name;

        // Create the new role
        $role = Role::create([
            'name' => $roleName,
            'original_name' => $request->input('name'),
            'origin_name' => $origin_name,
            'created_by' => auth()->id(),
        ]);

        // Sync permissions if provided
        $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
        $role->syncPermissions($validPermissions);

        return redirect()->route('roles.index')->with('toast_success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        // Get the logged-in user's ID
        $userId = Auth::id();

        // Get only the permissions created by the logged-in user
        $permissions = Permission::where('created_by', $userId)->get();

        // Get the IDs of the permissions assigned to the role
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        // Pass the filtered permissions and role data to the view
        return view('super_admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',
                'unique:roles,name,' . $role->id . ',id,created_by,' . auth()->id(),
            ],
            'permissions' => 'nullable|array', // Ensure permissions is an array if provided
            'permissions.*' => 'exists:permissions,id', // Ensure each permission exists in the database
        ]);

        // Check if no permissions are selected
        if (!$request->has('permissions') || empty($request->permissions)) {
            return redirect()->back()->with('error', 'Please select at least one permission.'); // Show error if no permissions are selected
        }

        // Update the role with validated name
        $role->update([
            'original_name' => $request->name,
            'name' => $request->name . '_' . auth()->id(),
        ]);

        // Retrieve only existing permissions that match the current guard
        $validPermissions = Permission::whereIn('id', $request->permissions)
            ->where('guard_name', $role->guard_name) // Ensure permissions are valid for this guard
            ->pluck('id')
            ->toArray();

        // Sync the valid permissions
        $role->syncPermissions($validPermissions);

        // Redirect with success message
        return redirect()->route('roles.index')->with('toast_success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('toast_success', 'Role deleted successfully.');
    }
}
