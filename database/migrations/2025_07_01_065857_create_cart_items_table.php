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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Assuming you have a 'products' table
            $table->integer('quantity');
            $table->decimal('price_at_addition', 10, 2); // Store price when added
            $table->json('options')->nullable(); // For variations like size, color, etc.
            $table->timestamps();

            // Ensure unique product per cart, considering options (if needed)
$table->unique(['cart_id', 'product_id']);        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};