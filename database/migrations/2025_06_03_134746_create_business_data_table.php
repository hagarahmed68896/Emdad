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
            Schema::create('business_data', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->string('company_name')->nullable();
            $table->string('national_id')->nullable();
            $table->string('national_id_attach')->nullable();
            $table->string('commercial_registration')->nullable();
            $table->string('commercial_registration_attach')->nullable();
            $table->string('national_address')->nullable();
            $table->string('national_address_attach')->nullable();
            $table->string('iban')->nullable();
            $table->string('iban_attach')->nullable();
            $table->string('tax_certificate')->nullable();
            $table->string('tax_certificate_attach')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_data');
    }
};
