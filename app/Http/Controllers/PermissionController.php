<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
{
    $permissions = Permission::where('created_by', auth()->id())->get();
    return view('super_admin.permissions.index', compact('permissions'));
}


    public function create()
    {
        return view('super_admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name|regex:/^[a-zA-Z]+$/', // Only alphabets allowed
            'description' => 'required|string|max:255|regex:/^[a-zA-Z]+$/',
        ]);
    
        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => auth()->id(), // Associate with the authenticated user
        ]);
    
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }
    
    
    public function edit(Permission $permission)
    {
        return view('super_admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'required|string|max:255', // Validate description
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description, // Update description
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
