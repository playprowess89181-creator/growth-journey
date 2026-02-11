<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-lg font-semibold text-gray-900">Habits</h2>
        <p class="text-sm text-gray-600">Create and manage habits for this group.</p>
    </div>
    <a href="{{ route('admin.track-habits.habits.create', $group) }}"
       class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700 transition">
        Create
    </a>
</div>

<div class="overflow-x-auto border border-gray-100 rounded-xl">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Habit Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Frequency</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">XP</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse ($habits as $habit)
                <tr class="hover:bg-indigo-50/40 transition">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">{{ $habit->name }}</div>
                        @if (!empty($habit->description))
                            <div class="text-sm text-gray-500">{{ $habit->description }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $habit->frequency_label }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $habit->xp }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.track-habits.habits.edit', [$group, $habit->id]) }}"
                               class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-sm font-semibold text-gray-600 hover:text-gray-900">
                                Edit
                            </a>
                            <form action="{{ route('admin.track-habits.habits.destroy', [$group, $habit->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-50 text-sm font-semibold text-red-600 hover:text-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-list-check text-gray-400"></i>
                        </div>
                        <p class="text-sm text-gray-500">No habits created yet.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
