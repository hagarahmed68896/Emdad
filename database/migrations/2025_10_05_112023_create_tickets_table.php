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
      Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id'); // العميل اللي فتح التذكرة
    $table->string('subject'); // عنوان المشكلة
    $table->enum('type', ['general', 'account', 'order', 'technical']);
    $table->text('message'); // رسالة البداية
    $table->enum('status', ['open', 'closed'])->default('open');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
