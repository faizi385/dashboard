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
            // Retrieve all roles created by the super admin and those not created by anyone
            $roles = Role::where('created_by', $user->id)
                ->orWhereNull('created_by')
                ->get();
        } else {
            // Default case for other roles (if applicable)
            $roles = Role::all();
        }

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,NULL,id,created_by,' . auth()->id(),
            'permissions' => 'array', 
        ]);

        $role = Role::create([
            'name' => $request->name,
            'created_by' => auth()->id(), // Set the creator of the role
        ]);

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

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . ',id,created_by,' . auth()->id(),
            'permissions' => 'array', 
        ]);

        $role->update(['name' => $request->name]);

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
