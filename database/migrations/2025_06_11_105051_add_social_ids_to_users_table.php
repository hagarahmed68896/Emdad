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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('facebook_id')->nullable()->after('google_id');
            // Optional: Add a column to track the provider type
            $table->string('provider')->nullable()->after('facebook_id');
            $table->string('provider_id')->nullable()->after('provider');

            // Add unique index for social IDs
            $table->unique(['google_id']);
            $table->unique(['facebook_id']);
        });
     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id','facebook_id','provider','provider_id']);
        });
    }
};
