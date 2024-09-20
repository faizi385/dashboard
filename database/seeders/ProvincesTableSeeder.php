<?php

// database/seeders/ProvincesTableSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('provinces')->insert([
            [
                'name' => 'Province 1',
                'slug' => 'province-1',
                'timezone_1' => 'UTC+1',
                'timezone_2' => 'UTC+2',
                'tax_value' => 5.00,
                'status' => 1, // 1 for active
            ],
            [
                'name' => 'Province 2',
                'slug' => 'province-2',
                'timezone_1' => 'UTC+3',
                'timezone_2' => 'UTC+4',
                'tax_value' => 7.00,
                'status' => 0, // 0 for inactive
            ],
            [
                'name' => 'Province 3',
                'slug' => 'province-3',
                'timezone_1' => 'UTC-1',
                'timezone_2' => 'UTC-2',
                'tax_value' => 10.00,
                'status' => 1, // 1 for active
            ],
            [
                'name' => 'Alberta',
                'slug' => 'AB',
                'timezone_1' => 'MDT',
                'timezone_2' => 'MDT_2',
                'tax_value' => 5.00,
                'status' => 1, // 1 for active
            ],
            [
                'name' => 'Ontario',
                'slug' => 'ON',
                'timezone_1' => 'EDT',
                'timezone_2' => 'CDT',
                'tax_value' => 5.00,
                'status' => 1, // 1 for active
            ],
            [
                'name' => 'Manitoba',
                'slug' => 'MB',
                'timezone_1' => 'CDT',
                'timezone_2' => '',
                'tax_value' => 5.00,
                'status' => 1, // 1 for active
            ],
            [
                'name' => 'Saskatchewan',
                'slug' => 'SK',
                'timezone_1' => 'CDT',
                'timezone_2' => '',
                'tax_value' => 5.00,
                'status' => 1, // 1 for active
            ],
            [
                'name' => 'British Columbia',
                'slug' => 'BC',
                'timezone_1' => 'PDT',
                'timezone_2' => 'PDT',
                'tax_value' => 5.00,
                'status' => 1, // 1 for active
            ],
        ]);
    }
}
