<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('financial_settlements', function (Blueprint $table) {
            $table->id();

            // supplier_id references business_data.id
            $table->foreignId('supplier_id')
                  ->constrained('business_data', 'id')
                  ->onDelete('cascade');

            $table->string('request_number'); // Request number
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'transferred'])->default('pending');
            $table->date('settlement_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_settlements');
    }
};
