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
            ['name' => 'view provinces', 'description' => 'View the list of provinces'],
            ['name' => 'view logs', 'description' => 'View logs'],
            ['name' => 'view retailer dashboard', 'description' => 'View the retailer dashboard'],
            ['name' => 'view lp dashboard', 'description' => 'View the LP dashboard'],
            ['name' => 'view deals', 'description' => 'View deals'],
            ['name' => 'view supplier', 'description' => 'View suppliers'],
            ['name' => 'view manage info', 'description' => 'Manage supplier information'],
            ['name' => 'view distributor', 'description' => 'View distributors'],
            ['name' => 'view carveouts', 'description' => 'View carveouts'],
            ['name' => 'view products', 'description' => 'View products'],
            ['name' => 'view reports', 'description' => 'View the list of reports'],
            ['name' => 'view statement', 'description' => 'View statements'],
        ];

        // Loop through each permission and create if it doesn't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}

// To run the seeder, use the following command:
// php artisan db:seed --class=PermissionsTableSeeder
