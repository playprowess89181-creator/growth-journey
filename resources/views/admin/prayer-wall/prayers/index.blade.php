@extends('layouts.admin')

@section('title', 'Prayer Interactions')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Prayer Interactions</h1>
                    <p class="text-gray-600">Review who prayed for which request</p>
                </div>
                <a href="{{ route('admin.prayer-wall.requests.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                    View Requests
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form method="GET" action="{{ route('admin.prayer-wall.prayers.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="prayer_request" class="block text-sm font-medium text-gray-700 mb-2">Request</label>
                        <select id="prayer_request"
                                name="prayer_request"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Requests</option>
                            @foreach($requests as $prayerRequest)
                                <option value="{{ $prayerRequest->id }}" {{ request('prayer_request') == $prayerRequest->id ? 'selected' : '' }}>
                                    {{ Str::limit($prayerRequest->title, 60) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                        <select id="author"
                                name="author"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">All Users</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3 md:self-end">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-filter mr-2"></i>
                            Apply
                        </button>
                        <a href="{{ route('admin.prayer-wall.prayers.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            @if($prayers->count() > 0)
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Prayers ({{ $prayers->total() }})</h2>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($prayers as $prayer)
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-6">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-800">
                                        <span class="font-semibold">{{ $prayer->user?->name ?? 'Unknown' }}</span>
                                        <span class="text-gray-500">prayed for</span>
                                        <a href="{{ route('admin.prayer-wall.requests.edit', $prayer->prayerRequest) }}"
                                           class="text-purple-700 hover:text-purple-900 font-semibold">
                                            {{ Str::limit($prayer->prayerRequest?->title ?? 'Unknown', 80) }}
                                        </a>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        {{ $prayer->created_at?->diffForHumans() ?? '' }}
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <button onclick="deletePrayer({{ $prayer->id }})"
                                            class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-colors duration-200 text-sm font-semibold">
                                        <i class="fas fa-trash mr-2"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($prayers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $prayers->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-heart text-4xl text-gray-300"></i>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No prayers found</h3>
                    <p class="mt-2 text-gray-500">No interactions match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deletePrayer(prayerId) {
    if (!confirm('Delete this prayer interaction?')) return;
    fetch(`/admin/prayer-wall/prayers/${prayerId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(() => window.location.reload());
}
</script>
@endsection

