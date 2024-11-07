<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        // Define permissions with their descriptions
        $permissions = [
            ['name' => 'view users', 'description' => 'View the list of users'],
            ['name' => 'create users', 'description' => 'Create new users'],
            ['name' => 'edit users', 'description' => 'Edit existing users'],
            ['name' => 'delete users', 'description' => 'Delete users'],

            ['name' => 'view roles', 'description' => 'View the list of roles'],
            ['name' => 'create roles', 'description' => 'Create new roles'],
            ['name' => 'edit roles', 'description' => 'Edit existing roles'],
            ['name' => 'delete roles', 'description' => 'Delete roles'],

            ['name' => 'view permissions', 'description' => 'View the list of permissions'],
            ['name' => 'create permissions', 'description' => 'Create new permissions'],
            ['name' => 'edit permissions', 'description' => 'Edit existing permissions'],
            ['name' => 'delete permissions', 'description' => 'Delete permissions'],

            ['name' => 'view retailer dashboard', 'description' => 'View the retailer dashboard'],
            ['name' => 'view lp dashboard', 'description' => 'View the LP dashboard'],

            // Permissions for reports
            ['name' => 'view reports', 'description' => 'View the list of reports'],
            ['name' => 'create reports', 'description' => 'Create new reports'],
            ['name' => 'edit reports', 'description' => 'Edit existing reports'],
            ['name' => 'delete reports', 'description' => 'Delete reports'],

            // Permissions for managing provinces
            ['name' => 'view provinces', 'description' => 'View the list of provinces'],
            ['name' => 'create provinces', 'description' => 'Create new provinces'],
            ['name' => 'edit provinces', 'description' => 'Edit existing provinces'],
            ['name' => 'delete provinces', 'description' => 'Delete provinces'],

            // Permissions for logs
            ['name' => 'view logs', 'description' => 'View logs'],
            ['name' => 'delete logs', 'description' => 'Delete logs'],
        ];

        // Loop through each permission and check if it exists before creating
        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission['name'])->exists()) {
                Permission::create($permission);
            }
        }
    }
}

// To run the seeder, use the following command:
// php artisan db:seed --class=PermissionsTableSeeder
