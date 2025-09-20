<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profit_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('percentage', 5, 2)->default(0); // نسبة الأرباح 0-100
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profit_settings');
    }
};

