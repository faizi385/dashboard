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

        // Create new roles
        Role::create(['name' => 'Retailer']);
        Role::create(['name' => 'LP']);
        Role::create(['name' => 'Super Admin']);
    }
}
