<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_orders_table.php

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('order_number')->unique(); // Example: A unique order number
            $table->decimal('total_amount', 10, 2)->default(0.00); // Example: Total amount of the order
            $table->string('status')->default('pending'); // Example: pending, processing, completed, cancelled
            // Add any other columns relevant to your orders, e.g., shipping address, payment status, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};