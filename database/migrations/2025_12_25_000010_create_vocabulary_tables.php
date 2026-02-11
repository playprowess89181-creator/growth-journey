<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('v_categories', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('v_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('v_categories')
                ->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->string('title', 255);
            $table->timestamps();

            $table->index('category_id');
            $table->index('language_code');
            $table->unique(['category_id', 'language_code']);
        });

        Schema::create('vocabulary_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('v_categories')
                ->cascadeOnDelete();
            $table->string('word_key', 120);
            $table->timestamps();

            $table->index('category_id');
            $table->unique(['category_id', 'word_key']);
        });

        Schema::create('v_word_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_id')
                ->constrained('vocabulary_words')
                ->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->string('word_text', 255)->default('');
            $table->text('meaning_text')->nullable();
            $table->timestamps();

            $table->index('word_id');
            $table->index('language_code');
            $table->unique(['word_id', 'language_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('v_word_translations');
        Schema::dropIfExists('vocabulary_words');
        Schema::dropIfExists('v_category_translations');
        Schema::dropIfExists('v_categories');
    }
};
