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
        Schema::table('calendar_events', function(Blueprint $table) {
            $table->renameColumn('checked_for_image', 'checked_for_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_events', function(Blueprint $table) {
            $table->renameColumn('checked_for_data', 'checked_for_image');
        });
    }
};
