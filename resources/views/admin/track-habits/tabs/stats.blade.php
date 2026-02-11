<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Stats / Reports</h2>
        <p class="text-sm text-gray-600">Group-level performance insights.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Average group completion</p>
            <div class="mt-3 flex items-center gap-3">
                <div class="text-3xl font-bold text-gray-900">{{ $averageCompletion }}%</div>
                <span class="text-xs font-semibold text-gray-500">{{ $monthLabel }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Monthly streak leaderboard</p>
            <div class="mt-4 space-y-3">
                @forelse ($leaderboard as $index => $entry)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 text-sm font-semibold flex items-center justify-center">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $entry['member']->name }}</p>
                                <p class="text-xs text-gray-500">Streak: {{ $entry['value'] }} days</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ $entry['value'] }} days</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No streak data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Top 5 consistent members</p>
            <div class="mt-4 space-y-3">
                @forelse ($consistentMembers as $index => $entry)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 text-sm font-semibold flex items-center justify-center">{{ $index + 1 }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $entry['member']->name }}</p>
                                <p class="text-xs text-gray-500">Completion: {{ $entry['value'] }}%</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ $entry['value'] }}%</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No completion data yet.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Most completed habits</p>
            <div class="mt-4 space-y-3">
                @forelse ($topHabits as $entry)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $entry['habit']->name }}</p>
                            <p class="text-xs text-gray-500">Completions: {{ $entry['count'] }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ $entry['percent'] }}%</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No habit completion data yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
