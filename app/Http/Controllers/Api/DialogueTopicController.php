<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DialogueTopic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DialogueTopicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = (string) $request->query('status', 'active');
        if ($status === '') {
            $status = 'active';
        }

        $user = Auth::guard('sanctum')->user();

        $topics = DialogueTopic::query()
            ->when($status !== 'all', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->withCount(['approvedComments as approved_comments_count', 'upvotes as upvotes_count'])
            ->when($user, function ($q) use ($user) {
                $q->with(['upvotes' => function ($upvotes) use ($user) {
                    $upvotes->where('user_id', $user->id);
                }]);
            })
            ->orderByDesc('created_at')
            ->get()
            ->map(function (DialogueTopic $topic) use ($user) {
                return [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'description' => $topic->description,
                    'status' => $topic->status,
                    'approved_comments_count' => (int) ($topic->approved_comments_count ?? 0),
                    'upvotes_count' => (int) ($topic->upvotes_count ?? 0),
                    'has_upvoted' => $user ? $topic->upvotes->isNotEmpty() : false,
                    'created_at' => $topic->created_at?->toISOString(),
                    'updated_at' => $topic->updated_at?->toISOString(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $topics,
        ]);
    }

    public function show(Request $request, DialogueTopic $topic): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $topic->loadCount(['approvedComments as approved_comments_count', 'upvotes as upvotes_count']);
        if ($user) {
            $topic->load(['upvotes' => function ($upvotes) use ($user) {
                $upvotes->where('user_id', $user->id);
            }]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $topic->id,
                'title' => $topic->title,
                'description' => $topic->description,
                'status' => $topic->status,
                'approved_comments_count' => (int) ($topic->approved_comments_count ?? 0),
                'upvotes_count' => (int) ($topic->upvotes_count ?? 0),
                'has_upvoted' => $user ? $topic->upvotes->isNotEmpty() : false,
                'created_at' => $topic->created_at?->toISOString(),
                'updated_at' => $topic->updated_at?->toISOString(),
            ],
        ]);
    }

    public function toggleUpvote(Request $request, DialogueTopic $topic): JsonResponse
    {
        $user = $request->user();
        $existing = $topic->upvotes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $topic->upvotes()->create(['user_id' => $user->id]);
        }

        $topic->loadCount(['approvedComments as approved_comments_count', 'upvotes as upvotes_count']);
        $hasUpvoted = $topic->upvotes()->where('user_id', $user->id)->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $topic->id,
                'title' => $topic->title,
                'description' => $topic->description,
                'status' => $topic->status,
                'approved_comments_count' => (int) ($topic->approved_comments_count ?? 0),
                'upvotes_count' => (int) ($topic->upvotes_count ?? 0),
                'has_upvoted' => $hasUpvoted,
                'created_at' => $topic->created_at?->toISOString(),
                'updated_at' => $topic->updated_at?->toISOString(),
            ],
        ]);
    }
}
