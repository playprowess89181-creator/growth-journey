@extends('layouts.admin')

@section('title', 'Prayer Requests')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Prayer Requests</h1>
                    <p class="text-gray-600">Review and manage Prayer Wall requests</p>
                </div>
                <a href="{{ route('admin.prayer-wall.requests.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>
                    New Request
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-6 py-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" action="{{ route('admin.prayer-wall.requests.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search title or description..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category"
                            name="category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        <option value="all" {{ request('category', 'all') === 'all' ? 'selected' : '' }}>All</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
                    <select id="visibility"
                            name="visibility"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        <option value="all" {{ request('visibility', 'all') === 'all' ? 'selected' : '' }}>All</option>
                        <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Private</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="md:col-span-4 flex gap-3 md:justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.prayer-wall.requests.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Requests ({{ $requests->total() }})</h2>
            </div>

            @if($requests->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($requests as $request)
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-6">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-bold text-gray-900 truncate">{{ $request->title }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->is_public ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $request->is_public ? 'Public' : 'Private' }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed mb-3">{{ Str::limit($request->description, 220) }}</p>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                            Category: {{ ucfirst($request->category) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                            {{ (int) ($request->prayers_count ?? 0) }} prayers
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                            {{ (int) ($request->comments_count ?? 0) }} comments
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-50 border border-gray-200">
                                            {{ (int) ($request->approved_comments_count ?? 0) }} approved
                                        </span>
                                        <span>By {{ $request->user?->name ?? 'Unknown' }}</span>
                                        <span>•</span>
                                        <span>Created {{ $request->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.prayer-wall.requests.edit', $request) }}"
                                       class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition-colors duration-200 text-sm font-semibold">
                                        <i class="fas fa-pen mr-2"></i>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.prayer-wall.requests.destroy', $request) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Delete this prayer request? This will also delete its prayers and comments.')"
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
                    {{ $requests->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-hands-praying text-4xl text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No prayer requests found</h3>
                    <p class="mt-2 text-gray-500">Try adjusting your filters or create a new request.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

