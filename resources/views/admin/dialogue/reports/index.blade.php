@extends('layouts.admin')

@section('title', 'Dialogue Reports')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dialogue Reports</h1>
                    <p class="text-gray-600">Reported dialogue comments and reasons</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[120px]">
                        <div class="text-2xl font-bold text-red-600">{{ $stats['pending_reports'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Pending</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[120px]">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['resolved_reports'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Resolved</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-4 min-w-[120px]">
                        <div class="text-2xl font-bold text-gray-600">{{ $stats['dismissed_reports'] ?? 0 }}</div>
                        <div class="text-sm text-gray-600">Dismissed</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" action="{{ route('admin.dialogue.reports.index') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                    </select>
                </div>
                <div class="flex gap-3 md:self-end md:justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Apply
                    </button>
                    <a href="{{ route('admin.dialogue.reports.index') }}"
                       class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Reports ({{ $reports->total() }})</h2>
            </div>

            @if($reports->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($reports as $report)
                        @php
                            $comment = $report->reportable;
                        @endphp
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-6">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-3 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->status_badge_color }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $report->formatted_reason }}
                                        </span>
                                        <span class="text-xs text-gray-500">Reported {{ $report->created_at->diffForHumans() }}</span>
                                    </div>

                                    <div class="text-sm text-gray-700 mb-2">
                                        <strong>Reported by:</strong> {{ $report->user->name }}
                                    </div>

                                    <div class="text-sm text-gray-900 leading-relaxed mb-2">
                                        <strong>Comment:</strong> {{ $comment ? Str::limit($comment->content, 240) : 'Comment not found' }}
                                    </div>

                                    @if($comment && $comment->relationLoaded('topic') && $comment->topic)
                                        <div class="text-xs text-gray-500">
                                            Topic: <span class="font-medium text-purple-700">{{ $comment->topic->title }}</span>
                                        </div>
                                    @endif

                                    @if($report->description)
                                        <div class="mt-2 text-sm text-gray-800">
                                            <strong>Reason details:</strong> {{ Str::limit($report->description, 240) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.dialogue.reports.show', $report) }}"
                                       class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-700 rounded-xl hover:bg-purple-200 transition-colors duration-200 text-sm font-semibold">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-flag text-4xl text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No reports found</h3>
                    <p class="mt-2 text-gray-500">There are currently no dialogue reports to review.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
