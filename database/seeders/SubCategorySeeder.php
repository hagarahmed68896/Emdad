<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // Make sure to import your Category model
use App\Models\SubCategory; // Make sure to import your SubCategory model
use Illuminate\Support\Str; // For generating slugs

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     
    
        $subCategoriesData = [
            [
                'name_en' => 't-shirt',
                'name_ar' => ' تيشرت',
                'slug'=> 't-shirt',
                'iconUrl'=> 'c5ee969a39847f84173601fad7592a9d2665472f.png',
                'category_id' => Category::where('slug', operator: 'clothing')->first()->id,
            ],
            [
                'name_en' => 'Sportswear',
                'name_ar' => 'ملابس رياضية',
                'slug'=> 'sportswear',
                'iconUrl'=> 'ae47aac6b78e0b40ec624c0a9901629506f88327.png',
                'category_id' => Category::where('slug', operator: 'clothing')->first()->id,
            ],
            [
                'name_en' => 'Pants',
                'name_ar' => 'بنطلون',
                'slug'=> 'pants',
                'iconUrl'=> 'fafd2e33685db985079daa9d00e4d6b7272db7a3.png',
                'category_id' => Category::where('slug', operator: 'clothing')->first()->id,
            ],
            [
                'name_en' => 'Sneakers',
                'name_ar' => 'حذاء رياضي',
                'slug'=> 'sneakers',
                'iconUrl'=> '9b0bc513807fe7ee77741df5d5403cd607ced526.png',
                'category_id' => Category::where('slug', operator: 'clothing')->first()->id,
            ],
            [
                'name_en' => 'watches',
                'name_ar' => 'الساعات',
                'slug'=> 'watches',
                'iconUrl'=> '59d1dd7ec5648b9ed4f362a219e878e5c0e10cc4.png',
                'category_id' => Category::where('slug', operator: 'clothing')->first()->id,
            ],
            [
                'name_en' => 'Bags',
                'name_ar' => 'حقائب',
                'slug'=> 'bags',
                'iconUrl'=> '91a8a258e3d7b69ebf5633a0a8b97654e0c6a4e2.png',
                'category_id' => Category::where('slug', operator: 'clothing')->first()->id,
            ],
            [
                'name_en' => 'Sunglasses',
                'name_ar' => 'نظارات شمسية',
                'slug'=> 'sunglasses',
                'iconUrl'=> 'aafd3d80765ef1803d24002179a479f672501611.png',
                'category_id' => Category::where('slug', operator: 'clothing')->first()->id,
            ],
            [
                'name_en' => 'Accessories',
                'name_ar' => 'إكسسوارات',
                'slug'=> 'accessories',
                'iconUrl'=> 'ef110cc82138aae8ff1be67ecd567a196e671856.png',
                'category_id' => Category::where('slug', operator: 'clothing')->first()->id,
            ],
            [
                'name_en'=> 'Furniture',
                'name_ar'=> 'أثاث',
                'slug'=> 'furniture',
                'iconUrl'=> '8ad8ce1e18ac5d339abc1cfd640d9cb517b36494.png',
                'category_id' => Category::where('slug', operator: 'furniture')->first()->id,
            ],
            [
                'name_en'=> 'Phones',
                'name_ar'=> 'هواتف',
                'slug'=> 'phones',
                'iconUrl'=> '28442cd2d8b4be75c0b3aa5caada440028925cd0.png',
                'category_id' => Category::where('slug', operator: 'electronics')->first()->id,
            ],
        ];
       

        foreach ($subCategoriesData as $data) {
            SubCategory::create([
                'name_en' => $data['name_en'],
                'name_ar' => $data['name_ar'],
                'slug' => $data['slug'], 
                'iconUrl' => $data['iconUrl'],
                'category_id' => $data['category_id'],
            ]);
        }

        $this->command->info('Sub-categories seeded successfully!');
    }    }

