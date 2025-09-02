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
    Schema::table('reports', function (Blueprint $table) {
        $table->string('report_type')->nullable()->after('reason'); 
        // هيتحط بعد عمود reason (غيّر مكان after لو محتاج)
    });
}

public function down()
{
    Schema::table('reports', function (Blueprint $table) {
        $table->dropColumn('report_type');
    });
}

};
