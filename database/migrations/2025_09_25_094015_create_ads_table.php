<?php

// database/migrations/xxxx_xx_xx_create_ads_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade'); 
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // ad banner or image
            $table->decimal('amount', 10, 2); // payment amount
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};

