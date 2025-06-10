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
            $table->string('image')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            
            // Offer-related columns:
            $table->boolean('is_offer')->default(false); // true if the product is on offer
            $table->unsignedTinyInteger('discount_percent')->nullable(); 
            $table->timestamp('offer_expires_at')->nullable(); // Optional: when the offer ends
        
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
