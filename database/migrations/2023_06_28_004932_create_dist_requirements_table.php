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
        Schema::create('dist_requirements', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // e.g. Fine Arts Distribution
            $table->string('code')->unique(); // e.g. ARTS

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dist_requirements');
    }
};
