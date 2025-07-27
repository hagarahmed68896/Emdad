<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique()->nullable();
            
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            $table->enum('payment_way', ['cash', 'bank_transfer', 'credit_card']); // عدل القيم حسب طرق الدفع لديك
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['payment', 'not payment', 'review'])->default('not payment');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
