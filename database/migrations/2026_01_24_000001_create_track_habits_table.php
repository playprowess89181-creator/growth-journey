<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('track_habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_group_id')->constrained('community_groups')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('frequency_type');
            $table->string('frequency_label');
            $table->json('weekdays')->nullable();
            $table->unsignedTinyInteger('times_per_week')->nullable();
            $table->unsignedInteger('xp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track_habits');
    }
};
