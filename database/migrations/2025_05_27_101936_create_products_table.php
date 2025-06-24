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
            $table->decimal('price', 10, 2); // Original price
            $table->string('image')->nullable(); // Main image, if any
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            // Offer-related columns:
            $table->boolean('is_offer')->default(false); // true if the product is on offer
            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->timestamp('offer_expires_at')->nullable(); // Optional: when the offer ends

            // New fields added:
            $table->string('supplier_name')->nullable(); // Supplier name added to the product
            $table->boolean('supplier_confirmed')->default(false); // If supplier confirmed put the green mark
            $table->unsignedInteger('min_order_quantity')->default(1); // Minimum order for each product
            $table->decimal('rating', 2, 1)->nullable(); // Rating for each product (e.g., 4.5)
            // For 'more than one image', a separate table for product images is typically better,
            // but for simplicity and direct addition to the 'products' table as requested,
            // a JSON column can store an array of image paths.
            $table->json('images')->nullable(); // Stores an array of additional image paths

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