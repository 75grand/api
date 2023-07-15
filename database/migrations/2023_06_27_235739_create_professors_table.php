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
        Schema::create('professors', function (Blueprint $table) {
            $table->id();

            $table->string('name')->index();
            $table->string('email');
            $table->string('department')->nullable();

            $table->float('difficulty')->nullable();
            $table->float('rating')->nullable();
            $table->float('rating_count')->nullable();
            $table->integer('would_take_again')->nullable();
            $table->json('ratings_distribution')->nullable(); // e.g. { "1": 0, "2": 0, "3": 0, "4": 3, "5": 13 }

            $table->text('featured_review')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professors');
    }
};
