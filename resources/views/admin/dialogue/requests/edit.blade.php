@extends('layouts.admin')

@section('title', 'Review Topic Request')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Review Topic Request</h1>
                <p class="text-gray-600">Update status and share feedback with the user</p>
            </div>
            <a href="{{ route('admin.dialogue.topic-requests.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                Back
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-6 py-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <form method="POST" action="{{ route('admin.dialogue.topic-requests.update', $topicRequest) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <div class="px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-800">
                        {{ $topicRequest->title }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <div class="px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 whitespace-pre-line">
                        {{ $topicRequest->description ?: 'No description provided.' }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="status">Status</label>
                        <select id="status"
                                name="status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                required>
                            <option value="pending" {{ old('status', $topicRequest->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $topicRequest->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="declined" {{ old('status', $topicRequest->status) === 'declined' ? 'selected' : '' }}>Declined</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Submitted By</label>
                        <div class="px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-700">
                            {{ $topicRequest->user?->name ?? 'Unknown' }}
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="admin_feedback">Admin Feedback</label>
                    <textarea id="admin_feedback"
                              name="admin_feedback"
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                              placeholder="Share feedback for the user...">{{ old('admin_feedback', $topicRequest->admin_feedback) }}</textarea>
                    @error('admin_feedback')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="text-sm text-gray-500">
                        <span>Submitted: {{ $topicRequest->created_at->toDayDateTimeString() }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.dialogue.topic-requests.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
