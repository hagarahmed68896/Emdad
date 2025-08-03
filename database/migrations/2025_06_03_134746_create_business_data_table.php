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
            $table->string('company_name')->naullable();
            $table->string('national_id')->nullable();
            $table->string('commercial_registration')->nullable();
            $table->string('national_address')->nullable();
            $table->string('iban')->nullable();
            $table->string('tax_certificate')->nullable();
            $table->boolean(column: 'supplier_confirmed')->default(false); // If supplier confirmed
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
