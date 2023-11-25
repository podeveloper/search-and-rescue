<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->date('date')->nullable();
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->string('location')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('organizer')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('event_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_place_id')->nullable()->constrained()->nullOnDelete();
            $table->string('google_calendar_event_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
