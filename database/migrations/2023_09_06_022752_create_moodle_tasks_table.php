<?php

use App\Models\User;
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
        Schema::create('moodle_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('remote_id');

            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('class');
            $table->text('description')->nullable();

            $table->dateTime('due_date');
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['remote_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moodle_tasks');
    }
};
