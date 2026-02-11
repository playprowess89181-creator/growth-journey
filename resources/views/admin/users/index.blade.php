@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    User Management
                </h1>
                <p class="mt-2 text-gray-600">Manage and monitor all users in your worship platform.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="group relative overflow-hidden bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 flex items-center gap-2">
                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                <i class="fas fa-plus relative z-10"></i>
                <span class="relative z-10">Add New User</span>
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div id="success-alert" class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 shadow-sm transition-all duration-500 ease-in-out">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
                <div class="ml-auto">
                    <button type="button" class="text-green-400 hover:text-green-600 transition-colors duration-200" onclick="dismissAlert('success-alert')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Actions Bar -->
    <div id="bulk-actions-bar" class="mb-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-4 shadow-sm hidden">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span id="selected-count" class="text-indigo-800 font-medium mr-4">0 users selected</span>
                <button type="button" onclick="clearSelection()" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    Clear Selection
                </button>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="bulkDelete()" class="group relative overflow-hidden bg-gradient-to-r from-red-500 to-pink-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                    <i class="fas fa-trash mr-2 relative z-10"></i>
                    <span class="relative z-10">Delete Selected</span>
                </button>
                <button type="button" onclick="bulkVerify()" class="group relative overflow-hidden bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                    <i class="fas fa-check-circle mr-2 relative z-10"></i>
                    <span class="relative z-10">Mark as Verified</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-users mr-3"></i>
                Users List
            </h3>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-4 px-4 font-semibold text-gray-700 bg-gray-50 rounded-tl-lg">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" onchange="toggleSelectAll()">
                            </th>
                            <th class="text-left py-4 px-4 font-semibold text-gray-700 bg-gray-50">ID</th>
                            <th class="text-left py-4 px-4 font-semibold text-gray-700 bg-gray-50">Name</th>
                            <th class="text-left py-4 px-4 font-semibold text-gray-700 bg-gray-50">Email</th>
                            <th class="text-left py-4 px-4 font-semibold text-gray-700 bg-gray-50">Status</th>
                            <th class="text-left py-4 px-4 font-semibold text-gray-700 bg-gray-50">Created At</th>
                            <th class="text-left py-4 px-4 font-semibold text-gray-700 bg-gray-50 rounded-tr-lg">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-4 px-4">
                                    <input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="{{ $user->id }}" onchange="syncUserCheckbox(this)">
                                </td>
                                <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $user->id }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-3">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="py-4 px-4">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 border border-amber-200">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Not Verified
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="group relative overflow-hidden bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-2 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                                            <i class="fas fa-eye text-sm relative z-10"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="group relative overflow-hidden bg-gradient-to-r from-amber-500 to-orange-600 text-white p-2 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                                            <i class="fas fa-edit text-sm relative z-10"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="group relative overflow-hidden bg-gradient-to-r from-red-500 to-pink-600 text-white p-2 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5"
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                                                <i class="fas fa-trash text-sm relative z-10"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-users text-2xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 text-lg font-medium">No users found</p>
                                        <p class="text-gray-400 text-sm mt-1">Users will appear here once they register</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden space-y-4">
                @forelse($users as $user)
                    <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-all duration-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-3" value="{{ $user->id }}" onchange="syncUserCheckbox(this)">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $user->name }}</h4>
                                    <p class="text-sm text-gray-600">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 border border-amber-200">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Not Verified
                                </span>
                            @endif
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                {{ $user->email }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                {{ $user->created_at->format('M d, Y') }}
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="flex-1 group relative overflow-hidden bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-2 px-3 rounded-lg text-center text-sm font-medium shadow-sm hover:shadow-md transition-all duration-200">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                                <i class="fas fa-eye mr-1 relative z-10"></i>
                                <span class="relative z-10">View</span>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="flex-1 group relative overflow-hidden bg-gradient-to-r from-amber-500 to-orange-600 text-white py-2 px-3 rounded-lg text-center text-sm font-medium shadow-sm hover:shadow-md transition-all duration-200">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                                <i class="fas fa-edit mr-1 relative z-10"></i>
                                <span class="relative z-10">Edit</span>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full group relative overflow-hidden bg-gradient-to-r from-red-500 to-pink-600 text-white py-2 px-3 rounded-lg text-center text-sm font-medium shadow-sm hover:shadow-md transition-all duration-200"
                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-200"></div>
                                    <i class="fas fa-trash mr-1 relative z-10"></i>
                                    <span class="relative z-10">Delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-lg font-medium">No users found</p>
                        <p class="text-gray-400 text-sm mt-1">Users will appear here once they register</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
                <div class="mt-8 flex justify-center">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-dismiss alert functionality
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            dismissAlert('success-alert');
        }, 5000);
    }
});

function dismissAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alert.remove();
        }, 500);
    }
}

// Bulk actions functionality
let selectedUsers = [];

function getUniqueUserIdsFromCheckboxes(checkboxes) {
    return [...new Set(Array.from(checkboxes).map(checkbox => checkbox.value))];
}

function syncUserCheckbox(sourceCheckbox) {
    const userCheckboxes = document.querySelectorAll('.user-checkbox');

    userCheckboxes.forEach(checkbox => {
        if (checkbox.value === sourceCheckbox.value) {
            checkbox.checked = sourceCheckbox.checked;
        }
    });

    updateBulkActions();
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    userCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const userCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const selectedCount = document.getElementById('selected-count');
    const selectAllCheckbox = document.getElementById('select-all');
    
    selectedUsers = getUniqueUserIdsFromCheckboxes(userCheckboxes);
    
    if (selectedUsers.length > 0) {
        bulkActionsBar.classList.remove('hidden');
        selectedCount.textContent = `${selectedUsers.length} user${selectedUsers.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActionsBar.classList.add('hidden');
    }
    
    // Update select-all checkbox state
    const allCheckboxes = document.querySelectorAll('.user-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    const uniqueAll = getUniqueUserIdsFromCheckboxes(allCheckboxes);
    const uniqueChecked = getUniqueUserIdsFromCheckboxes(checkedCheckboxes);
    
    if (uniqueChecked.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (uniqueChecked.length === uniqueAll.length) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    }
}

function clearSelection() {
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');
    
    userCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    selectAllCheckbox.checked = false;
    selectAllCheckbox.indeterminate = false;
    
    updateBulkActions();
}

function bulkDelete() {
    if (selectedUsers.length === 0) {
        alert('Please select users to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedUsers.length} selected user${selectedUsers.length > 1 ? 's' : ''}? This action cannot be undone.`)) {
        // Create a form to submit the bulk delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.users.bulk-delete") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add selected user IDs
        selectedUsers.forEach(userId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_ids[]';
            input.value = userId;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkVerify() {
    if (selectedUsers.length === 0) {
        alert('Please select users to verify.');
        return;
    }
    
    if (confirm(`Are you sure you want to mark ${selectedUsers.length} selected user${selectedUsers.length > 1 ? 's' : ''} as verified?`)) {
        // Create a form to submit the bulk verify request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.users.bulk-verify") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add selected user IDs
        selectedUsers.forEach(userId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_ids[]';
            input.value = userId;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
