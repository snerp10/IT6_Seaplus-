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
                'price' => 1500.00,
                'unit' => 'cubic meter',
                'stock'=> 9999,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gravel 3/4',
                'category' => 'Gravel',
                'price' => 1800.00,
                'unit' => 'cubic meter',
                'stock'=> 9999,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '4" Hollow Blocks',
                'category' => 'Hollow Blocks',
                'price' => 25.00,
                'unit' => 'piece',
                'stock'=> 9999,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cement Nail 2"',
                'category' => 'Hardware Supplies',
                'price' => 85.00,
                'unit' => 'kilo',
                'stock'=> 9999,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('products')->insert($products);
    }
}
