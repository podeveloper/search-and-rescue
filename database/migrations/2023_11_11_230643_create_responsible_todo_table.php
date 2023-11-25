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
        Schema::create('responsible_todo', function (Blueprint $table) {
            $table->foreignId('responsible_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('todo_id')->references('id')->on('todos')->onDelete('cascade');

            $table->unique(['responsible_id', 'todo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsible_todo');
    }
};
