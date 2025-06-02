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
        Category::create(['name_en' => 'All', 'name_ar' => 'الجميع', 'slug' => 'all']);
        Category::create(['name_en' => 'Electronics', 'name_ar' => 'الالكترونيات', 'slug' => 'electronics']);
        Category::create(['name_en' => 'Home Appliances', 'name_ar' => 'الاجهزة الكهربائية', 'slug' => 'home-appliances']);
        Category::create(['name_en' => 'Industrial Equipment', 'name_ar' => 'المعدات الصناعية', 'slug' => 'industrial-equipment']);
        Category::create(['name_en' => 'Tools & Equipment', 'name_ar' => 'العدد والأدوات', 'slug' => 'tools-equipment']);
        Category::create(['name_en' => 'Testing Supplies', 'name_ar' => 'مستلزمات بحثية', 'slug' => 'testing-supplies']);
        Category::create(['name_en' => 'Medical Devices', 'name_ar' => 'الأجهزة الطبية', 'slug' => 'medical-devices']);
        Category::create(['name_en' => 'Furniture', 'name_ar' => 'الأثاث', 'slug' => 'furniture']);
        Category::create(['name_en' => 'Metal Materials', 'name_ar' => 'المواد المعدنية', 'slug' => 'metal-materials']);
        Category::create(['name_en' => 'Clothing', 'name_ar' => 'الأزياء', 'slug' => 'clothing']);

}
}
