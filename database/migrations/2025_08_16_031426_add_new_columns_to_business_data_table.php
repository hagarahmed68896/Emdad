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
        Schema::table('business_data', function (Blueprint $table) {
            $table->integer('experience_years')->nullable(); // number of years of experience
            $table->text('description')->nullable();         // supplier general description

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_data', function (Blueprint $table) {
            $table->dropColumn(['experience_years', 'description']);

        });
    }
};
