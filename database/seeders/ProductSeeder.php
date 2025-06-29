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
$product = Product::create([
            'name' => 'تيشيرت رجالي',
            'slug' => 'تيشيرت-رجالي',
            'description' => 'Latest model with advanced features.',
            'price' => 299.99,
            'image' => 'storage/products/c5ee969a39847f84173601fad7592a9d2665472f.png',
            'category_id' => Category::where('slug', 'clothing')->first()->id, // Ensure the category exists
            'is_offer' => true,
            'discount_percent' => 15, // 15% discount
            'offer_expires_at' => now()->addDays(7), // Offer expires in 7 days
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => true,
            'min_order_quantity' => 3,
            'rating' => 4.5,
            'is_featured' =>1,
        ]);

        Product::create([
            'name' => 'نظارة واقع افتراضي',
            'slug' => 'نظارة-واقع-افتراضي',
            'description' => 'Ergonomic office chair for comfort.',
            'price' => 89.99,
            'image' => 'storage/products/8e9558ec8a1c11e2c6fb22d5d5c5f657b5cbf8c7.png',
            'category_id' => Category::where('slug', 'electronics')->first()->id,
            'is_offer' => true,
            'discount_percent' => 20, 
            'offer_expires_at' => now()->addDays(5),
            'supplier_name'=> 'Fuzhou Green',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 1,
            'rating' => 4.8,
            'is_featured'=> 1,
        ]);
        Product::create([
            'name' => 'كرسي  مريح',
            'slug' => 'كرسي-مريح',
            'description' => 'Ergonomic office chair for comfort.',
            'price' => 89.99,
            'image' => 'storage/products/8ad8ce1e18ac5d339abc1cfd640d9cb517b36494.png',
            'category_id' => Category::where('slug', 'furniture')->first()->id,
            'is_offer' => false, 
            'supplier_name'=> 'Office Comforts',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 2,
            'rating' => 4.2,
            'is_featured' =>0,
        ]);
        Product::create([
            'name' => 'طابعة ليزر',
            'slug' => 'طابعة-ليزر',
            'description' => 'High-speed laser printer for office use.',
            'price' => 199.99,
            'image' => 'storage/products/4b86b7d17ff840be81ca85b2e885843777dcbb85.png',
            'category_id' => Category::where('slug', 'testing-supplies')->first()->id,
            'is_offer' => true,
            'discount_percent' => 10, // 10% discount
            'offer_expires_at' => now()->addDays(3),
            'supplier_name'=> 'Print Solutions',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 1,
            'rating' => 4.6,
            'is_featured' =>1,
        ]);
        Product::create([
            'name' => 'ساعة ذكية',
            'slug' => 'ساعة-ذكية',
            'description' => 'Smartwatch with health tracking features.',
            'price' => 149.99,
            'image' => 'storage/products/1_1099cd20-7237-4bdf-a180-b7126de5ef3d_1024x1024.webp',
            'category_id' => Category::where('slug', 'electronics')->first()->id,
            'is_offer' => true, 
            'discount_percent' => 10, 
            'supplier_name'=> 'Tech Gadgets',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 1,
            'rating' => 4.7,
            'is_featured' => 1,
        ]);
        Product::create([
            'name' => 'حقيبة ظهر',
            'slug' => 'حقيبة-ظهر',
            'description' => 'Durable backpack for daily use.',
            'price' => 39.99,
            'category_id' => Category::where('slug', 'clothing')->first()->id,
            'is_offer' => true,
            'discount_percent' => 25,
            'offer_expires_at' => now()->addDays(10),
            'supplier_name'=> 'Bag World',
            'supplier_confirmed'=> false,
            'min_order_quantity'=> 1,
            'rating' => 4.3,
            'images' => json_encode([
                'storage/products/1 (1).webp',
                'storage/products/2.webp',
                        ]),
            'is_featured' => 1,
        ]);
        Product::create([
            'name' => 'قلم حبر جاف',
            'slug' => 'قلم-حبر-جاف',
            'description' => 'Smooth writing ballpoint pen.',
            'price' => 1.99,
            'image' => 'storage/products/IMG-20230302-WA0016.webp',
            'category_id' => Category::where('slug', 'testing-supplies')->first()->id,
            'is_offer' => false, 
            'supplier_name'=> 'Stationery Hub',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 10,
            'rating' => 4.0,
            'is_featured' => 0,
        ]);
        Product::create([
            'name'=> 'أثاث',
            'slug'=> 'أثاث',
            'description'=> 'Modern furniture for home and office.',
            'price'=> 999.99,
            'image'=> 'storage/products/872c7362e417b45a9e5e1e13c169fb360de8fc74.png',
            'category_id'=> Category::where('slug', 'furniture')->first()->id,
            'is_offer' => false,
            'supplier_name'=> 'Furniture World',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 1,
            'rating' => 4.9,
            'is_featured' => 1,
        ]);
        Product::create([
            'name'=> 'iPhone 15 pro max',
            'slug'=> 'iphone-15-pro-max',
            'description'=> 'Latest iPhone with advanced features.',
            'price'=> 1099.99,
            'image'=> 'storage/products/28442cd2d8b4be75c0b3aa5caada440028925cd0.png',
            'category_id'=> Category::where('slug', 'electronics')->first()->id,
            'is_offer' => true,
            'discount_percent' => 5, // 5% discount
            'offer_expires_at' => now()->addDays(14), // Offer expires in 14 days
            'supplier_name'=> 'Apple Inc.',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 1,
            'rating' => 4.8,
            'is_featured' => 1,
        ]);
        Product::create([
            'name' => 'تيشيرت رجالي',
            'slug' => '2-تيشيرت-رجالي',
            'description' => 'Latest model with advanced features.',
            'price' => 299.99,
            'image' => 'storage/products/c7cc5df6a3a3ad5b9e08d091347e0b2f8b61e1e4.png',
            'category_id' => Category::where('slug', 'clothing')->first()->id, // Ensure the category exists
            'is_offer' => true,
            'discount_percent' => 15, // 15% discount
            'offer_expires_at' => now()->addDays(7), // Offer expires in 7 days
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => true,   
            'min_order_quantity' => 3,
            'rating' => 4.5,
            'is_featured' => 1,
        ]);
    }
}