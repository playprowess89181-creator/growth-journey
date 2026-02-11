<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityGroup;
use App\Models\CommunityPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CommunityPostController extends Controller
{
    /**
     * Display a listing of community posts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CommunityPost::with(['communityGroup', 'user', 'comments.user']);

        // Filter by search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('content', 'like', '%'.$request->search.'%');
        }

        // Filter by community group
        if ($request->filled('group_id')) {
            $query->where('community_group_id', $request->group_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'pinned') {
                $query->pinned();
            }
        }

        // Filter by author
        if ($request->filled('author_id')) {
            $query->where('user_id', $request->author_id);
        }

        $posts = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Store a newly created community post.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'community_group_id' => 'required|exists:community_groups,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'is_pinned' => 'boolean',
            'published_at' => 'nullable|date',
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

        // Handle boolean fields that might not be present when unchecked
        $validated['is_published'] = $request->has('is_published') ? (bool) $request->is_published : false;
        $validated['is_pinned'] = $request->has('is_pinned') ? (bool) $request->is_pinned : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('community-posts', 'public');
        }

        // Set published_at if publishing
        if ($validated['is_published'] && ! isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = CommunityPost::create($validated);
        $post->load(['communityGroup', 'user', 'comments']);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    /**
     * Display the specified community post.
     */
    public function show(CommunityPost $post): JsonResponse
    {
        $post->load(['communityGroup', 'user']);
        $post->loadCount('comments');

        $comments = $post->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => [
                'post' => $post,
                'comments' => $comments->items(),
                'comments_pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ],
            ],
        ]);
    }

    /**
     * Update the specified community post.
     */
    public function update(Request $request, CommunityPost $post): JsonResponse
    {
        // Check if user can update this post
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this post',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'community_group_id' => 'required|exists:community_groups,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'is_pinned' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle boolean fields that might not be present when unchecked
        $validated['is_published'] = $request->has('is_published') ? (bool) $request->is_published : false;
        $validated['is_pinned'] = $request->has('is_pinned') ? (bool) $request->is_pinned : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('community-posts', 'public');
        }

        // Set published_at if publishing for the first time
        if ($validated['is_published'] && ! $post->published_at && ! isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post->update($validated);
        $post->load(['communityGroup', 'user', 'comments']);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    /**
     * Remove the specified community post.
     */
    public function destroy(CommunityPost $post): JsonResponse
    {
        // Check if user can delete this post
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this post',
            ], 403);
        }

        // Delete image if exists
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }

    /**
     * Get posts by community group.
     */
    public function byGroup(CommunityGroup $communityGroup): JsonResponse
    {
        $posts = $communityGroup->posts()
            ->with(['user', 'comments.user'])
            ->published()
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Toggle publish status of a post.
     */
    public function togglePublish(CommunityPost $post): JsonResponse
    {
        // Check if user can update this post
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this post',
            ], 403);
        }

        $post->is_published = ! $post->is_published;

        if ($post->is_published && ! $post->published_at) {
            $post->published_at = now();
        }

        $post->save();

        return response()->json([
            'success' => true,
            'message' => $post->is_published ? 'Post published successfully' : 'Post unpublished successfully',
            'data' => $post,
        ]);
    }

    /**
     * Toggle pin status of a post.
     */
    public function togglePin(CommunityPost $post): JsonResponse
    {
        // Check if user can update this post
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this post',
            ], 403);
        }

        $post->is_pinned = ! $post->is_pinned;
        $post->save();

        return response()->json([
            'success' => true,
            'message' => $post->is_pinned ? 'Post pinned successfully' : 'Post unpinned successfully',
            'data' => $post,
        ]);
    }
}
