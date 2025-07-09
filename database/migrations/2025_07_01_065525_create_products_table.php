<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Base price

            // Core product relationships and images
            $table->string('image')->nullable(); // Main image (single path)
            $table->json('images')->nullable(); // Stores an array of additional image paths (JSON)
            $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade');

            // Offer-related columns:
            $table->boolean('is_offer')->default(false); // true if the product is on offer
            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->timestamp('offer_expires_at')->nullable(); // Optional: when the offer ends

            // Supplier & Basic Product Info
            $table->string('supplier_name')->nullable(); // Supplier name
            $table->boolean('supplier_confirmed')->default(false); // If supplier confirmed
            $table->unsignedInteger('min_order_quantity')->default(1); // Minimum order for each product
            $table->decimal('rating', 2, 1)->nullable(); // Rating (e.g., 4.5)
            $table->unsignedInteger('reviews_count')->default(0); // Total number of reviews

            // Pricing Tiers (JSON column for quantity-based pricing)
            $table->json('price_tiers')->nullable(); // e.g., [{"min_qty": 1, "max_qty": 10, "price": 100}]

            // Shipping & Delivery
            $table->decimal('shipping_cost', 8, 2)->nullable(); // e.g., 5.99 or percentage (if stored as decimal)
            $table->unsignedInteger('estimated_delivery_days')->nullable(); // e.g., 3-5 days

            // New requested columns:
            $table->boolean('is_main_featured')->default(false); // For products prominently featured (e.g., on homepage banner)
            $table->string('model_number')->nullable(); // Unique identifier for product model
            $table->string('quality')->nullable(); // e.g., "High", "Premium", "Standard"

            // Generic JSON column for highly specific or varied attributes
            // 'colors', 'size', 'gender', 'material' will now be stored within 'specifications'
            // For example:
            // - T-shirt: {"neck_type": "Crew", "sleeve_style": "Short", "colors": ["Red", "Blue"], "size": ["S", "M"], "gender": "Male", "material": "Cotton"}
            // - Phone: {"processor": "Snapdragon 8 Gen 2", "ram": "12GB", "storage": "256GB SSD"}
            // - Furniture: {"wood_type": "Oak", "assembly_required": true}
            $table->json('specifications')->nullable();

            // Existing 'is_featured' (could be for general featured sections)
            $table->boolean('is_featured')->default(false);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
