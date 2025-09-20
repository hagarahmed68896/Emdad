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
        Schema::table('terms', function (Blueprint $table) {
                $table->enum('type', ['terms', 'policies'])
                  ->default('terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms', function (Blueprint $table) {
             $table->dropColumn('type');

        });
    }
};
