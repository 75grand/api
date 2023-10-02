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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('remote_id')->unique()->nullable();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('location')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->string('calendar_name');
            $table->string('url')->nullable();

            $table->boolean('checked_for_data')->default(false);
            $table->text('image_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
