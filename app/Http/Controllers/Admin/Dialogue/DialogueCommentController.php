<?php

namespace App\Http\Controllers\Admin\Dialogue;

use App\Http\Controllers\Controller;
use App\Models\DialogueComment;
use App\Models\DialogueTopic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DialogueCommentController extends Controller
{
    public function index(Request $request)
    {
        $query = DialogueComment::query()->with(['topic', 'user']);

        if ($request->filled('search')) {
            $query->where('content', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('topic')) {
            $query->where('dialogue_topic_id', $request->topic);
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

        $topics = DialogueTopic::query()->orderByDesc('created_at')->get();
        $authors = User::query()
            ->whereIn('id', DialogueComment::query()->select('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);

        $topicStats = DialogueComment::query()
            ->select('dialogue_topic_id', DB::raw('count(*) as comments_count'), DB::raw('count(distinct user_id) as users_count'))
            ->groupBy('dialogue_topic_id')
            ->get()
            ->keyBy('dialogue_topic_id');

        return view('admin.dialogue.comments.index', compact('comments', 'topics', 'authors', 'topicStats'));
    }

    public function approve(DialogueComment $comment)
    {
        $comment->update(['is_approved' => true]);

        return response()->json(['success' => true]);
    }

    public function reject(DialogueComment $comment)
    {
        $comment->update(['is_approved' => false]);

        return response()->json(['success' => true]);
    }

    public function destroy(DialogueComment $comment)
    {
        $comment->delete();

        return response()->json(['success' => true]);
    }
}
