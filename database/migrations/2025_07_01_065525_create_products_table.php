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
            $table->decimal('price', 10, 2);

            // Core product relationships and images
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->foreignId('sub_category_id')->constrained('sub_categories')->onDelete('cascade');


            // Supplier & Basic Product Info
            $table->unsignedInteger('min_order_quantity')->default(1);
            $table->decimal('rating', 2, 1)->nullable();
            $table->unsignedInteger('reviews_count')->default(0);

            $table->json('price_tiers')->nullable();
            // Shipping & Delivery
            $table->decimal('shipping_cost', 8, 2)->nullable();
            $table->unsignedInteger('estimated_delivery_days')->nullable();
            $table->unsignedInteger('preparation_days')->nullable();
            $table->unsignedInteger('shipping_days')->nullable();

            // Additional product info
            $table->boolean('is_main_featured')->default(false);
            $table->string('model_number')->nullable();
            $table->string('quality')->nullable();
            $table->string('material_type')->nullable();
            $table->unsignedInteger('available_quantity')->nullable();

            $table->string('production_capacity')->nullable();
            $table->decimal('product_weight', 8, 2)->nullable();
            $table->string('package_dimensions')->nullable();

            $table->string('attachments')->nullable();

            // ✅ بدلاً من specifications: أعمدة منفصلة
            $table->json('sizes')->nullable();
            $table->json('colors')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->foreignId('business_data_id')->constrained('business_data')->onDelete('cascade');

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
