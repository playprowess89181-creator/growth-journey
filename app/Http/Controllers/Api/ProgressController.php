<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Level;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function snapshot(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $completedLessonIds = DB::table('user_progress_items')
            ->where('user_id', $user->id)
            ->whereNotNull('lesson_id')
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->map(fn ($id) => (int) $id)
            ->values();

        $completedLevelIds = DB::table('user_progress_items')
            ->where('user_id', $user->id)
            ->whereNotNull('level_id')
            ->where('is_completed', true)
            ->pluck('level_id')
            ->map(fn ($id) => (int) $id)
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'completed_lesson_ids' => $completedLessonIds,
                'completed_level_ids' => $completedLevelIds,
            ],
        ], 200);
    }

    public function completeLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $now = now();
        $existingId = DB::table('user_progress_items')
            ->where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->value('id');

        if ($existingId) {
            DB::table('user_progress_items')
                ->where('id', $existingId)
                ->update([
                    'is_completed' => true,
                    'completed_at' => $now,
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('user_progress_items')->insert([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'level_id' => null,
                'is_completed' => true,
                'completed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'lesson_id' => $lesson->id,
                'is_completed' => true,
            ],
        ], 200);
    }

    public function completeLevel(Request $request, Level $level): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $now = now();
        $existingId = DB::table('user_progress_items')
            ->where('user_id', $user->id)
            ->where('level_id', $level->id)
            ->value('id');

        if ($existingId) {
            DB::table('user_progress_items')
                ->where('id', $existingId)
                ->update([
                    'is_completed' => true,
                    'completed_at' => $now,
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('user_progress_items')->insert([
                'user_id' => $user->id,
                'level_id' => $level->id,
                'lesson_id' => null,
                'is_completed' => true,
                'completed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'level_id' => $level->id,
                'is_completed' => true,
            ],
        ], 200);
    }
}
