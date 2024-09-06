<?php
// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions if they don't exist
        $permissions = ['view dashboard', 'manage users'];
        foreach ($permissions as $permissionName) {
            if (!Permission::where('name', $permissionName)->exists()) {
                Permission::create(['name' => $permissionName]);
            }
        }

        // Create roles and assign permissions if they don't exist
        if (!Role::where('name', 'super admin')->exists()) {
            $role = Role::create(['name' => 'super admin']);
            $role->givePermissionTo($permissions);
        }

        // Assign role to user (e.g., user with ID 1) if the user exists
        $user = User::find(1);
        if ($user) {
            $user->assignRole('super admin');
        } else {
            $this->command->info('User with ID 1 does not exist.');
        }
    }
}
