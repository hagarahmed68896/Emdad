<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Change enum values
        DB::statement("ALTER TABLE tickets MODIFY COLUMN type ENUM('General', 'Account', 'Order', 'Technical') NOT NULL");
    }

    public function down(): void
    {
        // Rollback to old enum
        DB::statement("ALTER TABLE tickets MODIFY COLUMN type ENUM('general', 'account', 'order', 'technical') NOT NULL");
    }
};

