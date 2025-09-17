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
    Schema::create('admin_notifications', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('title');
        $table->text('content');
        $table->string('category'); // client, supplier
        $table->string('notification_type'); // alert, offer, info
        $table->string('status'); // sent, pending
        $table->json('data')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
