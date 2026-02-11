<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_request_prayers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prayer_request_id')->constrained('prayer_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['prayer_request_id', 'user_id']);
            $table->index(['prayer_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_request_prayers');
    }
};
