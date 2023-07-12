<?php

use App\Models\Course;
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
        Schema::create('cross_listings', function (Blueprint $table) {
            $table->foreignIdFor(Course::class, 'course_a')->constrained('courses')->cascadeOnDelete();
            $table->foreignIdFor(Course::class, 'course_b')->constrained('courses')->cascadeOnDelete();
            $table->unique(['course_a', 'course_b']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cross_listings');
    }
};
