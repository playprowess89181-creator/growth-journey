<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityQnaQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommunityQnaQuestionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 30);
        $perPage = max(1, min(100, $perPage));

        $questions = CommunityQnaQuestion::query()
            ->with('user:id,name')
            ->latest()
            ->paginate($perPage);

        $data = collect($questions->items())->map(function (CommunityQnaQuestion $question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'is_anonymous' => (bool) $question->is_anonymous,
                'user_id' => $question->user_id,
                'created_at' => $question->created_at?->toISOString(),
                'updated_at' => $question->updated_at?->toISOString(),
                'user' => $question->is_anonymous ? null : $question->user,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'per_page' => $questions->perPage(),
                'total' => $questions->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:2000',
            'is_anonymous' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $question = CommunityQnaQuestion::create([
            'user_id' => Auth::id(),
            'question' => $validated['question'],
            'is_anonymous' => (bool) ($validated['is_anonymous'] ?? false),
        ]);

        $question->load('user:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Question submitted successfully',
            'data' => [
                'id' => $question->id,
                'question' => $question->question,
                'is_anonymous' => (bool) $question->is_anonymous,
                'user_id' => $question->user_id,
                'created_at' => $question->created_at?->toISOString(),
                'updated_at' => $question->updated_at?->toISOString(),
                'user' => $question->is_anonymous ? null : $question->user,
            ],
        ], 201);
    }
}
