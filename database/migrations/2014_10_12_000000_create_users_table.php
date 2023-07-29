<?php

use App\Models\User;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('avatar');

            $table->string('referral_code')->nullable();
            $table->foreignIdFor(User::class, 'referrer_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('expo_token')->nullable();
            $table->string('class_year')->nullable();
            $table->string('position')->nullable(); // e.g. student, professor, staff

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
