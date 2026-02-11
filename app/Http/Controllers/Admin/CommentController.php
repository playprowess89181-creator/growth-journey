<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommunityPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Comment::with(['communityPost.communityGroup', 'user']);

        // Filter by search
        if ($request->filled('search')) {
            $query->where('content', 'like', '%'.$request->search.'%');
        }

        // Filter by post
        if ($request->filled('post')) {
            $query->byPost($request->post);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->approved();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        // Filter by author
        if ($request->filled('author')) {
            $query->where('user_id', $request->author);
        }

        $comments = $query->latest()->paginate(15);

        // Get filter options
        $posts = CommunityPost::with('communityGroup')->orderBy('title')->get();
        $authors = Comment::with('user')->get()->pluck('user')->unique('id');

        return view('admin.community.comments.index', compact('comments', 'posts', 'authors'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        $comment->load(['communityPost.communityGroup', 'user']);

        return view('admin.community.comments.show', compact('comment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        return view('admin.community.comments.edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'is_approved' => 'boolean',
        ]);

        $comment->update($validated);

        return redirect()->route('admin.community.comments.index')
            ->with('success', 'Comment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('admin.community.comments.index')
            ->with('success', 'Comment deleted successfully.');
    }

    /**
     * Approve a comment.
     */
    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', 'Comment approved successfully.');
    }

    /**
     * Reject/unapprove a comment.
     */
    public function reject(Comment $comment)
    {
        $comment->update(['is_approved' => false]);

        return redirect()->back()
            ->with('success', 'Comment rejected successfully.');
    }

    /**
     * Bulk approve comments.
     */
    public function bulkApprove(Request $request)
    {
        Log::info('Bulk approve request received', ['data' => $request->all()]);

        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        $commentIds = $request->comment_ids;
        Log::info('Comment IDs for bulk approve', ['comment_ids' => $commentIds]);

        $affectedCount = Comment::whereIn('id', $commentIds)
            ->update(['is_approved' => true]);

        Log::info('Bulk approve completed', ['affected_count' => $affectedCount]);

        return redirect()->route('admin.community.comments.index')
            ->with('success', "Successfully approved {$affectedCount} comments.");
    }

    /**
     * Bulk reject comments.
     */
    public function bulkReject(Request $request)
    {
        Log::info('Bulk reject request received', ['data' => $request->all()]);

        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        $commentIds = $request->comment_ids;
        Log::info('Comment IDs for bulk reject', ['comment_ids' => $commentIds]);

        $affectedCount = Comment::whereIn('id', $commentIds)
            ->update(['is_approved' => false]);

        Log::info('Bulk reject completed', ['affected_count' => $affectedCount]);

        return redirect()->route('admin.community.comments.index')
            ->with('success', "Successfully rejected {$affectedCount} comments.");
    }

    /**
     * Bulk delete comments.
     */
    public function bulkDelete(Request $request)
    {
        Log::info('Bulk delete request received', ['data' => $request->all()]);

        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
        ]);

        $commentIds = $request->comment_ids;
        Log::info('Comment IDs for bulk delete', ['comment_ids' => $commentIds]);

        $affectedCount = Comment::whereIn('id', $commentIds)->delete();

        Log::info('Bulk delete completed', ['affected_count' => $affectedCount]);

        return redirect()->route('admin.community.comments.index')
            ->with('success', "Successfully deleted {$affectedCount} comments.");
    }

    /**
     * Get comments for a specific post (AJAX endpoint).
     */
    public function getByPost(CommunityPost $post)
    {
        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }
}
