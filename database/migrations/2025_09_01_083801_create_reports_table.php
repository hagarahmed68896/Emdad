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
    Schema::create('reports', function (Blueprint $table) {
        $table->id();

        // Who made the report
        $table->unsignedBigInteger('reporter_id'); // user_id or supplier's user_id
        $table->foreign('reporter_id')->references('id')->on('users')->onDelete('cascade');

        // Who is being reported
        $table->unsignedBigInteger('reported_id'); 
        $table->foreign('reported_id')->references('id')->on('users')->onDelete('cascade');

        // Reason for reporting
        $table->text('reason')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
