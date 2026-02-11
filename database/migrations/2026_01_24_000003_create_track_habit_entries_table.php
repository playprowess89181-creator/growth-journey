<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('track_habit_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_group_id')->constrained('community_groups')->onDelete('cascade');
            $table->foreignId('track_habit_id')->constrained('track_habits')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('entry_date');
            $table->timestamps();

            $table->unique(['track_habit_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track_habit_entries');
    }
};
