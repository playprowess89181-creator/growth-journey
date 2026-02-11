<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Members</h2>
        <p class="text-sm text-gray-600">Track individual member progress and streaks.</p>
    </div>
</div>

<div class="overflow-x-auto border border-gray-100 rounded-xl">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Member Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Current Streak</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Completion % (this month)</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse ($members as $member)
                <tr class="hover:bg-indigo-50/40 transition">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                        <div class="text-sm text-gray-500">{{ $member->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $memberStats[$member->id]['streak'] ?? 0 }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $memberStats[$member->id]['overall'] ?? 0 }}%</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.track-habits.members.stats', [$group, $member]) }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700 transition">
                            View Stats
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-users text-gray-400"></i>
                        </div>
                        <p class="text-sm text-gray-500">No members in this group.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
