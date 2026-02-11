<?php

namespace App\Http\Controllers\Admin\PrayerWall;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use App\Models\PrayerRequestComment;
use App\Models\User;
use Illuminate\Http\Request;

class PrayerCommentController extends Controller
{
    public function index(Request $request)
    {
        $query = PrayerRequestComment::query()->with(['prayerRequest', 'user']);

        if ($request->filled('search')) {
            $query->where('comment', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('prayer_request')) {
            $query->where('prayer_request_id', $request->prayer_request);
        }

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('author')) {
            $query->where('user_id', $request->author);
        }

        $comments = $query->orderByDesc('created_at')->paginate(15);

        $requests = PrayerRequest::query()
            ->orderByDesc('created_at')
            ->get(['id', 'title']);

        $authors = User::query()
            ->whereIn('id', PrayerRequestComment::query()->select('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.prayer-wall.comments.index', compact('comments', 'requests', 'authors'));
    }

    public function approve(PrayerRequestComment $comment)
    {
        $comment->update(['is_approved' => true]);

        return response()->json(['success' => true]);
    }

    public function reject(PrayerRequestComment $comment)
    {
        $comment->update(['is_approved' => false]);

        return response()->json(['success' => true]);
    }

    public function destroy(PrayerRequestComment $comment)
    {
        $comment->delete();

        return response()->json(['success' => true]);
    }
}
