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
    Schema::create('scrape_schedules', function (Blueprint $table) {
        $table->id();
        $table->dateTime('scheduled_at'); // Waktu eksekusi dari frontend
        $table->string('status')->default('pending'); // pending, success, failed
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrape_schedules');
    }
};
