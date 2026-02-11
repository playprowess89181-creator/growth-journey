<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityGroup;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CommunityPost::with(['communityGroup', 'user', 'comments']);

        // Filter by search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('content', 'like', '%'.$request->search.'%');
        }

        // Filter by community group
        if ($request->filled('group')) {
            $query->byGroup($request->group);
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
        if ($request->filled('author')) {
            $query->where('user_id', $request->author);
        }

        $posts = $query->latest()->paginate(15);

        // Get filter options
        $groups = CommunityGroup::active()->orderBy('name')->get();
        $authors = CommunityPost::with('user')->get()->pluck('user')->unique('id');

        return view('admin.community.posts.index', compact('posts', 'groups', 'authors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = CommunityGroup::active()->orderBy('name')->get();

        return view('admin.community.posts.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'community_group_id' => 'required|exists:community_groups,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'is_pinned' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

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

        return redirect()->route('admin.community.posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CommunityPost $post)
    {
        $post->load(['communityGroup', 'user']);
        $post->loadCount('comments');

        $comments = $post->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.community.posts.show', compact('post', 'comments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommunityPost $post)
    {
        $groups = CommunityGroup::active()->orderBy('name')->get();

        return view('admin.community.posts.edit', compact('post', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommunityPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'community_group_id' => 'required|exists:community_groups,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'is_pinned' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

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

        return redirect()->route('admin.community.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommunityPost $post)
    {
        // Delete image if exists
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('admin.community.posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    public function publish(CommunityPost $post)
    {
        $post->update([
            'is_published' => true,
            'published_at' => $post->published_at ?? now(),
        ]);

        return back()->with('success', 'Post published successfully.');
    }

    public function unpublish(CommunityPost $post)
    {
        $post->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        return back()->with('success', 'Post unpublished successfully.');
    }

    public function pin(CommunityPost $post)
    {
        $post->update(['is_pinned' => true]);

        return back()->with('success', 'Post pinned successfully.');
    }

    public function unpin(CommunityPost $post)
    {
        $post->update(['is_pinned' => false]);

        return back()->with('success', 'Post unpinned successfully.');
    }

    /**
     * Bulk publish posts.
     */
    public function bulkPublish(Request $request)
    {
        Log::info('Bulk publish request received', ['data' => $request->all()]);

        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:community_posts,id',
        ]);

        $postIds = $request->post_ids;
        Log::info('Post IDs for bulk publish', ['post_ids' => $postIds]);

        $affectedCount = CommunityPost::whereIn('id', $postIds)
            ->update([
                'is_published' => true,
                'published_at' => now(),
            ]);

        Log::info('Bulk publish completed', ['affected_count' => $affectedCount]);

        return redirect()->route('admin.community.posts.index')
            ->with('success', "Successfully published {$affectedCount} posts.");
    }

    /**
     * Bulk unpublish posts.
     */
    public function bulkUnpublish(Request $request)
    {
        Log::info('Bulk unpublish request received', ['data' => $request->all()]);

        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:community_posts,id',
        ]);

        $postIds = $request->post_ids;
        Log::info('Post IDs for bulk unpublish', ['post_ids' => $postIds]);

        $affectedCount = CommunityPost::whereIn('id', $postIds)
            ->update(['is_published' => false]);

        Log::info('Bulk unpublish completed', ['affected_count' => $affectedCount]);

        return redirect()->route('admin.community.posts.index')
            ->with('success', "Successfully unpublished {$affectedCount} posts.");
    }

    /**
     * Bulk pin posts.
     */
    public function bulkPin(Request $request)
    {
        Log::info('Bulk pin request received', ['data' => $request->all()]);

        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:community_posts,id',
        ]);

        $postIds = $request->post_ids;
        Log::info('Post IDs for bulk pin', ['post_ids' => $postIds]);

        $affectedCount = CommunityPost::whereIn('id', $postIds)
            ->update(['is_pinned' => true]);

        Log::info('Bulk pin completed', ['affected_count' => $affectedCount]);

        return redirect()->route('admin.community.posts.index')
            ->with('success', "Successfully pinned {$affectedCount} posts.");
    }

    /**
     * Bulk unpin posts.
     */
    public function bulkUnpin(Request $request)
    {
        Log::info('Bulk unpin request received', ['data' => $request->all()]);

        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:community_posts,id',
        ]);

        $postIds = $request->post_ids;
        Log::info('Post IDs for bulk unpin', ['post_ids' => $postIds]);

        $affectedCount = CommunityPost::whereIn('id', $postIds)
            ->update(['is_pinned' => false]);

        Log::info('Bulk unpin completed', ['affected_count' => $affectedCount]);

        return redirect()->route('admin.community.posts.index')
            ->with('success', "Successfully unpinned {$affectedCount} posts.");
    }

    /**
     * Bulk delete posts.
     */
    public function bulkDelete(Request $request)
    {
        Log::info('Bulk delete request received', ['data' => $request->all()]);

        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:community_posts,id',
        ]);

        $postIds = $request->post_ids;
        Log::info('Post IDs for bulk delete', ['post_ids' => $postIds]);

        // Get posts to delete their images
        $posts = CommunityPost::whereIn('id', $postIds)->get();

        // Delete images
        foreach ($posts as $post) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
        }

        $affectedCount = CommunityPost::whereIn('id', $postIds)->delete();

        Log::info('Bulk delete completed', ['affected_count' => $affectedCount]);

        return redirect()->route('admin.community.posts.index')
            ->with('success', "Successfully deleted {$affectedCount} posts.");
    }
}
