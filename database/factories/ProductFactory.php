<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;    // Still needed if you factory categories for sub-categories
use App\Models\SubCategory; // IMPORTANT: Import SubCategory model
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
        // Ensure you have sub-categories seeded first, or create them on the fly.
        // Products must link to a SubCategory.
        $subCategory = SubCategory::inRandomOrder()->first();

        // If no sub-categories exist, create a category and then a sub-category
        if (!$subCategory) {
            // First, ensure a Category exists
            $category = Category::inRandomOrder()->first();
            if (!$category) {
                $category = Category::factory()->create();
            }
            // Then, create a SubCategory linked to that Category
            $subCategory = SubCategory::factory()->create([
                'category_id' => $category->id,
            ]);
        }

        $name = $this->faker->words(rand(2, 5), true); // Generates 2-5 words for a product name
        $description = $this->faker->paragraph(rand(3, 8)); // Generates 3-8 sentences for description

        // Determine if product is on offer
        $isOffer = $this->faker->boolean(30); // 30% chance of being on offer
        $discountPercent = $isOffer ? $this->faker->numberBetween(5, 50) : null;
        $offerExpiresAt = $isOffer ? $this->faker->dateTimeBetween('+1 day', '+1 month') : null;

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(4), // Ensure unique slug
            'description' => $description,
            'price' => $this->faker->randomFloat(2, 10, 1000), // Price between 10.00 and 1000.00
            'image' => 'https://placehold.co/640x480/E0E0E0/333333?text=' . urlencode($name), // Placeholder image
            // IMPORTANT CHANGE: Link to sub_category_id
            'sub_category_id' => $subCategory->id,

            // Offer-related columns
            'is_offer' => $isOffer,
            'discount_percent' => $discountPercent,
            'offer_expires_at' => $offerExpiresAt,

            // New fields added
            'supplier_name' => $this->faker->company,
            'supplier_confirmed' => $this->faker->boolean(80), // 80% chance of being confirmed
            'min_order_quantity' => $this->faker->numberBetween(1, 5),
            'rating' => $this->faker->randomFloat(1, 1, 5), // Rating between 1.0 and 5.0
            'images' => json_encode([ // Store multiple images as JSON
                'https://placehold.co/400x300/C0C0C0/333333?text=' . urlencode($name . ' Alt 1'),
                'https://placehold.co/400x300/B0B0B0/333333?text=' . urlencode($name . ' Alt 2'),
            ]),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
        ];
    }
}