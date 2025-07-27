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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique()->nullable();
            $table->decimal('total_amount', 10, 2)->default(0.00);

            // Add payment way: مثل (cash, card, transfer, etc.)
            $table->enum('payment_way', ['cash', 'bank_transfer', 'credit_card']); // عدل القيم حسب طرق الدفع لديك

            // Use ENUM-like pattern for status
            $table->enum('status', ['completed', 'processing', 'cancelled', 'returned']
            )->default('processing');

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
