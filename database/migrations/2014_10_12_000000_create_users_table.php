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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar');

            $table->string('expo_token')->nullable();
            $table->string('macpass_number')->nullable();
            $table->smallInteger('class_year')->nullable();
            $table->string('position')->nullable(); // e.g. student, professor, staff

            $table->string('mailbox_combination')->nullable();
            $table->integer('mailbox_number')->nullable();

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
