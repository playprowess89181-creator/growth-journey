@extends('layouts.admin')

@section('title', 'View Community Post')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $post->title }}</h1>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span>By {{ $post->user->name }}</span>
                        <span>•</span>
                        <span>{{ $post->created_at->format('M j, Y \a\t g:i A') }}</span>
                        <span>•</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </span>
                        @if($post->is_pinned)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                    <path fill-rule="evenodd" d="M3 8a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Pinned
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.community.posts.edit', $post) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 shadow-sm border border-blue-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Post
                    </a>
                    <a href="{{ route('admin.community.posts.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-sm border border-gray-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Posts
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Post Content Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    @if($post->image)
                        <div class="aspect-video overflow-hidden">
                            <img src="{{ asset('storage/' . $post->image) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                    
                    <div class="p-8">
                        <div class="prose prose-lg max-w-none">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">
                                Comments ({{ $post->comments_count ?? 0 }})
                            </h2>
                            <div class="flex gap-2">
                                <button onclick="bulkApproveComments()" 
                                        class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Approve Selected
                                </button>
                                <button onclick="bulkRejectComments()" 
                                        class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject Selected
                                </button>
                                <button onclick="bulkDeleteComments()" 
                                        class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200" id="comments-container">
                        @forelse($comments as $comment)
                            <div class="p-6 comment-item" data-comment-id="{{ $comment->id }}">
                                <div class="flex items-start space-x-4">
                                    <input type="checkbox" 
                                           class="comment-checkbox mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                           value="{{ $comment->id }}">
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
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
                                        
                                        <div class="mt-2">
                                            <p class="text-gray-700 text-sm leading-relaxed">{{ $comment->content }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">No comments yet</h3>
                                <p class="mt-2 text-gray-500">This post hasn't received any comments yet.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($comments->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $comments->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Post Info -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Post Information</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Community Group:</span>
                            <span class="font-semibold text-gray-900">{{ $post->communityGroup->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Author:</span>
                            <span class="font-semibold text-gray-900">{{ $post->user->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created:</span>
                            <span class="font-semibold text-gray-900">{{ $post->created_at->format('M j, Y') }}</span>
                        </div>
                        @if($post->published_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Published:</span>
                                <span class="font-semibold text-gray-900">{{ $post->published_at->format('M j, Y') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Comments:</span>
                            <span class="font-semibold text-gray-900">{{ $post->comments_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($post->is_published)
                            <form action="{{ route('admin.community.posts.unpublish', $post) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m-3.122-3.122l4.243 4.243"/>
                                    </svg>
                                    Unpublish
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.community.posts.publish', $post) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Publish
                                </button>
                            </form>
                        @endif

                        @if($post->is_pinned)
                            <form action="{{ route('admin.community.posts.unpin', $post) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    Unpin Post
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.community.posts.pin', $post) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                        <path fill-rule="evenodd" d="M3 8a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Pin Post
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.community.posts.destroy', $post) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Post
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

function bulkApproveComments() {
    const commentIds = getSelectedComments();
    if (commentIds.length === 0) {
        alert('Please select comments to approve');
        return;
    }

    fetch('/admin/community/comments/bulk-approve', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ comment_ids: commentIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error approving comments');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error approving comments');
    });
}

function bulkRejectComments() {
    const commentIds = getSelectedComments();
    if (commentIds.length === 0) {
        alert('Please select comments to reject');
        return;
    }

    fetch('/admin/community/comments/bulk-reject', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ comment_ids: commentIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error rejecting comments');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error rejecting comments');
    });
}

function bulkDeleteComments() {
    const commentIds = getSelectedComments();
    if (commentIds.length === 0) {
        alert('Please select comments to delete');
        return;
    }

    if (!confirm('Are you sure you want to delete the selected comments? This action cannot be undone.')) {
        return;
    }

    fetch('/admin/community/comments/bulk-delete', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ comment_ids: commentIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            commentIds.forEach(id => {
                document.querySelector(`[data-comment-id="${id}"]`).remove();
            });
        } else {
            alert('Error deleting comments');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting comments');
    });
}

// Select all functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add select all checkbox if there are comments
    const commentsContainer = document.getElementById('comments-container');
    const commentCheckboxes = document.querySelectorAll('.comment-checkbox');
    
    if (commentCheckboxes.length > 0) {
        const selectAllContainer = document.createElement('div');
        selectAllContainer.className = 'px-6 py-3 bg-gray-50 border-b border-gray-200';
        selectAllContainer.innerHTML = `
            <label class="flex items-center">
                <input type="checkbox" id="select-all-comments" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <span class="ml-2 text-sm text-gray-700">Select all comments</span>
            </label>
        `;
        
        commentsContainer.parentNode.insertBefore(selectAllContainer, commentsContainer);
        
        // Handle select all functionality
        const selectAllCheckbox = document.getElementById('select-all-comments');
        selectAllCheckbox.addEventListener('change', function() {
            commentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Update select all checkbox when individual checkboxes change
        commentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCount === commentCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < commentCheckboxes.length;
            });
        });
    }
});
</script>
@endsection