<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all(); // Fetch all permissions to display in the form
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array', // Validate permissions input
        ]);

        $role = Role::create(['name' => $request->name]);

        // Ensure permissions are valid before assigning
        if ($request->has('permissions')) {
            $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
            $role->syncPermissions($validPermissions);
        }

        return redirect()->route('roles.index')->with('toast_success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all(); // Fetch all permissions for editing
        $rolePermissions = $role->permissions->pluck('id')->toArray(); // Get current role's permissions

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array', // Validate permissions input
        ]);

        $role->update(['name' => $request->name]);

        // Ensure permissions are valid before assigning
        if ($request->has('permissions')) {
            $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
            $role->syncPermissions($validPermissions);
        } else {
            $role->syncPermissions([]); // Remove all permissions if none selected
        }

        return redirect()->route('roles.index')->with('toast_success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('toast_success','Role deleted successfully.');
    }
}
