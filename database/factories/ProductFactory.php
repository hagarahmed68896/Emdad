<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category; // Make sure to import Category model
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Import Str for slug generation

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Ensure you have categories seeded first, or create them on the fly
        // For simplicity, let's assume you have some categories.
        // If not, you might need to seed categories first or add a Category::factory() here.
        $category = Category::inRandomOrder()->first();

        // If no categories exist, create one
        if (!$category) {
            $category = Category::factory()->create();
        }

        $name = $this->faker->words(rand(2, 5), true); // Generates 2-5 words for a product name
        $description = $this->faker->paragraph(rand(3, 8)); // Generates 3-8 sentences for description

        return [
            'name' => $name,
            'slug' => Str::slug($name), // Generate a slug from the name
            'description' => $description,
            'price' => $this->faker->randomFloat(2, 10, 1000), // Price between 10.00 and 1000.00
            'image' => 'products/' . $this->faker->image('public/storage/products', 640, 480, null, false), // Generates a dummy image path
            'category_id' => $category->id,
        ];
    }
}