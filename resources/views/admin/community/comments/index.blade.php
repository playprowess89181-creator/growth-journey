@extends('layouts.admin')

@section('title', 'Manage Comments')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Comments</h1>
                    <p class="text-gray-600">Review and moderate comments on community posts</p>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">Bulk Actions:</span>
                    <button onclick="bulkApprove()" 
                            class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Approve Selected
                    </button>
                    <button onclick="bulkReject()" 
                            class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-200 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reject Selected
                    </button>
                    <button onclick="bulkDelete()" 
                            class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Selected
                    </button>
                </div>
                <div class="text-sm text-gray-500">
                    <span id="selected-count">0</span> selected
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" action="{{ route('admin.community.comments.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Comments</label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by content or author..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Post Filter -->
                    <div>
                        <label for="post" class="block text-sm font-medium text-gray-700 mb-2">Post</label>
                        <select id="post" 
                                name="post" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Posts</option>
                            @foreach($posts as $post)
                                <option value="{{ $post->id }}" {{ request('post') == $post->id ? 'selected' : '' }}>
                                    {{ Str::limit($post->title, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
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

                    <!-- Author Filter -->
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
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.community.comments.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Comments List -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @if($comments->count() > 0)
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">
                            Comments ({{ $comments->total() }})
                        </h2>
                        <label class="flex items-center">
                            <input type="checkbox" id="select-all" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Select all</span>
                        </label>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($comments as $comment)
                        <div class="p-6 comment-item" data-comment-id="{{ $comment->id }}">
                            <div class="flex items-start space-x-4">
                                <input type="checkbox" 
                                       class="comment-checkbox mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                       value="{{ $comment->id }}">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-3">
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</h4>
                                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ $comment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $comment->is_approved ? 'Approved' : 'Pending' }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            @if(!$comment->is_approved)
                                                <button onclick="approveComment({{ $comment->id }})" 
                                                        class="text-green-600 hover:text-green-800 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <button onclick="rejectComment({{ $comment->id }})" 
                                                        class="text-yellow-600 hover:text-yellow-800 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            
                                            <a href="{{ route('admin.community.comments.edit', $comment) }}" 
                                               class="text-blue-600 hover:text-blue-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            
                                            <button onclick="deleteComment({{ $comment->id }})" 
                                                    class="text-red-600 hover:text-red-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <p class="text-gray-700 text-sm leading-relaxed">{{ $comment->content }}</p>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                                        <span>On post: 
                                            @if($comment->communityPost)
                                                @if($comment->communityPost->trashed())
                                                    <span class="font-medium text-gray-600">
                                                        {{ Str::limit($comment->communityPost->title, 50) }}
                                                    </span>
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                        Deleted
                                                    </span>
                                                @else
                                                    <a href="{{ route('admin.community.posts.show', $comment->communityPost) }}" 
                                                       class="text-purple-600 hover:text-purple-800 font-medium">
                                                        {{ Str::limit($comment->communityPost->title, 50) }}
                                                    </a>
                                                @endif
                                            @else
                                                <span class="font-medium text-gray-600">Post unavailable</span>
                                            @endif
                                        </span>
                                        <span>•</span>
                                        <span>Group: {{ $comment->communityPost?->communityGroup?->name ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($comments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $comments->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No comments found</h3>
                    <p class="mt-2 text-gray-500">No comments match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden forms for bulk actions -->
<form id="bulk-approve-form" action="{{ route('admin.community.comments.bulk-approve') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="comment_ids" id="bulk-approve-ids">
</form>

<form id="bulk-reject-form" action="{{ route('admin.community.comments.bulk-reject') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="comment_ids" id="bulk-reject-ids">
</form>

<form id="bulk-delete-form" action="{{ route('admin.community.comments.bulk-delete') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="comment_ids" id="bulk-delete-ids">
</form>

<script>
// Comment management functions
function approveComment(commentId) {
    fetch(`/admin/community/comments/${commentId}/approve`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error approving comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error approving comment');
    });
}

function rejectComment(commentId) {
    fetch(`/admin/community/comments/${commentId}/reject`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error rejecting comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error rejecting comment');
    });
}

function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
        return;
    }

    fetch(`/admin/community/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-comment-id="${commentId}"]`).remove();
            updateSelectedCount();
        } else {
            alert('Error deleting comment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting comment');
    });
}

function getSelectedComments() {
    const checkboxes = document.querySelectorAll('.comment-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function bulkApprove() {
    const commentIds = getSelectedComments();
    if (commentIds.length === 0) {
        alert('Please select comments to approve');
        return;
    }

    document.getElementById('bulk-approve-ids').value = JSON.stringify(commentIds);
    document.getElementById('bulk-approve-form').submit();
}

function bulkReject() {
    const commentIds = getSelectedComments();
    if (commentIds.length === 0) {
        alert('Please select comments to reject');
        return;
    }

    document.getElementById('bulk-reject-ids').value = JSON.stringify(commentIds);
    document.getElementById('bulk-reject-form').submit();
}

function bulkDelete() {
    const commentIds = getSelectedComments();
    if (commentIds.length === 0) {
        alert('Please select comments to delete');
        return;
    }

    if (!confirm('Are you sure you want to delete the selected comments? This action cannot be undone.')) {
        return;
    }

    document.getElementById('bulk-delete-ids').value = JSON.stringify(commentIds);
    document.getElementById('bulk-delete-form').submit();
}

function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.comment-checkbox:checked').length;
    document.getElementById('selected-count').textContent = selectedCount;
}

// Initialize page functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const commentCheckboxes = document.querySelectorAll('.comment-checkbox');
    
    // Handle select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            commentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Update select all checkbox when individual checkboxes change
    commentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedCount === commentCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < commentCheckboxes.length;
            }
            updateSelectedCount();
        });
    });
    
    // Initialize selected count
    updateSelectedCount();
});
</script>
@endsection
