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
        Schema::create('responsible_event', function (Blueprint $table) {
            $table->foreignId('responsible_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('event_id')->references('id')->on('events')->onDelete('cascade');

            $table->unique(['responsible_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsible_event');
    }
};
