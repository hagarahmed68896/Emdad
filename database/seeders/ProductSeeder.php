<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Support\Str; // For generating slugs

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure all necessary subcategories exist
        $tShirtSubCategory = SubCategory::where('slug', 't-shirt')->firstOrFail();
        $furnitureSubCategory = SubCategory::where('slug', 'furniture')->firstOrFail();
        $watchesSubCategory = SubCategory::where('slug', 'watches')->firstOrFail();
        $bagsSubCategory = SubCategory::where('slug', 'bags')->firstOrFail();
        $phonesSubCategory = SubCategory::where('slug', 'phones')->firstOrFail();
        $sunglassesSubCategory = SubCategory::where('slug', 'sunglasses')->firstOrFail();
        // Create 'laptops' subcategory if it doesn't exist
        $laptopsSubCategory = SubCategory::where('slug', 'laptops')->firstOrCreate(
            ['slug' => 'laptops'],
            ['name_en' => 'Laptops', 'name_ar' => 'أجهزة لابتوب', 'iconUrl' => 'laptop_icon.png', 'category_id' => \App\Models\Category::where('slug', 'electronics')->first()->id]
        );

        // --- T-shirt (Male) ---
        Product::firstOrCreate(['slug' => 'تيشيرت-رجالي'], [
            'name' => 'تيشيرت رجالي',
            'slug' => 'تيشيرت-رجالي',
            'description' => 'تيشرت بطبعة',
            'price' => 299.99,
            'image' => 'storage/products/c5ee969a39847f84173601fad7592a9d2665472f.png',
            'images' => ['storage/products/c5ee969a39847f84173601fad7592a9d2665472f.png'], // Pass as array, Laravel casts to JSON
            'sub_category_id' => $tShirtSubCategory->id,
            'is_offer' => true,
            'discount_percent' => 15,
            'offer_expires_at' => now()->addDays(7),
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => true,
            'min_order_quantity' => 3,
            'rating' => 4.0,
            'reviews_count' => 50,
            'price_tiers' => [ // Pass as array, Laravel accessor handles
                ['min_qty' => 3, 'max_qty' => 10, 'price' => 280.00],
                ['min_qty' => 11, 'price' => 260.00],
            ],
            'shipping_cost' => 15.00,
            'estimated_delivery_days' => 5,
            'is_main_featured' => true,
            'model_number' => 'TSHIRT-M-001',
            'quality' => 'Standard',
            'specifications' => [ // All specific attributes consolidated here
                'colors' => ['Orange'], // Moved from top-level
                'size' => ['L'],       // Moved from top-level
                'gender' => 'رجالي',   // Moved from top-level
                'material' => 'قطن',   // Moved from top-level
                'neck_type' => 'Crew Neck',
                'sleeve_length' => 'Short',
            ],
            'is_featured' => 1,
        ]);

        // --- Comfortable Chair (Furniture) ---
        Product::firstOrCreate(['slug' => 'كرسي-مريح'], [
            'name' => 'كرسي مريح',
            'slug' => 'كرسي-مريح',
            'description' => 'Ergonomic office chair for comfort.',
            'price' => 89.99,
            'image' => 'storage/products/8ad8ce1e18ac5d339abc1cfd640d9cb517b36494.png',
            'images' => ['storage/products/8ad8ce1e18ac5d339abc1cfd640d9cb517b36494.png'],
            'sub_category_id' => $furnitureSubCategory->id,
            'is_offer' => false,
            'discount_percent' => null,
            'offer_expires_at' => null,
            'supplier_name' => 'Office Comforts',
            'supplier_confirmed' => true,
            'min_order_quantity' => 2,
            'rating' => 4.2,
            'reviews_count' => 30,
            'price_tiers' => [
                ['min_qty' => 2, 'max_qty' => 5, 'price' => 85.00],
                ['min_qty' => 6, 'price' => 80.00],
            ],
            'shipping_cost' => 25.00,
            'estimated_delivery_days' => 10,
            'is_main_featured' => false,
            'model_number' => 'CHAIR-ERG-001',
            'quality' => 'Premium',
            'specifications' => [
                'colors' => ['Black', 'Gray'],
                'material' => 'Mesh and Fabric',
                'assembly_required' => true,
                'max_weight_kg' => 120,
            ],
            'is_featured' => 0,
        ]);

        // --- Smartwatch ---
        Product::firstOrCreate(['slug' => 'ساعة-ذكية'], [
            'name' => 'ساعة ذكية',
            'slug' => 'ساعة-ذكية',
            'description' => 'Smartwatch with health tracking features.',
            'price' => 149.99,
            'image' => 'storage/products/1_1099cd20-7237-4bdf-a180-b7126de5ef3d_1024x1024.webp',
            'images' => ['storage/products/1_1099cd20-7237-4bdf-a180-b7126de5ef3d_1024x1024.webp'],
            'sub_category_id' => $watchesSubCategory->id,
            'is_offer' => true,
            'discount_percent' => 10,
            'offer_expires_at' => now()->addDays(14),
            'supplier_name' => 'Tech Gadgets',
            'supplier_confirmed' => true,
            'min_order_quantity' => 1,
            'rating' => 4.7,
            'reviews_count' => 75,
            'price_tiers' => [
                ['min_qty' => 1, 'max_qty' => 5, 'price' => 140.00],
                ['min_qty' => 6, 'price' => 130.00],
            ],
            'shipping_cost' => 10.00,
            'estimated_delivery_days' => 7,
            'is_main_featured' => false,
            'model_number' => 'SMARTWATCH-TG-001',
            'quality' => 'High',
            'specifications' => [
                'colors' => ['Black', 'Silver', 'Rose Gold'],
                'display_type' => 'AMOLED',
                'features' => ['Heart Rate Monitor', 'Sleep Tracker', 'GPS'],
                'water_resistance' => '5 ATM',
            ],
            'is_featured' => 1,
        ]);

        // --- Backpack ---
        Product::firstOrCreate(['slug' => 'حقيبة-ظهر'], [
            'name' => 'حقيبة ظهر',
            'slug' => 'حقيبة-ظهر',
            'description' => 'Durable backpack for daily use.',
            'price' => 39.99,
            'image' => 'storage/products/91a8a258e3d7b69ebf5633a0a8b97654e0c6a4e2.png',
            'images' => [
                'storage/products/1 (1).webp',
                'storage/products/2.webp',
            ],
            'sub_category_id' => $bagsSubCategory->id,
            'is_offer' => true,
            'discount_percent' => 25,
            'offer_expires_at' => now()->addDays(10),
            'supplier_name' => 'Bag World',
            'supplier_confirmed' => false,
            'min_order_quantity' => 1,
            'rating' => 4.3,
            'reviews_count' => 60,
            'price_tiers' => [
                ['min_qty' => 1, 'max_qty' => 5, 'price' => 35.00],
                ['min_qty' => 6, 'price' => 30.00],
            ],
            'shipping_cost' => 8.00,
            'estimated_delivery_days' => 6,
            'is_main_featured' => false,
            'model_number' => 'BP-BW-001',
            'quality' => 'Standard',
            'specifications' => [
                'colors' => ['Black', 'Navy', 'Gray'],
                'size' => ['Medium'],
                'material' => 'Polyester',
                'capacity_liters' => 25,
                'water_resistant' => true,
            ],
            'is_featured' => 1,
        ]);

        // --- Modern Furniture ---
        Product::firstOrCreate(['slug' => 'أثاث'], [
            'name' => 'أثاث',
            'slug' => 'أثاث',
            'description' => 'Modern furniture for home and office.',
            'price' => 999.99,
            'image' => 'storage/products/872c7362e417b45a9e5e1e13c169fb360de8fc74.png',
            'images' => ['storage/products/872c7362e417b45a9e5e1e13c169fb360de8fc74.png'],
            'sub_category_id' => $furnitureSubCategory->id,
            'is_offer' => false,
            'discount_percent' => null,
            'offer_expires_at' => null,
            'supplier_name' => 'Furniture World',
            'supplier_confirmed' => true,
            'min_order_quantity' => 1,
            'rating' => 4.9,
            'reviews_count' => 90,
            'price_tiers' => [
                ['min_qty' => 1, 'price' => 999.99],
            ],
            'shipping_cost' => 50.00,
            'estimated_delivery_days' => 15,
            'is_main_featured' => true,
            'model_number' => 'FURN-MOD-001',
            'quality' => 'Premium',
            'specifications' => [
                'colors' => ['Brown', 'Beige', 'White'],
                'material' => 'Solid Wood',
                'style' => 'Modern',
                'assembly_required' => false,
            ],
            'is_featured' => 1,
        ]);

        // --- iPhone 15 Pro Max ---
        Product::firstOrCreate(['slug' => 'iphone-15-pro-max'], [
            'name' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max',
            'description' => 'Latest iPhone with advanced features and A17 Bionic chip.',
            'price' => 1099.99,
            'image' => 'storage/products/28442cd2d8b4be75c0b3aa5caada440028925cd0.png',
            'images' => ['storage/products/28442cd2d8b4be75c0b3aa5caada440028925cd0.png',
            'storage/products/Apple-iPhone-15-Pro-Max-256GB-Natural-Titanium-1.webp',
            'storage/products/Iphone 15 Pro Natural Titanium 256Gb (2)-600x600.jpg'],
            'sub_category_id' => $phonesSubCategory->id,
            'is_offer' => true,
            'discount_percent' => 5,
            'offer_expires_at' => now()->addDays(14),
            'supplier_name' => 'Apple Inc.',
            'supplier_confirmed' => true,
            'min_order_quantity' => 1,
            'rating' => 4.8,
            'reviews_count' => 200,
            'price_tiers' => [
                ['min_qty' => 1, 'max_qty' => 2, 'price' => 1099.99],
                ['min_qty' => 3, 'price' => 1050.00],
            ],
            'shipping_cost' => 3.00,
            'estimated_delivery_days' => 4,
            'is_main_featured' => true,
            'model_number' => 'IPHONE15PMAX',
            'quality' => 'Premium',
            'specifications' => [
                    'colors' => [ // This is an array of objects
                                ['name' => 'Blue Titanium', 'swatch_image' => 'storage/products/28442cd2d8b4be75c0b3aa5caada440028925cd0.png'],
        ['name' => 'Brown Titanium', 'swatch_image' => 'storage/products/Apple-iPhone-15-Pro-Max-256GB-Natural-Titanium-1.webp'],
        ['name' => 'Gold Titanium', 'swatch_image' => 'storage/products/Iphone 15 Pro Natural Titanium 256Gb (2)-600x600.jpg'],
    ],
                'storage_gb' => [128, 256, 512, 1024],
                'processor' => 'A17 Bionic',
                'display_size_inch' => 6.7,
                'camera_megapixels' => '48MP Main',
            ],
            'is_featured' => 1,
        ]);

        // --- T-shirt (Polo) ---
        Product::firstOrCreate(['slug' => 'تيشيرت'], [
            'name' => 'تيشيرت',
            'slug' => 'تيشيرت',
            'description' => 'تيشرت بولو',
            'price' => 299.99,
            'image' => 'storage/products/c7cc5df6a3a3ad5b9e08d091347e0b2f8b61e1e4.png',
            'images' => [
                'storage/products/c7cc5df6a3a3ad5b9e08d091347e0b2f8b61e1e4.png',
                'storage/products/340bb1d4c43f0944e9e74745b6d6eb54c7252b78.png',
            ],
            'sub_category_id' => $tShirtSubCategory->id,
            'is_offer' => true,
            'discount_percent' => 15,
            'offer_expires_at' => now()->addDays(7),
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => true,
            'min_order_quantity' => 3,
            'rating' => 4.5,
            'reviews_count' => 40,
            'price_tiers' => [
                ['min_qty' => 3, 'max_qty' => 10, 'price' => 280.00],
                ['min_qty' => 11, 'price' => 260.00],
            ],
            'shipping_cost' => 15.00,
            'estimated_delivery_days' => 5,
            'is_main_featured' => false,
            'model_number' => 'TSHIRT-POLO-002',
            'quality' => 'High',
            'specifications' => [
                'colors' => ['Black', 'White', 'Gray'],
                'size' => ['M', 'L', 'XL'],
                'gender' => 'رجالي',
                'material' => 'بوليستر',
                'neck_type' => 'Polo Collar',
            ],
            'is_featured' => 1,
        ]);

        // --- Sunglasses ---
        Product::firstOrCreate(['slug' => 'نظارة-شمس'], [
            'name' => 'نظارة شمس',
            'slug' => 'نظارة-شمس',
            'description' => 'Stylish sunglasses with UV protection.',
            'price' => 100.00,
            'image' => 'storage/products/aafd3d80765ef1803d24002179a479f672501611.png',
            'images' => ['storage/products/aafd3d80765ef1803d24002179a479f672501611.png'],
            'sub_category_id' => $sunglassesSubCategory->id,
            'is_offer' => true,
            'discount_percent' => 10,
            'offer_expires_at' => now()->addDays(20),
            'supplier_name' => 'Fuzhou Green',
            'supplier_confirmed' => true,
            'min_order_quantity' => 2,
            'rating' => 4.8,
            'reviews_count' => 120,
            'price_tiers' => [
                ['min_qty' => 2, 'max_qty' => 5, 'price' => 95.00],
                ['min_qty' => 6, 'price' => 90.00],
            ],
            'shipping_cost' => 7.50,
            'estimated_delivery_days' => 8,
            'is_main_featured' => false,
            'model_number' => 'SUN-FZY-001',
            'quality' => 'High',
            'specifications' => [
                'colors' => ['Black', 'Brown', 'Silver'],
                'frame_material' => 'Acetate',
                'lens_type' => 'Polarized',
                'uv_protection' => 'UV400',
                'frame_shape' => 'Wayfarer',
            ],
            'is_featured' => 1,
        ]);

        // --- Sport T-shirt (Female) ---
        Product::firstOrCreate(['slug' => 'تيشيرت-رياضي'], [
            'name' => 'تيشيرت رياضي',
            'slug' => 'تيشيرت-رياضي',
            'description' => 'تيشرت رياضي مريح للنساء.',
            'price' => 155.0,
            'image' => 'storage/products/a83454ae419fb750af84f07c1f35acb42e3d3ff5.png',
            'images' => [
                'storage/products/a83454ae419fb750af84f07c1f35acb42e3d3ff5.png',
                'storage/products/c9e653c80313c6ada3c24262f4d7cd4f205b36fb.png',
            ],
            'sub_category_id' => $tShirtSubCategory->id, // Assuming it's still a t-shirt subcategory
            'is_offer' => true,
            'discount_percent' => 10,
            'offer_expires_at' => now()->addDays(7),
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => true,
            'min_order_quantity' => 3,
            'rating' => 4.0,
            'reviews_count' => 35,
            'price_tiers' => [
                ['min_qty' => 3, 'max_qty' => 10, 'price' => 145.00],
                ['min_qty' => 11, 'price' => 135.00],
            ],
            'shipping_cost' => 12.00,
            'estimated_delivery_days' => 5,
            'is_main_featured' => false,
            'model_number' => 'TSHIRT-SPORT-003',
            'quality' => 'Standard',
            'specifications' => [
                'colors' => ['Blue', 'Gray', 'Caffe'],
                'size' => ['S', 'M', 'L'],
                'gender' => 'نسائي',
                'material' => 'قطن',
                'fabric_type' => 'Breathable',
            ],
            'is_featured' => 1,
        ]);

        // --- Plain T-shirt ---
        Product::firstOrCreate(['slug' => 'تيشيرت-سادة'], [
            'name' => 'تيشيرت سادة',
            'slug' => 'تيشيرت-سادة',
            'description' => 'تيشرت سادة كلاسيكي.',
            'price' => 252.0,
            'image' => 'storage/products/c7cc5df6a3a3ad5b9e08d091347e0b2f8b61e1e4.png',
            'images' => [
                'storage/products/c7cc5df6a3a3ad5b9e08d091347e0b2f8b61e1e4.png',
                'storage/products/340bb1d4c43f0944e9e74745b6d6eb54c7252b78.png',
            ],
            'sub_category_id' => $tShirtSubCategory->id,
            'is_offer' => true,
            'discount_percent' => 5,
            'offer_expires_at' => now()->addDays(7),
            'supplier_name' => 'Fashion Supplier',
            'supplier_confirmed' => false,
            'min_order_quantity' => 3,
            'rating' => 4.0,
            'reviews_count' => 25,
            'price_tiers' => [
                ['min_qty' => 3, 'max_qty' => 10, 'price' => 240.00],
                ['min_qty' => 11, 'price' => 230.00],
            ],
            'shipping_cost' => 15.00,
            'estimated_delivery_days' => 5,
            'is_main_featured' => false,
            'model_number' => 'TSHIRT-PLAIN-004',
            'quality' => 'Standard',
            'specifications' => [
                'colors' => ['Black', 'White', 'Nike Red'],
                'size' => ['S', 'M', 'L', 'XL'],
                'material' => 'قطن',
                'gender' => 'رجالي',
                'fit_type' => 'Regular',
            ],
            'is_featured' => 1,
        ]);

        // --- New Laptop Product ---
        Product::firstOrCreate(['slug' => Str::slug('Dell XPS 15')], [
            'name' => 'Dell XPS 15',
            'slug' => Str::slug('Dell XPS 15'),
            'description' => 'Powerful laptop for creative professionals, featuring a stunning display and high-performance components.',
            'price' => 1899.99,
            'image' => '/images/products/dell-xps-15-main.jpg',
            'images' => [
                '/images/products/dell-xps-15-1.jpg',
                '/images/products/dell-xps-15-2.jpg',
                '/images/products/dell-xps-15-3.jpg',
            ],
            'sub_category_id' => $laptopsSubCategory->id,
            'is_offer' => true,
            'discount_percent' => 10,
            'offer_expires_at' => now()->addDays(30),
            'supplier_name' => 'Dell Official Store',
            'supplier_confirmed' => true,
            'min_order_quantity' => 1,
            'rating' => 4.8,
            'reviews_count' => 150,
            'price_tiers' => [
                ['min_qty' => 1, 'max_qty' => 1, 'price' => 1899.99],
                ['min_qty' => 2, 'max_qty' => 5, 'price' => 1800.00],
                ['min_qty' => 6, 'price' => 1750.00],
            ],
            'shipping_cost' => 0.00,
            'estimated_delivery_days' => 5,
            'is_main_featured' => true,
            'model_number' => 'XPS15-2024',
            'quality' => 'Premium',
            'specifications' => [
                'processor' => 'Intel Core i7-13700H',
                'ram' => '32GB DDR5',
                'storage' => '1TB PCIe NVMe SSD',
                'graphics_card' => 'NVIDIA GeForce RTX 4070',
                'operating_system' => 'Windows 11 Pro',
                'battery_life_hours' => 10,
                'webcam' => '1080p FHD',
                'ports' => '2x Thunderbolt 4, 1x USB-C 3.2 Gen 2, SD Card Reader',
                'weight_kg' => 1.92,
                'colors' => ['Silver', 'Space Gray'],
                'size' => ['15.6-inch'],
                'material' => 'Aluminum',
            ],
            'is_featured' => true,
        ]);

        $this->command->info('Products seeded successfully!');
    }
}
