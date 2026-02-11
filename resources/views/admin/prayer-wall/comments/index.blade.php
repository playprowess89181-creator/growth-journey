@extends('layouts.admin')

@section('title', 'Prayer Comments')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Prayer Comments</h1>
                    <p class="text-gray-600">Review and moderate comments on Prayer Wall requests</p>
                </div>
                <a href="{{ route('admin.prayer-wall.requests.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    View Requests
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" action="{{ route('admin.prayer-wall.comments.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by comment..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <div>
                        <label for="prayer_request" class="block text-sm font-medium text-gray-700 mb-2">Request</label>
                        <select id="prayer_request"
                                name="prayer_request"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Requests</option>
                            @foreach($requests as $prayerRequest)
                                <option value="{{ $prayerRequest->id }}" {{ request('prayer_request') == $prayerRequest->id ? 'selected' : '' }}>
                                    {{ Str::limit($prayerRequest->title, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status"
                                name="status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Statuses</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                        <select id="author"
                                name="author"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Authors</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.prayer-wall.comments.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @if($comments->count() > 0)
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Comments ({{ $comments->total() }})</h2>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($comments as $comment)
                        <div class="p-6 comment-item" data-comment-id="{{ $comment->id }}">
                            <div class="flex items-start justify-between gap-6">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-3">
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $comment->user?->name ?? 'Unknown' }}</h4>
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $comment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $comment->is_approved ? 'Approved' : 'Pending' }}
                                            </span>
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            @if(!$comment->is_approved)
                                                <button onclick="approveComment({{ $comment->id }})"
                                                        class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-xl hover:bg-green-200 transition-colors duration-200 text-sm font-semibold">
                                                    <i class="fas fa-check mr-2"></i>
                                                    Approve
                                                </button>
                                            @else
                                                <button onclick="rejectComment({{ $comment->id }})"
                                                        class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-700 rounded-xl hover:bg-yellow-200 transition-colors duration-200 text-sm font-semibold">
                                                    <i class="fas fa-xmark mr-2"></i>
                                                    Unapprove
                                                </button>
                                            @endif

                                            <button onclick="deleteComment({{ $comment->id }})"
                                                    class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors duration-200 text-sm font-semibold">
                                                <i class="fas fa-trash mr-2"></i>
                                                Delete
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <p class="text-gray-700 text-sm leading-relaxed">{{ $comment->comment }}</p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <span>On request:</span>
                                        <a href="{{ route('admin.prayer-wall.requests.edit', $comment->prayerRequest) }}"
                                           class="text-purple-600 hover:text-purple-800 font-medium">
                                            {{ Str::limit($comment->prayerRequest?->title ?? 'Unknown', 70) }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($comments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $comments->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-comment-dots text-4xl text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No comments found</h3>
                    <p class="mt-2 text-gray-500">No comments match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function approveComment(commentId) {
    fetch(`/admin/prayer-wall/comments/${commentId}/approve`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(() => window.location.reload());
}

function rejectComment(commentId) {
    fetch(`/admin/prayer-wall/comments/${commentId}/reject`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(() => window.location.reload());
}

function deleteComment(commentId) {
    if (!confirm('Delete this comment?')) return;
    fetch(`/admin/prayer-wall/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(() => window.location.reload());
}
</script>
@endsection

