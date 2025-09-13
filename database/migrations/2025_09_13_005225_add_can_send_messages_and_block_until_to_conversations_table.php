<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->boolean('can_send_messages')->default(true)->after('status');
            $table->timestamp('block_until')->nullable()->after('can_send_messages');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['can_send_messages', 'block_until']);
        });
    }
};

