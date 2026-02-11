<?php

namespace App\Http\Controllers\Admin\Dialogue;

use App\Http\Controllers\Controller;
use App\Models\DialogueTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DialogueTopicController extends Controller
{
    public function index(Request $request)
    {
        $query = DialogueTopic::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $topics = $query->withCount([
            'comments as comments_count',
            'approvedComments as approved_comments_count',
        ])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.dialogue.topics.index', compact('topics'));
    }

    public function create()
    {
        return view('admin.dialogue.topics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status' => 'required|in:active,inactive',
        ]);

        DialogueTopic::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.dialogue.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function edit(DialogueTopic $topic)
    {
        return view('admin.dialogue.topics.edit', compact('topic'));
    }

    public function update(Request $request, DialogueTopic $topic)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'status' => 'required|in:active,inactive',
        ]);

        $topic->update($validated);

        return redirect()->route('admin.dialogue.topics.index')
            ->with('success', 'Topic updated successfully.');
    }

    public function destroy(DialogueTopic $topic)
    {
        $topic->delete();

        return redirect()->route('admin.dialogue.topics.index')
            ->with('success', 'Topic deleted successfully.');
    }
}
