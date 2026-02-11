@extends('layouts.admin')

@section('title', 'Dialogue Comments')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dialogue Comments</h1>
                    <p class="text-gray-600">Approve, decline, and delete user-submitted comments</p>
                </div>
            </div>
        </div>

        @if($topics->count() > 0)
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($topics as $topic)
                        @php
                            $stats = $topicStats[$topic->id] ?? null;
                        @endphp
                        <div class="border border-gray-200 rounded-2xl p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $topic->title }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($topic->status) }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-purple-700">{{ (int) ($stats->comments_count ?? 0) }}</div>
                                    <div class="text-xs text-gray-500">comments</div>
                                </div>
                            </div>
                            <div class="mt-3 text-xs text-gray-600">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                    {{ (int) ($stats->users_count ?? 0) }} users
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" action="{{ route('admin.dialogue.comments.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search comment text..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>
                    <div>
                        <label for="topic" class="block text-sm font-medium text-gray-700 mb-2">Topic</label>
                        <select id="topic"
                                name="topic"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Topics</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ (string) request('topic') === (string) $topic->id ? 'selected' : '' }}>
                                    {{ Str::limit($topic->title, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status"
                                name="status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                        <select id="author"
                                name="author"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Users</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ (string) request('author') === (string) $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.dialogue.comments.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Comments ({{ $comments->total() }})</h2>
            </div>

            @if($comments->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($comments as $comment)
                        <div class="p-6" data-comment-id="{{ $comment->id }}">
                            <div class="flex items-start justify-between gap-6">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                        <div class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $comment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $comment->is_approved ? 'Approved' : 'Pending' }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-800 leading-relaxed mb-3">{{ $comment->content }}</div>
                                    <div class="text-xs text-gray-500">
                                        Topic:
                                        <span class="font-medium text-purple-700">{{ $comment->topic->title }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(!$comment->is_approved)
                                        <button onclick="updateComment({{ $comment->id }}, 'approve')"
                                                class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-xl hover:bg-green-200 transition-colors duration-200 text-sm font-semibold">
                                            Approve
                                        </button>
                                    @else
                                        <button onclick="updateComment({{ $comment->id }}, 'reject')"
                                                class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-xl hover:bg-yellow-200 transition-colors duration-200 text-sm font-semibold">
                                            Decline
                                        </button>
                                    @endif
                                    <button onclick="deleteComment({{ $comment->id }})"
                                            class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors duration-200 text-sm font-semibold">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $comments->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-comment-dots text-4xl text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No comments found</h3>
                    <p class="mt-2 text-gray-500">Try adjusting your filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateComment(commentId, action) {
    const method = 'PATCH';
    const url = action === 'approve'
        ? `/admin/dialogue/comments/${commentId}/approve`
        : `/admin/dialogue/comments/${commentId}/reject`;

    fetch(url, {
        method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    }).then(() => window.location.reload());
}

function deleteComment(commentId) {
    if (!confirm('Delete this comment?')) return;
    fetch(`/admin/dialogue/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    }).then(() => window.location.reload());
}
</script>
@endsection
