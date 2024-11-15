<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
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
        // Get all permissions
        $permissions = Permission::all();
        return view('super_admin.roles.create', compact('permissions'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'permissions' => 'nullable|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        // Create a unique role name for the creator
        $roleName = $request->name . '_' . auth()->id();
        $existingRole = Role::where('name', $roleName)->where('created_by', auth()->id())->first();
    
        if ($existingRole) {
            return redirect()->back()->withErrors(['name' => 'A role with this name already exists for the current user.']);
        }
    
        // Create the new role
        $role = Role::create([
            'name' => $roleName,
            'original_name' => $request->input('name'),
            'created_by' => auth()->id(),
        ]);
    
        // Check if permissions are provided
        if (empty($request->permissions)) {
            return redirect()->back()->with('error', 'Please select at least one permission.');
        }
    
        // Sync permissions if provided
        $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
        $role->syncPermissions($validPermissions);
        // if (!empty($request->permissions)) {
        //     $role->syncPermissions($request->permissions);
        // }
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
                'regex:/^[a-zA-Z\s]+$/',
                'unique:roles,name,' . $role->id . ',id,created_by,' . auth()->id(),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
    
        // Update role with validated name
        $role->update([
            'original_name' => $request->name,
            'name' => $request->name . '_' . auth()->id(),
        ]);
    
        // Check if permissions are provided
        if (!$request->has('permissions') || empty($request->permissions)) {
            return redirect()->back()->with('error', 'Please select at least one permission.');
        }
    
        // Retrieve only existing permissions that match the current guard
        $validPermissions = Permission::whereIn('id', $request->permissions)
            ->where('guard_name', $role->guard_name)
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
