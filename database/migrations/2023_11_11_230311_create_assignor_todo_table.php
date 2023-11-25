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
        Schema::create('assignor_todo', function (Blueprint $table) {
            $table->foreignId('assignor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('todo_id')->references('id')->on('todos')->onDelete('cascade');

            $table->unique(['assignor_id', 'todo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignor_todo');
    }
};
