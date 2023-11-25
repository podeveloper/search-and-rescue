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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('title');
            $table->text('text')->nullable();
            $table->softDeletes();
        });

        Schema::create('certificate_user', function (Blueprint $table) {
            $table->unsignedBigInteger('certificate_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('certificate_id')->references('id')->on('certificates')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['certificate_id','user_id']);

            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('issuer')->nullable();
            $table->string('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_user');
        Schema::dropIfExists('certificates');
    }
};
