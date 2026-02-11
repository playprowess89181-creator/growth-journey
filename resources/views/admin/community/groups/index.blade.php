@extends('layouts.admin')

@section('title', 'Community Groups')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 overflow-y-auto">
    <!-- Page Header -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 sticky top-0 z-30">
        <div class="px-6 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        Community Groups
                    </h1>
                    <p class="text-gray-600 mt-1">Manage and organize community groups</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('admin.community.groups.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Group
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulk-actions-bar" class="hidden bg-blue-600 text-white px-6 py-4 sticky top-[120px] z-20">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <span id="selected-count" class="font-semibold">0 groups selected</span>
                <button type="button" id="select-all" class="text-blue-200 hover:text-white transition-colors">
                    Select All
                </button>
                <button type="button" id="deselect-all" class="text-blue-200 hover:text-white transition-colors">
                    Deselect All
                </button>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="submitBulkAction('activate')" 
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Activate
                </button>
                <button type="button" onclick="submitBulkAction('deactivate')" 
                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14L5 9m0 0l5-5m-5 5h14"/>
                    </svg>
                    Deactivate
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
            <form method="GET" action="{{ route('admin.community.groups.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search groups..." 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                    <a href="{{ route('admin.community.groups.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Groups Grid -->
        @if($groups->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($groups as $group)
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden hover:shadow-xl transform hover:scale-105 transition-all duration-300 relative">
                        <!-- Checkbox -->
                        <div class="absolute top-3 left-3 z-10">
                            <input type="checkbox" 
                                   class="group-checkbox w-5 h-5 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" 
                                   value="{{ $group->id }}" 
                                   onchange="updateBulkActions()">
                        </div>
                        
                        <!-- Group Image -->
                        <div class="h-48 bg-gradient-to-br from-purple-400 to-blue-500 relative overflow-hidden">
                            @if($group->image)
                                <img src="{{ Storage::url($group->image) }}" alt="{{ $group->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $group->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $group->status_label }}
                                </span>
                            </div>

                        </div>

                        <!-- Group Info -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $group->name }}</h3>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ $group->description ?: 'No description available.' }}
                            </p>

                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ ucfirst($group->category) }}
                                </span>
                                @if($group->max_members)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                        Max: {{ $group->max_members }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs text-gray-500 mb-4">
                                Created by {{ $group->creator->name ?? 'Unknown' }} • {{ $group->created_at->diffForHumans() }}
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.community.groups.show', $group) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('admin.community.groups.edit', $group) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-200 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                            </div>

                            <!-- Additional Actions -->
                            <div class="flex gap-2 mt-3">
                                <form action="{{ route('admin.community.groups.toggle-status', $group) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 {{ $group->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} text-sm font-medium rounded-lg transition-colors duration-200">
                                        @if($group->is_active)
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14L5 9m0 0l5-5m-5 5h14"/>
                                            </svg>
                                            Deactivate
                                        @else
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l5-5m0 0l-5-5m5 5H9"/>
                                            </svg>
                                            Activate
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('admin.community.groups.destroy', $group) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this group?')">
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
            @if($groups->hasPages())
                <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Pagination Info -->
                        <div class="text-sm text-gray-600">
                            Showing {{ $groups->firstItem() }} to {{ $groups->lastItem() }} of {{ $groups->total() }} results
                        </div>

                        <!-- Pagination Links -->
                        <div class="flex items-center space-x-2">
                            {{-- Previous Page Link --}}
                            @if ($groups->onFirstPage())
                                <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $groups->appends(request()->query())->previousPageUrl() }}" 
                                   class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-purple-600 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($groups->appends(request()->query())->getUrlRange(1, $groups->lastPage()) as $page => $url)
                                @if ($page == $groups->currentPage())
                                    <span class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" 
                                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-purple-600 transition-colors duration-200">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($groups->hasMorePages())
                                <a href="{{ $groups->appends(request()->query())->nextPageUrl() }}" 
                                   class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-purple-600 transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Community Groups Found</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first community group.</p>
                <a href="{{ route('admin.community.groups.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Your First Group
                </a>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="success-message">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('success-message').style.display = 'none';
        }, 5000);
    </script>
@endif

@if(session('error'))
    <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50" id="error-message">
        {{ session('error') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('error-message').style.display = 'none';
        }, 5000);
    </script>
@endif

<!-- Bulk Action Forms -->
<form id="bulk-activate-form" method="POST" action="{{ route('admin.community.groups.bulk-activate') }}" style="display: none;">
    @csrf
</form>

<form id="bulk-deactivate-form" method="POST" action="{{ route('admin.community.groups.bulk-deactivate') }}" style="display: none;">
    @csrf
</form>

<form id="bulk-delete-form" method="POST" action="{{ route('admin.community.groups.bulk-delete') }}" style="display: none;">
    @csrf
</form>

<script>
// Bulk actions functionality
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.group-checkbox:checked');
    const bulkBar = document.getElementById('bulk-actions-bar');
    const selectedCount = document.getElementById('selected-count');
    
    if (checkboxes.length > 0) {
        bulkBar.classList.remove('hidden');
        selectedCount.textContent = `${checkboxes.length} group${checkboxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkBar.classList.add('hidden');
    }
}

// Select all functionality
document.getElementById('select-all').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.group-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateBulkActions();
});

// Deselect all functionality
document.getElementById('deselect-all').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.group-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
});

// Submit bulk action
function submitBulkAction(action) {
    const checkboxes = document.querySelectorAll('.group-checkbox:checked');
    const groupIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (groupIds.length === 0) {
        alert('Please select at least one group.');
        return;
    }
    
    let confirmMessage = '';
    switch(action) {
        case 'activate':
            confirmMessage = `Are you sure you want to activate ${groupIds.length} group${groupIds.length > 1 ? 's' : ''}?`;
            break;
        case 'deactivate':
            confirmMessage = `Are you sure you want to deactivate ${groupIds.length} group${groupIds.length > 1 ? 's' : ''}?`;
            break;
        case 'delete':
            confirmMessage = `Are you sure you want to delete ${groupIds.length} group${groupIds.length > 1 ? 's' : ''}? This action cannot be undone.`;
            break;
    }
    
    if (confirm(confirmMessage)) {
        // Show loading state
        const bulkBar = document.getElementById('bulk-actions-bar');
        const originalContent = bulkBar.innerHTML;
        bulkBar.innerHTML = '<div class="flex items-center justify-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-white"></div><span class="ml-2">Processing...</span></div>';
        
        const form = document.getElementById(`bulk-${action}-form`);
        
        // Clear any existing hidden inputs
        const existingInputs = form.querySelectorAll('input[name="group_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        // Add each group ID as a separate input
        groupIds.forEach(id => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'group_ids[]';
            hiddenInput.value = id;
            form.appendChild(hiddenInput);
        });
        
        // Add error handling
        try {
            form.submit();
        } catch (error) {
            console.error('Form submission error:', error);
            bulkBar.innerHTML = originalContent;
            alert('An error occurred while processing your request. Please try again.');
        }
    }
}
</script>
@endsection
