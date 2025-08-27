<?php

// database/migrations/..._alter_orders_table_add_payment_methods.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Change the enum to include 'paypal' and 'card'
            $table->enum('payment_way', ['cash', 'bank_transfer', 'credit_card', 'paypal', 'card'])->change();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // You should be careful with down() methods for enums
            // as it can cause data loss if you have a row with 'paypal'
            $table->enum('payment_way', ['cash', 'bank_transfer', 'credit_card'])->change();
        });
    }
};
