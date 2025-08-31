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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('type', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Revert to the old column definition.
            // You should check your original migration to find the correct type and length.
            // Example: $table->char('type', 10)->change();
            $table->string('type', 10)->change(); // Assuming a previous size of 10
        });
    }
};
