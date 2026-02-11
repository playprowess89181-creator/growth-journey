@extends('layouts.admin')

@section('title', 'Topic Requests')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Topic Requests</h1>
                    <p class="text-gray-600">Review and respond to user-submitted dialogue topics</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" action="{{ route('admin.dialogue.topic-requests.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-4 items-end">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search topics..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-2 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl w-full sm:w-auto">
                            Apply
                        </button>
                        <a href="{{ route('admin.dialogue.topic-requests.index') }}"
                           class="inline-flex items-center justify-center px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 w-full sm:w-auto">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div x-data="{ activeTab: 'pending' }" class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex flex-wrap" aria-label="Tabs">
                    <button @click="activeTab = 'pending'"
                            class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200"
                            :class="{ 'border-purple-600 text-purple-600': activeTab === 'pending', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'pending' }">
                        Pending ({{ $requests['pending']->total() }})
                    </button>
                    <button @click="activeTab = 'approved'"
                            class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200"
                            :class="{ 'border-purple-600 text-purple-600': activeTab === 'approved', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'approved' }">
                        Approved ({{ $requests['approved']->total() }})
                    </button>
                    <button @click="activeTab = 'declined'"
                            class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200"
                            :class="{ 'border-purple-600 text-purple-600': activeTab === 'declined', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'declined' }">
                        Declined ({{ $requests['declined']->total() }})
                    </button>
                </nav>
            </div>

            @foreach(['pending' => 'pending', 'approved' => 'approved', 'declined' => 'declined'] as $statusKey => $statusLabel)
                <div x-show="activeTab === '{{ $statusKey }}'" x-cloak>
                    @if($requests[$statusKey]->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($requests[$statusKey] as $request)
                                <div class="p-4 sm:p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $request->title }}</h3>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : ($request->status === 'declined' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-gray-500 mb-3 flex flex-wrap items-center gap-2">
                                                <span class="font-medium text-gray-700">{{ $request->user?->name ?? 'Unknown' }}</span>
                                                <span class="hidden sm:inline">•</span>
                                                <span>{{ $request->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($request->description)
                                                <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ Str::limit($request->description, 220) }}</p>
                                            @endif
                                            @if($request->admin_feedback)
                                                <div class="mt-3 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                                                    <div class="text-xs font-semibold text-gray-500 mb-1">Admin feedback</div>
                                                    <div>{{ Str::limit($request->admin_feedback, 260) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                                            <a href="{{ route('admin.dialogue.topic-requests.edit', $request) }}"
                                               class="inline-flex items-center justify-center px-4 py-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition-colors duration-200 text-sm font-semibold w-full sm:w-auto">
                                                Review
                                            </a>
                                            <form method="POST" action="{{ route('admin.dialogue.topic-requests.destroy', $request) }}" class="w-full sm:w-auto">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Delete this request?')"
                                                        class="inline-flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors duration-200 text-sm font-semibold w-full sm:w-auto">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                            {{ $requests[$statusKey]->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="p-10 text-center">
                            <i class="fas fa-list text-4xl text-gray-300"></i>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No requests found</h3>
                            <p class="mt-2 text-gray-500">Try adjusting your search.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
