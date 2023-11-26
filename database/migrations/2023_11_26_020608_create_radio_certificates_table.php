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
        Schema::create('radio_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('call_sign');
            $table->string('licence_number')->nullable();
            $table->string('licence_class')->nullable();
            $table->date('date_of_issue')->nullable();
            $table->date('expiration_date')->nullable();
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
        Schema::dropIfExists('radio_certificates');
    }
};
