<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        // User management permissions
        Permission::create(['name' => 'view users', 'description' => 'View the list of users']);
        Permission::create(['name' => 'create users', 'description' => 'Create new users']);
        Permission::create(['name' => 'edit users', 'description' => 'Edit existing users']);
        Permission::create(['name' => 'delete users', 'description' => 'Delete users']);

        // Role management permissions
        Permission::create(['name' => 'view roles', 'description' => 'View the list of roles']);
        Permission::create(['name' => 'create roles', 'description' => 'Create new roles']);
        Permission::create(['name' => 'edit roles', 'description' => 'Edit existing roles']);
        Permission::create(['name' => 'delete roles', 'description' => 'Delete roles']);

        // Permission management
        Permission::create(['name' => 'view permissions', 'description' => 'View the list of permissions']);
        Permission::create(['name' => 'create permissions', 'description' => 'Create new permissions']);
        Permission::create(['name' => 'edit permissions', 'description' => 'Edit existing permissions']);
        Permission::create(['name' => 'delete permissions', 'description' => 'Delete permissions']);

        // Dashboard permissions for Retailer and LP
        Permission::create(['name' => 'view retailer dashboard', 'description' => 'View the retailer dashboard']);
        Permission::create(['name' => 'view lp dashboard', 'description' => 'View the LP dashboard']);
    }
}

// php artisan db:seed --class=PermissionsTableSeeder
