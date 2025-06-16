<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category; // Ensure Category is imported if you're checking for it

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Optional: Ensure at least one category exists if your products rely on it
        if (Category::count() === 0) {
            Category::factory()->count(5)->create(); // Create some dummy categories
        }

        // Create 50 dummy products using the factory
        Product::factory()->count(50)->create();
    }
}