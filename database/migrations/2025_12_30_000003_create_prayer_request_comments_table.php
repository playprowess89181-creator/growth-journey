<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('prayer_request_comments');

        Schema::create('prayer_request_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prayer_request_id')->constrained('prayer_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index(['prayer_request_id', 'is_approved', 'created_at'], 'prc_req_appr_created_idx');
            $table->index(['user_id', 'created_at'], 'prc_user_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_request_comments');
    }
};
