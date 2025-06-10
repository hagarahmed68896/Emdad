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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('code', 255)->change();
            $table->string('identifier_type'); // Type of identifier (e.g., 'email', 'phone')
            $table->string('identifier'); // The value of the identifier (e.g., email address or phone number)
            $table->timestamp('expires_at'); // Expiration time for the OTP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          Schema::table('otps', function (Blueprint $table) {
            // Revert the column back to its original length if needed (careful with existing data)
            // This might truncate data if you have existing hashes longer than 10
            $table->string('code', 10)->change();
        });
    }
};
