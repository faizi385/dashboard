<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create user 1
        $user1 = User::create([
            'first_name' => 'faizanmoeen',
            'last_name' => 'moeen',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign the Super Admin role
        $superAdminRole = Role::where('first_name', 'super_admin_1')->first();
        if ($superAdminRole) {
            $user1->assignRole($superAdminRole);
        }

        // Optionally, create additional users
        // User::factory(9)->create(); // This will create 9 more users
    }
}
