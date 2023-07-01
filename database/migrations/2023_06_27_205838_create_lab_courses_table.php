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
        Schema::create('lab_courses', function (Blueprint $table) {
            $table->foreignIdFor(Course::class, 'main_course_id');//->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Course::class, 'lab_course_id');//->constrained()->cascadeOnDelete();
            $table->unique(['main_course_id', 'lab_course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_courses');
    }
};
