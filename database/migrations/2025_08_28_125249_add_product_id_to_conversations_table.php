<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('conversations', function (Blueprint $table) {
        $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
    });
}

public function down()
{
    Schema::table('conversations', function (Blueprint $table) {
        $table->dropForeign(['product_id']);
        $table->dropColumn('product_id');
    });
}

};
