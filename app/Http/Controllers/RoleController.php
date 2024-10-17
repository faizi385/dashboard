<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();
    
        // Check the user's role and retrieve roles accordingly
        if ($user->hasRole('Retailer') || $user->hasRole('LP')) {
            // Retrieve roles created by the logged-in retailer or LP
            $roles = Role::where('created_by', $user->id)->get();
        } elseif ($user->hasRole('Super Admin')) {
            // Retrieve roles created by the super admin only
            $roles = Role::where('created_by', $user->id)->get(); 
        } else {
            // Default case for other roles (if applicable)
            $roles = Role::all();
        }
    
        return view('super_admin.roles.index', compact('roles'));
    }
    
    public function create()
    {
        $permissions = Permission::all();
        return view('super_admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'permissions' => 'nullable|array|min:1', // Ensure at least one permission is selected
            'permissions.*' => 'exists:permissions,id', // Ensure each permission ID exists in the database
        ]);
    
        // Concatenate the user ID with the role name
        $roleName = $request->name . '_' . auth()->id();
    
        // Check if a role with the same name exists for the current user
        $existingRole = Role::where('name', $roleName)
            ->where('created_by', auth()->id())
            ->first();
    
        if ($existingRole) {
            return redirect()->back()->withErrors(['name' => 'A role with this name already exists for the current user.']);
        }
    
        // Create the new role with the concatenated name
        $role = Role::create([
            'name' => $roleName, // For unique naming
            'original_name' => $request->input('name'), // Base role name
            'created_by' => auth()->id(), // Track the creator
        ]);
    
        // Sync permissions if provided
        if ($request->has('permissions')) {
            $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
            $role->syncPermissions($validPermissions);
        }
    
        return redirect()->route('roles.index')->with('toast_success', 'Role created successfully.');
    }
    
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('super_admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }   

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[a-zA-Z\s]+$/', // Ensure no numeric values
                'unique:roles,name,' . $role->id . ',id,created_by,' . auth()->id(),
            ],
            'permissions' => 'array', 
        ]);

        $role->update(['original_name' => $request->name]);

        if ($request->has('permissions')) {
            $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
            $role->syncPermissions($validPermissions);
        } else {
            $role->syncPermissions([]); 
        }

        return redirect()->route('roles.index')->with('toast_success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('toast_success','Role deleted successfully.');
    }
}
