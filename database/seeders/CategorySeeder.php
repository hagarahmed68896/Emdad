<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name_en' => 'Electronics', 'name_ar' => 'الإلكترونيات', 'slug' => 'electronics','iconUrl' => '8e9558ec8a1c11e2c6fb22d5d5c5f657b5cbf8c7.png']);
        Category::create(['name_en' => 'Furniture', 'name_ar' => 'الأثاث', 'slug' => 'furniture','iconUrl' => '8ad8ce1e18ac5d339abc1cfd640d9cb517b36494.png']);
        Category::create(['name_en' => 'Testing Supplies', 'name_ar' => 'مستلزمات مكتبية', 'slug' => 'testing-supplies','iconUrl' => '4b86b7d17ff840be81ca85b2e885843777dcbb85.png']);
        Category::create(['name_en' => 'Clothing', 'name_ar' => 'الأزياء', 'slug' => 'clothing','iconUrl' => 'c5ee969a39847f84173601fad7592a9d2665472f.png']);
        Category::create(['name_en' => 'Home Appliances', 'name_ar' => 'الاجهزة الكهربائية', 'slug' => 'electrical-appliances','iconUrl' => '']);
        Category::create(['name_en' => 'Industrial Equipment', 'name_ar' => 'المعدات الصناعية', 'slug' => 'industrial-equipment','iconUrl' => '']);
        Category::create(['name_en' => 'Tools & Equipment', 'name_ar' => 'العدد والأدوات', 'slug' => 'tools-equipment','iconUrl' => '']);
        Category::create(['name_en' => 'Medical Devices', 'name_ar' => 'الأجهزة الطبية', 'slug' => 'medical-devices','iconUrl' => '']);
        Category::create(['name_en' => 'Metal Materials', 'name_ar' => 'المواد المعدنية', 'slug' => 'metal-materials','iconUrl' => '']);
        Category::create(['name_en' => 'Food Materials', 'name_ar' => 'المواد الغذائية', 'slug' => 'food_materials', 'iconUrl' => '' ]);

}
}
