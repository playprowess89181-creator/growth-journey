@extends('layouts.admin')

@section('title', 'Community Posts')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 overflow-y-auto">
    <!-- Page Header -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 sticky top-0 z-30">
        <div class="px-6 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        Community Posts
                    </h1>
                    <p class="text-gray-600 mt-1">Manage posts from all community groups</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.community.posts.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Post
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulk-actions-bar" class="hidden bg-blue-600 text-white px-6 py-4 sticky top-[120px] z-20">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <span id="selected-count" class="font-semibold">0 posts selected</span>
                <button type="button" id="select-all" class="text-blue-200 hover:text-white transition-colors">
                    Select All
                </button>
                <button type="button" id="deselect-all" class="text-blue-200 hover:text-white transition-colors">
                    Deselect All
                </button>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="submitBulkAction('publish')" 
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Publish
                </button>
                <button type="button" onclick="submitBulkAction('unpublish')" 
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-5.197-5.197m0 0L5.636 5.636M18.364 18.364L12 18l-6.364-6.364"/>
                    </svg>
                    Unpublish
                </button>
                <button type="button" onclick="submitBulkAction('pin')" 
                        class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    Pin
                </button>
                <button type="button" onclick="submitBulkAction('unpin')" 
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    Unpin
                </button>
                <button type="button" onclick="submitBulkAction('delete')" 
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="px-6 py-6">
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 mb-6">
            <form method="GET" action="{{ route('admin.community.posts.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search posts..." 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Group Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Group</label>
                        <select name="group" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Groups</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pinned" {{ request('status') == 'pinned' ? 'selected' : '' }}>Pinned</option>
                        </select>
                    </div>

                    <!-- Author Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Author</label>
                        <select name="author" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Authors</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.community.posts.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Posts Grid -->
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($posts as $post)
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden hover:shadow-xl transform hover:scale-105 transition-all duration-300 relative">
                        <!-- Checkbox -->
                        <div class="absolute top-3 left-3 z-10">
                            <input type="checkbox" 
                                   class="post-checkbox w-5 h-5 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" 
                                   value="{{ $post->id }}" 
                                   onchange="updateBulkActions()">
                        </div>
                        
                        <!-- Post Image -->
                        <div class="h-48 bg-gradient-to-br from-purple-400 to-blue-500 relative overflow-hidden">
                            @if($post->image)
                                <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Status Badges -->
                            <div class="absolute top-3 right-3 flex flex-col gap-2">
                                @if($post->is_pinned)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500 text-white">
                                        Pinned
                                    </span>
                                @endif
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $post->is_published ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                    {{ $post->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>

                            <!-- Group Badge -->
                            <div class="absolute bottom-3 left-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm">
                                    {{ $post->communityGroup->name }}
                                </span>
                            </div>
                        </div>

                        <!-- Post Info -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $post->title }}</h3>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $post->excerpt }}
                            </p>

                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $post->user->name }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    {{ $post->comments_count }} comments
                                </span>
                            </div>

                            <div class="text-xs text-gray-500 mb-4">
                                @if($post->published_at)
                                    Published {{ $post->published_at->diffForHumans() }}
                                @else
                                    Created {{ $post->created_at->diffForHumans() }}
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.community.posts.show', $post) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('admin.community.posts.edit', $post) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-200 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                            </div>

                            <!-- Additional Actions -->
                            <div class="flex gap-2 mt-3">
                                @if($post->is_published)
                                    <form action="{{ route('admin.community.posts.unpublish', $post) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-200 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-5.197-5.197m0 0L5.636 5.636M18.364 18.364L12 18l-6.364-6.364"/>
                                            </svg>
                                            Unpublish
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.community.posts.publish', $post) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-lg hover:bg-green-200 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Publish
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.community.posts.destroy', $post) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $posts->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No posts found</h3>
                <p class="text-gray-600 mb-6">There are no community posts matching your criteria.</p>
                <a href="{{ route('admin.community.posts.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create First Post
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Bulk Action Forms -->
<form id="bulk-publish-form" action="{{ route('admin.community.posts.bulk-publish') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_ids" id="bulk-publish-ids">
</form>

<form id="bulk-unpublish-form" action="{{ route('admin.community.posts.bulk-unpublish') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_ids" id="bulk-unpublish-ids">
</form>

<form id="bulk-pin-form" action="{{ route('admin.community.posts.bulk-pin') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_ids" id="bulk-pin-ids">
</form>

<form id="bulk-unpin-form" action="{{ route('admin.community.posts.bulk-unpin') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_ids" id="bulk-unpin-ids">
</form>

<form id="bulk-delete-form" action="{{ route('admin.community.posts.bulk-delete') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_ids" id="bulk-delete-ids">
</form>

<script>
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.post-checkbox:checked');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const selectedCount = document.getElementById('selected-count');
    
    if (checkboxes.length > 0) {
        bulkActionsBar.classList.remove('hidden');
        selectedCount.textContent = `${checkboxes.length} post${checkboxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActionsBar.classList.add('hidden');
    }
}

function submitBulkAction(action) {
    const checkboxes = document.querySelectorAll('.post-checkbox:checked');
    const postIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (postIds.length === 0) {
        alert('Please select at least one post.');
        return;
    }
    
    let confirmMessage = '';
    let form = null;
    let idsInput = null;
    
    switch(action) {
        case 'publish':
            confirmMessage = `Are you sure you want to publish ${postIds.length} post${postIds.length > 1 ? 's' : ''}?`;
            form = document.getElementById('bulk-publish-form');
            idsInput = document.getElementById('bulk-publish-ids');
            break;
        case 'unpublish':
            confirmMessage = `Are you sure you want to unpublish ${postIds.length} post${postIds.length > 1 ? 's' : ''}?`;
            form = document.getElementById('bulk-unpublish-form');
            idsInput = document.getElementById('bulk-unpublish-ids');
            break;
        case 'pin':
            confirmMessage = `Are you sure you want to pin ${postIds.length} post${postIds.length > 1 ? 's' : ''}?`;
            form = document.getElementById('bulk-pin-form');
            idsInput = document.getElementById('bulk-pin-ids');
            break;
        case 'unpin':
            confirmMessage = `Are you sure you want to unpin ${postIds.length} post${postIds.length > 1 ? 's' : ''}?`;
            form = document.getElementById('bulk-unpin-form');
            idsInput = document.getElementById('bulk-unpin-ids');
            break;
        case 'delete':
            confirmMessage = `Are you sure you want to delete ${postIds.length} post${postIds.length > 1 ? 's' : ''}? This action cannot be undone.`;
            form = document.getElementById('bulk-delete-form');
            idsInput = document.getElementById('bulk-delete-ids');
            break;
    }
    
    if (confirm(confirmMessage)) {
        idsInput.value = JSON.stringify(postIds);
        form.submit();
    }
}

document.getElementById('select-all').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.post-checkbox');
    checkboxes.forEach(cb => cb.checked = true);
    updateBulkActions();
});

document.getElementById('deselect-all').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.post-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    updateBulkActions();
});
</script>
@endsection
