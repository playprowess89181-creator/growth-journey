<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommunityPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of comments.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Comment::with(['user', 'communityPost.communityGroup']);

        // Filter by search
        if ($request->filled('search')) {
            $query->where('content', 'like', '%'.$request->search.'%');
        }

        // Filter by post
        if ($request->filled('post_id')) {
            $query->where('community_post_id', $request->post_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Filter by author
        if ($request->filled('author_id')) {
            $query->where('user_id', $request->author_id);
        }

        $comments = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'community_post_id' => 'required|exists:community_posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['user_id'] = Auth::id();
        $validated['is_approved'] = true; // Auto-approve for now, can be changed based on requirements

        $comment = Comment::create($validated);
        $comment->load(['user', 'communityPost']);

        return response()->json([
            'success' => true,
            'message' => 'Comment created successfully',
            'data' => $comment,
        ], 201);
    }

    /**
     * Display the specified comment.
     */
    public function show(Comment $comment): JsonResponse
    {
        $comment->load(['user', 'communityPost.communityGroup']);

        return response()->json([
            'success' => true,
            'data' => $comment,
        ]);
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        // Check if user can update this comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this comment',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment->update($validator->validated());
        $comment->load(['user', 'communityPost']);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => $comment,
        ]);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        // Check if user can delete this comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this comment',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
        ]);
    }

    /**
     * Get comments for a specific post.
     */
    public function byPost(CommunityPost $post): JsonResponse
    {
        $comments = $post->comments()
            ->with('user')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Approve a comment.
     */
    public function approve(Comment $comment): JsonResponse
    {
        $comment->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Comment approved successfully',
            'data' => $comment,
        ]);
    }

    /**
     * Reject a comment.
     */
    public function reject(Comment $comment): JsonResponse
    {
        $comment->update(['is_approved' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Comment rejected successfully',
            'data' => $comment,
        ]);
    }

    /**
     * Bulk approve comments.
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $affectedCount = Comment::whereIn('id', $request->comment_ids)
            ->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => "Successfully approved {$affectedCount} comments",
        ]);
    }

    /**
     * Bulk reject comments.
     */
    public function bulkReject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $affectedCount = Comment::whereIn('id', $request->comment_ids)
            ->update(['is_approved' => false]);

        return response()->json([
            'success' => true,
            'message' => "Successfully rejected {$affectedCount} comments",
        ]);
    }

    /**
     * Bulk delete comments.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $affectedCount = Comment::whereIn('id', $request->comment_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$affectedCount} comments",
        ]);
    }
}
