<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogue_topic_upvotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dialogue_topic_id')->constrained('dialogue_topics')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['dialogue_topic_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogue_topic_upvotes');
    }
};
