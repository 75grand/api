<?php

use App\Models\Course;
use App\Models\DistRequirement;
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
        Schema::create('course_dist_requirement', function (Blueprint $table) {
            $table->foreignIdFor(Course::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(DistRequirement::class)->constrained()->cascadeOnDelete();
            $table->unique(['course_id', 'dist_requirement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_dist_requirement');
    }
};
