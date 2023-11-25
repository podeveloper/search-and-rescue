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
        Schema::create('visitorables', function (Blueprint $table) {
            $table->unsignedBigInteger('visitor_id');
            $table->unsignedBigInteger('visitorable_id');
            $table->string('visitorable_type');
            $table->foreign('visitor_id')->references('id')->on('visitors')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitorables');
    }
};
