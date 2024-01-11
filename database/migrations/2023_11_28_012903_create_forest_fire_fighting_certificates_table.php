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
        Schema::create('forest_fire_fighting_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number');
            $table->foreignId('work_area_city_id')->nullable();
            $table->string('directorate')->nullable();
            $table->string('duty')->nullable();
            $table->string('pdf')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forest_fire_fighting_certificates');
    }
};
