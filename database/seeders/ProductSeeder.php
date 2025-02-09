<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Fine Sand',
                'category' => 'Sand',
                'description' => 'Fine quality sand for construction',
                'price' => 1500.00,
                'unit' => 'cubic meter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gravel 3/4',
                'category' => 'Gravel',
                'description' => '3/4 inch gravel for concrete mixing',
                'price' => 1800.00,
                'unit' => 'cubic meter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '4" Hollow Blocks',
                'category' => 'Hollow Blocks',
                'description' => '4-inch concrete hollow blocks',
                'price' => 25.00,
                'unit' => 'piece',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cement Nail 2"',
                'category' => 'Hardware Supplies',
                'description' => '2-inch cement nails',
                'price' => 85.00,
                'unit' => 'kilo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($products);
    }
}
