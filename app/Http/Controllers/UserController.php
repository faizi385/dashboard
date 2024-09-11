<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'roles' => 'array',
            'permissions' => 'array',
        ]);
    
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
        ]);
    
        if ($request->roles) {
            $roles = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
            $user->assignRole($roles);
        }
    
        if ($request->permissions) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $user->givePermissionTo($permissions);
        }
    
        return redirect()->route('users.index')
        ->with('toast_success', 'User created successfully.');
    }
    
    

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'roles' => 'array',
            'permissions' => 'array',
        ]);
    
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
    
        if ($request->roles) {
            $roles = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
            $user->syncRoles($roles);
        }
    
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
}
