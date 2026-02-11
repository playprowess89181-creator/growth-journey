@extends('layouts.admin')

@section('title', 'Manage Reports')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Reports</h1>
                    <p class="text-gray-600">Review and moderate user reports on community content</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Statistics Cards -->
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[120px]">
                        <div class="text-2xl font-bold text-red-600">{{ $stats['pending'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Pending</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[120px]">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['resolved'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Resolved</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[120px]">
                        <div class="text-2xl font-bold text-gray-600">{{ $stats['dismissed'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Dismissed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">Bulk Actions:</span>
                    <button onclick="bulkUpdateStatus('reviewed')" 
                            class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mark as Reviewed
                    </button>
                    <button onclick="bulkUpdateStatus('resolved')" 
                            class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark as Resolved
                    </button>
                    <button onclick="bulkUpdateStatus('dismissed')" 
                            class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-200 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Dismiss Selected
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
            <form method="GET" action="{{ route('admin.community.reports.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Reports</label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by reason or description..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                        </select>
                    </div>

                    <!-- Content Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Content Type</label>
                        <select id="type" 
                                name="type" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Types</option>
                            <option value="App\Models\CommunityPost" {{ request('type') === 'App\Models\CommunityPost' ? 'selected' : '' }}>Posts</option>
                            <option value="App\Models\Comment" {{ request('type') === 'App\Models\Comment' ? 'selected' : '' }}>Comments</option>
                        </select>
                    </div>

                    <!-- Reason Filter -->
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                        <select id="reason" 
                                name="reason" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Reasons</option>
                            <option value="spam" {{ request('reason') === 'spam' ? 'selected' : '' }}>Spam</option>
                            <option value="inappropriate" {{ request('reason') === 'inappropriate' ? 'selected' : '' }}>Inappropriate Content</option>
                            <option value="harassment" {{ request('reason') === 'harassment' ? 'selected' : '' }}>Harassment</option>
                            <option value="misinformation" {{ request('reason') === 'misinformation' ? 'selected' : '' }}>Misinformation</option>
                            <option value="other" {{ request('reason') === 'other' ? 'selected' : '' }}>Other</option>
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
                    <a href="{{ route('admin.community.reports.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Reports List -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @if($reports->count() > 0)
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">
                            Reports ({{ $reports->total() }})
                        </h2>
                        <label class="flex items-center">
                            <input type="checkbox" id="select-all" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Select all</span>
                        </label>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($reports as $report)
                        <div class="p-6 report-item" data-report-id="{{ $report->id }}">
                            <div class="flex items-start space-x-4">
                                <input type="checkbox" 
                                       class="report-checkbox mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                       value="{{ $report->id }}">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->status_badge_color }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $report->reportable_type === 'App\\Models\\CommunityPost' ? 'Post' : 'Comment' }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $report->formatted_reason }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.community.reports.show', $report) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors duration-200 text-sm font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View Details
                                            </a>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Reported by:</strong> {{ $report->user->name }}
                                        </p>
                                        @if($report->description)
                                            <p class="text-sm text-gray-800">
                                                <strong>Description:</strong> {{ Str::limit($report->description, 200) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <div class="flex items-center space-x-4">
                                            <span>Reported {{ $report->created_at->diffForHumans() }}</span>
                                            @if($report->reviewed_at)
                                                <span>Reviewed {{ $report->reviewed_at->diffForHumans() }}</span>
                                            @endif
                                            @if($report->reviewer)
                                                <span>by {{ $report->reviewer->name }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($report->status === 'pending')
                                                <button onclick="updateReportStatus({{ $report->id }}, 'reviewed')" 
                                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                                    Mark as Reviewed
                                                </button>
                                                <span class="text-gray-300">|</span>
                                                <button onclick="updateReportStatus({{ $report->id }}, 'resolved')" 
                                                        class="text-green-600 hover:text-green-800 font-medium">
                                                    Resolve
                                                </button>
                                                <span class="text-gray-300">|</span>
                                                <button onclick="updateReportStatus({{ $report->id }}, 'dismissed')" 
                                                        class="text-yellow-600 hover:text-yellow-800 font-medium">
                                                    Dismiss
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $reports->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No reports found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request()->hasAny(['search', 'status', 'type', 'reason']))
                            Try adjusting your filters to see more results.
                        @else
                            There are currently no reports to review.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript for bulk actions and individual actions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const reportCheckboxes = document.querySelectorAll('.report-checkbox');
    const selectedCountSpan = document.getElementById('selected-count');

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            reportCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    // Individual checkbox functionality
    reportCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            
            // Update select all checkbox state
            if (selectAllCheckbox) {
                const checkedCount = document.querySelectorAll('.report-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCount === reportCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < reportCheckboxes.length;
            }
        });
    });

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.report-checkbox:checked').length;
        if (selectedCountSpan) {
            selectedCountSpan.textContent = checkedCount;
        }
    }
});

function getSelectedReportIds() {
    return Array.from(document.querySelectorAll('.report-checkbox:checked')).map(cb => cb.value);
}

function bulkUpdateStatus(status) {
    const selectedIds = getSelectedReportIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one report.');
        return;
    }

    if (confirm(`Are you sure you want to mark ${selectedIds.length} report(s) as ${status}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.community.reports.bulk-update") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);

        selectedIds.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'report_ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

function bulkDelete() {
    const selectedIds = getSelectedReportIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one report.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${selectedIds.length} report(s)? This action cannot be undone.`)) {
        selectedIds.forEach(id => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/community/reports/${id}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        });
    }
}

function updateReportStatus(reportId, status) {
    if (confirm(`Are you sure you want to mark this report as ${status}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/community/reports/${reportId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);

        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection