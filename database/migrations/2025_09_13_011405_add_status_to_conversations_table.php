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
        $table->enum('status', ['open', 'reported', 'closed','under_review'])->default('open')->after('product_id');
    });
}

public function down()
{
    Schema::table('conversations', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
