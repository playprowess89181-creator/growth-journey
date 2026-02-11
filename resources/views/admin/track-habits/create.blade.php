@extends('layouts.admin')

@section('title', ($isEdit ?? false) ? 'Edit Habit' : 'Create Habit')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.track-habits.show', $group) }}"
               class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                <span class="mr-2">←</span>
                Back to habits
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-6">
                <h1 class="text-2xl font-bold text-white">{{ ($isEdit ?? false) ? 'Edit Habit' : 'Create Habit' }}</h1>
                <p class="text-white/80 mt-1">
                    {{ ($isEdit ?? false) ? "Update habit details for {$group->name}" : "Add a new habit for {$group->name}" }}
                </p>
            </div>

            @php
                $isEdit = $isEdit ?? false;
                $habitData = $habit ?? null;
                $frequencyValue = old('frequency', $habitData?->frequency_type ?? 'daily');
                $selectedWeekdays = old('weekdays', $habitData?->weekdays ?? []);
                $selectedWeekdays = is_array($selectedWeekdays) ? $selectedWeekdays : [];
                $timesPerWeek = old('times_per_week', $habitData?->times_per_week ?? 1);
            @endphp

            <form method="POST"
                  action="{{ $isEdit ? route('admin.track-habits.habits.update', [$group, $habitIndex]) : route('admin.track-habits.habits.store', $group) }}"
                  class="p-6 space-y-6"
                  x-data="{ frequency: '{{ $frequencyValue }}' }">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Habit Name</label>
                        <input type="text" name="habit_name" required value="{{ old('habit_name', $habitData?->name ?? '') }}"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Enter habit name">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Describe the habit">{{ old('description', $habitData?->description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Frequency</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-gray-50">
                                <input type="radio" name="frequency" value="daily" @checked($frequencyValue === 'daily')
                                       x-model="frequency" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-semibold text-gray-700">Every day</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white">
                                <input type="radio" name="frequency" value="specific" @checked($frequencyValue === 'specific')
                                       x-model="frequency" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-semibold text-gray-700">Specific days</span>
                            </label>
                            <div x-show="frequency === 'specific'" class="grid grid-cols-2 sm:grid-cols-4 gap-3 pl-2">
                                @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                                    <label class="flex items-center gap-2 text-sm text-gray-600">
                                        <input type="checkbox" name="weekdays[]" value="{{ $day }}"
                                               @checked(in_array($day, $selectedWeekdays, true))
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        {{ $day }}
                                    </label>
                                @endforeach
                            </div>
                            <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 bg-white">
                                <input type="radio" name="frequency" value="times" @checked($frequencyValue === 'times')
                                       x-model="frequency" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-semibold text-gray-700">Times per week</span>
                            </label>
                            <div x-show="frequency === 'times'" class="pl-2">
                                <select name="times_per_week"
                                        class="w-full sm:w-48 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                                    @for ($i = 1; $i <= 7; $i++)
                                        <option value="{{ $i }}" @selected((int) $timesPerWeek === $i)>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">XP Value per completion</label>
                        <input type="number" name="xp" min="1" max="1000" required value="{{ old('xp', $habitData?->xp ?? '') }}"
                               class="w-full sm:w-48 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g. 10">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                    <a href="{{ route('admin.track-habits.show', $group) }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:text-gray-900">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700">
                        {{ $isEdit ? 'Save Changes' : 'Create Habit' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
