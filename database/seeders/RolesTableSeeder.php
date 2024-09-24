<?php

// database/seeders/RolesTableSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        // Remove all existing roles
        Role::query()->delete();

        // Create new roles with the original_name
        Role::create([
            'name' => 'retailer_1', // Example of concatenated name
            'original_name' => 'Retailer', // Store the original name
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'lp_1', // Example of concatenated name
            'original_name' => 'LP', // Store the original name
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'super_admin_1', // Example of concatenated name
            'original_name' => 'Super Admin', // Store the original name
            'guard_name' => 'web',
        ]);
    }
}
