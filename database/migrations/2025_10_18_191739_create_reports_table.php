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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who reported
            $table->string('reportable_type'); // 'App\Models\CommunityPost' or 'App\Models\Comment'
            $table->unsignedBigInteger('reportable_id'); // ID of the reported item
            $table->string('reason'); // Reason for reporting (spam, inappropriate, etc.)
            $table->text('description')->nullable(); // Additional details from reporter
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who reviewed
            $table->text('admin_notes')->nullable(); // Admin notes on the report
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['reportable_type', 'reportable_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('status');
            $table->index('reviewed_by');

            // Prevent duplicate reports from same user for same item
            $table->unique(['user_id', 'reportable_type', 'reportable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
