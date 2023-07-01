<?php

use App\Models\Subject;
use App\Models\Term;
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('remote_id')->unique();

            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('attendance_required')->nullable();
            $table->bigInteger('crn');
            $table->string('number');

            $table->foreignIdFor(Term::class);
            $table->foreignIdFor(Subject::class);
            $table->string('sequence_number');
            $table->integer('credits');
            $table->string('location')->nullable(); // e.g. THEATR 120

            $table->integer('max_enrollment');
            $table->integer('enrollment');

            $table->string('professor')->nullable(); // e.g. Andrew Beveridge

            // You're welcome or I'm truly sorry, it could work out either way
            // https://www.notion.so/jeromepaulos/63dc008b0986498486dc58b263b69b41
            $table->json('prerequisites')->nullable();

            $table->json('days'); // e.g. [1, 3, 5]
            $table->string('start_time')->nullable(); // e.g. 13:00
            $table->string('end_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
