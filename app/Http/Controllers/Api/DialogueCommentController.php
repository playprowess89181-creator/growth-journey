<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DialogueComment;
use App\Models\DialogueTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DialogueCommentController extends Controller
{
    public function index(Request $request, DialogueTopic $topic): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 50);
        $perPage = max(1, min(100, $perPage));

        $comments = DialogueComment::query()
            ->where('dialogue_topic_id', $topic->id)
            ->where('is_approved', true)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $data = collect($comments->items())->map(function (DialogueComment $comment) {
            return [
                'id' => $comment->id,
                'topic_id' => $comment->dialogue_topic_id,
                'content' => $comment->content,
                'is_approved' => (bool) $comment->is_approved,
                'user_id' => $comment->user_id,
                'created_at' => $comment->created_at?->toISOString(),
                'updated_at' => $comment->updated_at?->toISOString(),
                'user' => $comment->user,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    public function store(Request $request, DialogueTopic $topic): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($topic->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This topic is not available',
            ], 403);
        }

        $comment = DialogueComment::create([
            'dialogue_topic_id' => $topic->id,
            'user_id' => Auth::id(),
            'content' => $validator->validated()['content'],
            'is_approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment submitted for approval',
            'data' => [
                'id' => $comment->id,
                'topic_id' => $comment->dialogue_topic_id,
                'content' => $comment->content,
                'is_approved' => (bool) $comment->is_approved,
                'user_id' => $comment->user_id,
                'created_at' => $comment->created_at?->toISOString(),
                'updated_at' => $comment->updated_at?->toISOString(),
            ],
        ], 201);
    }
}
