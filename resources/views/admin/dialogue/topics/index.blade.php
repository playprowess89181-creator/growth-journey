@extends('layouts.admin')

@section('title', 'Dialogue Topics')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Dialogue Topics</h1>
                    <p class="text-gray-600">Create and manage inter-religious dialogue topics</p>
                </div>
                <a href="{{ route('admin.dialogue.topics.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>
                    New Topic
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" action="{{ route('admin.dialogue.topics.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search topics..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-3 md:self-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-filter mr-2"></i>
                        Apply
                    </button>
                    <a href="{{ route('admin.dialogue.topics.index') }}"
                       class="inline-flex items-center px-6 py-2 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Topics ({{ $topics->total() }})</h2>
            </div>

            @if($topics->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($topics as $topic)
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-6">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-bold text-gray-900 truncate">{{ $topic->title }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $topic->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($topic->status) }}
                                        </span>
                                    </div>
                                    @if($topic->description)
                                        <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ Str::limit($topic->description, 220) }}</p>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                            {{ (int) ($topic->comments_count ?? 0) }} comments
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                            {{ (int) ($topic->approved_comments_count ?? 0) }} approved
                                        </span>
                                        <span>Created {{ $topic->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.dialogue.topics.edit', $topic) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition-colors duration-200 text-sm font-semibold">
                                        <i class="fas fa-pen mr-2"></i>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.dialogue.topics.destroy', $topic) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Delete this topic? This will also delete its comments.')"
                                                class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors duration-200 text-sm font-semibold">
                                            <i class="fas fa-trash mr-2"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $topics->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-list text-4xl text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No topics found</h3>
                    <p class="mt-2 text-gray-500">Try adjusting your filters or create a new topic.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
