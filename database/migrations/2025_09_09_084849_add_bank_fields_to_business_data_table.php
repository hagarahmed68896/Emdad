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
    Schema::table('business_data', function (Blueprint $table) {
        $table->string('bank_name')->nullable();
        $table->string('account_name')->nullable();
        $table->string('bank_address')->nullable();
        $table->string('swift_code')->nullable();
        $table->string('iban')->nullable()->change(); // already exists, just ensure it is nullable
    });
}

public function down()
{
    Schema::table('business_data', function (Blueprint $table) {
        $table->dropColumn(['bank_name', 'account_name', 'bank_address', 'swift_code']);
    });
}

};
