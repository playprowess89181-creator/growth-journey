@extends('layouts.admin')

@section('title', 'Report Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Report Details</h1>
                <p class="text-gray-600">Review reported dialogue comment</p>
            </div>
            <a href="{{ route('admin.dialogue.reports.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                Back
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $report->status_badge_color }}">
                    {{ ucfirst($report->status) }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $report->formatted_reason }}
                </span>
                <span class="text-xs text-gray-500">Reported {{ $report->created_at->diffForHumans() }}</span>
                @if($report->reviewed_at)
                    <span class="text-xs text-gray-500">Reviewed {{ $report->reviewed_at->diffForHumans() }}</span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm text-gray-600">Reported by</div>
                    <div class="text-lg font-bold text-gray-900">{{ $report->user->name }}</div>
                    <div class="text-sm text-gray-500">{{ $report->user->email ?? '' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Reviewed by</div>
                    <div class="text-lg font-bold text-gray-900">{{ $report->reviewer?->name ?? 'Not reviewed yet' }}</div>
                </div>
            </div>

            @if($report->description)
                <div class="mt-6">
                    <div class="text-sm font-semibold text-gray-900 mb-1">Reporter notes</div>
                    <div class="text-sm text-gray-800 leading-relaxed">{{ $report->description }}</div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <div class="text-sm font-semibold text-gray-900 mb-2">Reported comment</div>
            @php
                $comment = $report->reportable;
            @endphp
            <div class="text-sm text-gray-800 leading-relaxed">
                {{ $comment ? $comment->content : 'Comment not found' }}
            </div>
            @if($comment && $comment->relationLoaded('topic') && $comment->topic)
                <div class="mt-3 text-xs text-gray-500">
                    Topic: <span class="font-medium text-purple-700">{{ $comment->topic->title }}</span>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <form method="POST" action="{{ route('admin.dialogue.reports.update', $report) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            required>
                        @foreach(['pending','reviewed','resolved','dismissed'] as $s)
                            <option value="{{ $s }}" {{ old('status', $report->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">Admin notes (optional)</label>
                    <textarea id="admin_notes"
                              name="admin_notes"
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">{{ old('admin_notes', $report->admin_notes) }}</textarea>
                    @error('admin_notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('admin.dialogue.reports.destroy', $report) }}"
                       onclick="event.preventDefault(); if(confirm('Delete this report?')) document.getElementById('delete-report-form').submit();"
                       class="inline-flex items-center px-6 py-3 bg-red-100 text-red-700 font-semibold rounded-xl hover:bg-red-200 transition-all duration-200">
                        Delete Report
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Save
                    </button>
                </div>
            </form>
            <form id="delete-report-form" method="POST" action="{{ route('admin.dialogue.reports.destroy', $report) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>
@endsection
