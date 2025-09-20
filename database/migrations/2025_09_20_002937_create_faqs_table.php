<?php

// database/migrations/xxxx_xx_xx_create_faqs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('faqs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('question');
            $table->text('answer');
            $table->string('type')->default('public'); // نوع السؤال
            $table->enum('user_type', ['customer', 'supplier']); // الفئة المستهدفة
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('faqs');
    }
};

