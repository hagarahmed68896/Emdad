<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('product_id');
            $table->json('issues')->nullable()->after('comment');
            $table->enum('issue_type', ['product', 'order'])->nullable()->after('issues');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('issue_type');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'issues', 'issue_type', 'status']);
        });
    }
};

