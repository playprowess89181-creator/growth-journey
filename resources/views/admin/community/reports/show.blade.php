@extends('layouts.admin')

@section('title', 'Report Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <a href="{{ route('admin.community.reports.index') }}" 
                           class="inline-flex items-center text-purple-600 hover:text-purple-800 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Back to Reports
                        </a>
                        <span class="text-gray-400">|</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->status_badge_color }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Report Details</h1>
                    <p class="text-gray-600">Review and manage this user report</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Report Information -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Report Information</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Report ID</label>
                                <p class="text-sm text-gray-900">#{{ $report->id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->status_badge_color }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Content Type</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $report->reportable_type === 'App\\Models\\CommunityPost' ? 'Post' : 'Comment' }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $report->formatted_reason }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reported By</label>
                                <p class="text-sm text-gray-900">{{ $report->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $report->user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reported On</label>
                                <p class="text-sm text-gray-900">{{ $report->created_at->format('M d, Y \a\t g:i A') }}</p>
                                <p class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        @if($report->description)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-800">{{ $report->description }}</p>
                                </div>
                            </div>
                        @endif

                        @if($report->reviewed_at)
                            <div class="border-t pt-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Review Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Reviewed By</label>
                                        <p class="text-sm text-gray-900">{{ $report->reviewer->name ?? 'System' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Reviewed On</label>
                                        <p class="text-sm text-gray-900">{{ $report->reviewed_at->format('M d, Y \a\t g:i A') }}</p>
                                        <p class="text-xs text-gray-500">{{ $report->reviewed_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                @if($report->admin_notes)
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                                        <div class="bg-blue-50 rounded-lg p-4">
                                            <p class="text-sm text-gray-800">{{ $report->admin_notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Reported Content -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Reported Content</h2>
                    
                    @if($report->reportable_type === 'App\\Models\\CommunityPost')
                        <!-- Post Content -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $report->reportable->title }}</h3>
                                <span class="text-xs text-gray-500">
                                    Posted {{ $report->reportable->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <div class="prose prose-sm max-w-none">
                                <p class="text-gray-800">{{ Str::limit($report->reportable->content, 500) }}</p>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    By {{ $report->reportable->user->name }} in {{ $report->reportable->communityGroup->name ?? 'General' }}
                                </p>
                            </div>
                        </div>
                    @elseif($report->reportable_type === 'App\\Models\\Comment')
                        <!-- Comment Content -->
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900">Comment</h3>
                                <span class="text-xs text-gray-500">
                                    Posted {{ $report->reportable->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <div class="prose prose-sm max-w-none">
                                <p class="text-gray-800">{{ $report->reportable->content }}</p>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    By {{ $report->reportable->user->name }} on post "{{ Str::limit($report->reportable->communityPost?->title ?? 'Post unavailable', 50) }}"
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    
                    @if($report->status === 'pending')
                        <div class="space-y-3">
                            <button onclick="updateStatus('reviewed')" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Mark as Reviewed
                            </button>
                            <button onclick="updateStatus('resolved')" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Mark as Resolved
                            </button>
                            <button onclick="updateStatus('dismissed')" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Dismiss Report
                            </button>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <button onclick="deleteReport()" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Report
                        </button>
                    </div>
                </div>

                <!-- Add Admin Notes -->
                @if($report->status !== 'dismissed')
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Notes</h3>
                        
                        <form id="admin-notes-form" onsubmit="updateWithNotes(event)">
                            <div class="mb-4">
                                <textarea id="admin-notes" 
                                          name="admin_notes" 
                                          rows="4" 
                                          placeholder="Add notes about this report..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none">{{ $report->admin_notes }}</textarea>
                            </div>
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Save Notes
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Report Statistics -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Information</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Reporter's Total Reports</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $report->user->reports()->count() }}</span>
                        </div>
                        @if($report->reportable_type === 'App\\Models\\CommunityPost')
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Post Reports</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $report->reportable->reports()->count() }}</span>
                            </div>
                        @elseif($report->reportable_type === 'App\\Models\\Comment')
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Comment Reports</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $report->reportable->reports()->count() }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for actions -->
<script>
function updateStatus(status) {
    if (confirm(`Are you sure you want to mark this report as ${status}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.community.reports.update", $report) }}';
        
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

function updateWithNotes(event) {
    event.preventDefault();
    
    const adminNotes = document.getElementById('admin-notes').value;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.community.reports.update", $report) }}';
    
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

    const notesInput = document.createElement('input');
    notesInput.type = 'hidden';
    notesInput.name = 'admin_notes';
    notesInput.value = adminNotes;
    form.appendChild(notesInput);

    document.body.appendChild(form);
    form.submit();
}

function deleteReport() {
    if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.community.reports.destroy", $report) }}';
        
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
    }
}
</script>
@endsection
