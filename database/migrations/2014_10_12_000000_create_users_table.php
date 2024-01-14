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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('resume')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('password');
            $table->string('password_temp')->nullable();
            $table->foreignId('gender_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('nationality_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('education_level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_name')->nullable();
            $table->foreignId('reference_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('referral_source_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('occupation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organisation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('organisation_text')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('national_id_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('kvkk')->default(true);
            $table->longText('note')->nullable();
            $table->boolean('is_active')->default(false);
            $table->dateTime('official_membership_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
