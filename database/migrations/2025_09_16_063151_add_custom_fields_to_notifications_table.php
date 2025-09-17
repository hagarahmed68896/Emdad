<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title')->nullable()->after('type');
            $table->text('content')->nullable()->after('title');
            $table->enum('category', ['customer', 'supplier'])->nullable()->after('content');
            $table->enum('notification_type', ['alert', 'offer', 'info'])->nullable()->after('category');
            $table->enum('status', ['sent', 'pending'])->default('pending')->after('notification_type');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'category', 'notification_type', 'status']);
        });
    }
};

