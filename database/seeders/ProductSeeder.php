<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create(['category_id' => 1, 'name' => 'iPhone 12', 'price' => 8000, 'quantity' => 10]);
        Product::create(['category_id' => 1, 'name' => 'iPhone 13', 'price' => 1000, 'quantity' => 1]);
        Product::create(['category_id' => 1, 'name' => 'iPhone 14', 'price' => 3000, 'quantity' => 3]);

        Product::create(['category_id' => 2, 'name' => 'Shirt', 'price' => 999, 'quantity' => 13]);
        Product::create(['category_id' => 2, 'name' => 'T-Shirt', 'price' => 120, 'quantity' => 50]);
        Product::create(['category_id' => 2, 'name' => 'Jeens', 'price' => 1320, 'quantity' => 100]);


        Product::create(['category_id' => 3, 'name' => '1984', 'price' => 123, 'quantity' => 12]);
        Product::create(['category_id' => 3, 'name' => 'Мир', 'price' => 321, 'quantity' => 23]);
        Product::create(['category_id' => 3, 'name' => 'Барнаул', 'price' => 1231, 'quantity' => 7]);
    }
}
