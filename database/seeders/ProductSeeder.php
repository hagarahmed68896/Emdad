<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\SubCategory;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tShirtSubCategory = SubCategory::where('slug', 't-shirt')->firstOrFail();
        $furnitureSubCategory = SubCategory::where('slug', 'furniture')->firstOrFail();
        $watchesSubCategory = SubCategory::where('slug', 'watches')->firstOrFail();
        $bagsSubCategory = SubCategory::where('slug', 'bags')->firstOrFail();
        $phonesSubCategory = SubCategory::where('slug', 'phones')->firstOrFail();

$product = Product::create([
            'name' => 'تيشيرت رجالي',
            'slug' => 'تيشيرت-رجالي',
            'description' => 'تيشرت بطبعة',
            'price' => 299.99,
            'image' => 'storage/products/c5ee969a39847f84173601fad7592a9d2665472f.png',
            'sub_category_id' => SubCategory::where('slug', 't-shirt')->first()->id, // Ensure the category exists
            'is_offer' => true,
            'discount_percent' => 15, // 15% discount
            'offer_expires_at' => now()->addDays(7), // Offer expires in 7 days
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => true,
            'min_order_quantity' => 3,
            'rating' => 4.0,
            'is_featured' =>1,
            'color' => ['Orange'],
            'size' => ['L'],
            'material' => 'قطن',
            'gender' => 'رجالي',
            
            
        ]);


        Product::create([
            'name' => 'كرسي  مريح',
            'slug' => 'كرسي-مريح',
            'description' => 'Ergonomic office chair for comfort.',
            'price' => 89.99,
            'image' => 'storage/products/8ad8ce1e18ac5d339abc1cfd640d9cb517b36494.png',
            'sub_category_id' => SubCategory::where('slug', 'furniture')->first()->id,
            'is_offer' => false, 
            'supplier_name'=> 'Office Comforts',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 2,
            'rating' => 4.2,
            'is_featured' =>0,
        ]);
        Product::create([
            'name' => 'ساعة ذكية',
            'slug' => 'ساعة-ذكية',
            'description' => 'Smartwatch with health tracking features.',
            'price' => 149.99,
            'image' => 'storage/products/1_1099cd20-7237-4bdf-a180-b7126de5ef3d_1024x1024.webp',
            'sub_category_id' => SubCategory::where('slug', 'watches')->first()->id,
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
            'sub_category_id' => SubCategory::where('slug', 'bags')->first()->id,
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
            'name'=> 'أثاث',
            'slug'=> 'أثاث',
            'description'=> 'Modern furniture for home and office.',
            'price'=> 999.99,
            'image'=> 'storage/products/872c7362e417b45a9e5e1e13c169fb360de8fc74.png',
            'sub_category_id'=> SubCategory::where('slug', 'furniture')->first()->id,
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
            'sub_category_id'=> SubCategory::where('slug', 'phones')->first()->id,
            'is_offer' => true,
            'discount_percent' => 5, 
            'offer_expires_at' => now()->addDays(14), // Offer expires in 14 days
            'supplier_name'=> 'Apple Inc.',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 1,
            'rating' => 4.8,
            'is_featured' => 1,
        ]);
        Product::create([
            'name' => 'تيشيرت ',
            'slug' => 'تيشيرت',
            'description' => 'تيشرت بولو',
            'price' => 299.99,
            'image' => 'storage/products/c7cc5df6a3a3ad5b9e08d091347e0b2f8b61e1e4.png',
            'sub_category_id' => SubCategory::where('slug', 't-shirt')->first()->id, 
            'is_offer' => true,
            'discount_percent' => 15, 
            'offer_expires_at' => now()->addDays(7), 
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => true,   
            'min_order_quantity' => 3,
            'rating' => 4.5,
            'is_featured' => 1,
            'color' => ['Black', 'White', 'Gray'], 
            'size' => ['XL', 'L', 'M'], 
            'material' => 'بوليستر',
            'gender' => 'رجالي',
        ]);
        Product::create([
            'name' => 'نظارة شمس',
            'slug' => 'نظارة-شمس',
            'description' => 'sunglasses',
            'price' => 100,
            'image' => 'storage/products/aafd3d80765ef1803d24002179a479f672501611.png',
            'sub_category_id' => SubCategory::where('slug', 'Sunglasses')->first()->id,
            'is_offer' => true, 
            'supplier_name'=> 'Fuzhou Green',
            'supplier_confirmed'=> true,
            'min_order_quantity'=> 2,
            'rating' => 4.8,
            'is_featured' =>1,
        ]);
        Product::create([
            'name' => 'تيشيرت رياضي',
            'slug' => 'تيشيرت-رياضي',
            'description' => 'تيشرت رياضي',
            'price' => 155.0,
            'sub_category_id' => SubCategory::where('slug', 't-shirt')->first()->id, // Ensure the category exists
            'is_offer' => true,
            'discount_percent' => 10, 
            'offer_expires_at' => now()->addDays(7),
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => true,
            'min_order_quantity' => 3,
            'rating' => 4.0,
            'is_featured' =>1,
             'images' => json_encode([
                'storage/products/a83454ae419fb750af84f07c1f35acb42e3d3ff5.png',
                'storage/products/c9e653c80313c6ada3c24262f4d7cd4f205b36fb.png',
                        ]),
            'color' => ['Blue','Gray','Caffe'],
            'size' => ['M'],
            'material' => 'قطن',
            'gender' => 'نسائي',
            'estimated_delivery_date' => '2023-10-15' 
        ]);
            Product::create([
            'name' => 'تيشيرت سادة',
            'slug' => 'تيشيرت-سادة',
            'description' => 'تيشرت سادة',
            'price' => 252.0,
            'sub_category_id' => SubCategory::where('slug', 't-shirt')->first()->id, // Ensure the category exists
            'is_offer' => true,
            'discount_percent' => 5, 
            'offer_expires_at' => now()->addDays(7),
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => false,
            'min_order_quantity' => 3,
            'rating' => 4.0,
            'is_featured' =>1,
            'images' => json_encode([
                'storage/products/c7cc5df6a3a3ad5b9e08d091347e0b2f8b61e1e4.png',
                'storage/products/340bb1d4c43f0944e9e74745b6d6eb54c7252b78.png',
                        ]),
            'color' => ['Black','White','Nike Red'],
            'size' => ['XL'],
            'material' => 'قطن',
            'gender' => 'رجالي',
            'estimated_delivery_date' => '2023-10-15' 
        ]);
    }
    
}