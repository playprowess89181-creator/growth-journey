<?php

namespace App\Http\Controllers\Admin\Dialogue;

use App\Http\Controllers\Controller;
use App\Models\DialogueTopic;
use App\Models\DialogueTopicRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DialogueTopicRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DialogueTopicRequest::query()->with('user');

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $requests = [
            'pending' => (clone $query)
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->paginate(15, ['*'], 'pending_page')
                ->withQueryString(),
            'approved' => (clone $query)
                ->where('status', 'approved')
                ->orderByDesc('created_at')
                ->paginate(15, ['*'], 'approved_page')
                ->withQueryString(),
            'declined' => (clone $query)
                ->where('status', 'declined')
                ->orderByDesc('created_at')
                ->paginate(15, ['*'], 'declined_page')
                ->withQueryString(),
        ];

        return view('admin.dialogue.requests.index', compact('requests'));
    }

    public function edit(DialogueTopicRequest $topicRequest)
    {
        return view('admin.dialogue.requests.edit', compact('topicRequest'));
    }

    public function update(Request $httpRequest, DialogueTopicRequest $topicRequest)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:pending,approved,declined',
            'admin_feedback' => 'nullable|string|max:2000',
        ]);

        $previousStatus = (string) $topicRequest->status;
        $newStatus = (string) $validated['status'];

        DB::transaction(function () use ($topicRequest, $validated, $previousStatus, $newStatus) {
            $topicRequest->update([
                'status' => $newStatus,
                'admin_feedback' => $validated['admin_feedback'] ?? null,
                'reviewed_by' => Auth::id(),
            ]);

            if ($previousStatus !== 'approved' && $newStatus === 'approved') {
                $alreadyExists = DialogueTopic::query()
                    ->where('title', $topicRequest->title)
                    ->where('created_by', $topicRequest->user_id)
                    ->exists();

                if (! $alreadyExists) {
                    DialogueTopic::create([
                        'title' => $topicRequest->title,
                        'description' => $topicRequest->description,
                        'status' => 'active',
                        'created_by' => $topicRequest->user_id,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.dialogue.topic-requests.edit', $topicRequest)
            ->with('success', 'Topic request updated.');
    }

    public function destroy(DialogueTopicRequest $topicRequest)
    {
        $topicRequest->delete();

        return redirect()
            ->route('admin.dialogue.topic-requests.index')
            ->with('success', 'Topic request deleted.');
    }
}
