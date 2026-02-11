@extends('layouts.admin')

@section('title', 'Member Stats')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.track-habits.show', $group) }}"
               class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                <span class="mr-2">←</span>
                Back to group
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $member->name }}</h1>
                        <p class="text-white/80 mt-1">Stats for {{ $group->name }}</p>
                    </div>
                    <div class="px-4 py-2 rounded-xl bg-white/15 text-white text-sm font-semibold">
                        {{ $monthLabel }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 lg:col-span-1">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Streak Control</h2>
                    <p class="text-sm text-gray-600 mt-1">Manage streak and recovery options.</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Current streak</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $currentStreak }}</p>
                        </div>
                        <span class="px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold">days</span>
                    </div>
                    <form method="POST" action="{{ route('admin.track-habits.members.freeze', [$group, $member]) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center justify-center w-full px-4 py-2 rounded-lg border border-indigo-200 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
                            {{ $isFrozenToday ? 'Unfreeze streak' : 'Freeze streak' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 lg:col-span-2">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Monthly Entries View</h2>
                            <p class="text-sm text-gray-600 mt-1">Daily habit completion status.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.track-habits.members.stats', [$group, $member]) }}?month={{ $prevMonth }}"
                               class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                                ← Prev
                            </a>
                            <span class="text-sm font-semibold text-gray-600">{{ $monthLabel }}</span>
                            <a href="{{ route('admin.track-habits.members.stats', [$group, $member]) }}?month={{ $nextMonth }}"
                               class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                                Next →
                            </a>
                            <form method="GET" action="{{ route('admin.track-habits.members.stats', [$group, $member]) }}" class="flex items-center gap-2">
                                <input type="month"
                                       name="month"
                                       value="{{ $selectedMonth }}"
                                       class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-semibold text-gray-600 focus:border-indigo-300 focus:ring focus:ring-indigo-200/60">
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg border border-indigo-200 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
                                    Go
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if ($habits->isEmpty())
                        <div class="text-center py-10">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-list-check text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-500">No habits to display.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($habits as $habit)
                                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $habit->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $habit->frequency_label }}</p>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-500">{{ $habitCompletion[$habit->id] ?? 0 }}% overall</span>
                                    </div>
                                    <div class="grid grid-cols-7 text-xs font-semibold text-gray-400">
                                        @foreach ($weekdays as $weekday)
                                            <div class="text-center">{{ $weekday }}</div>
                                        @endforeach
                                    </div>
                                    <div class="mt-2 grid grid-cols-7 gap-2">
                                        @foreach ($calendar[$habit->id] as $week)
                                            @foreach ($week as $cell)
                                                @if ($cell === null)
                                                    <div class="h-12"></div>
                                                @else
                                                    @php($status = $cell['status'] ?? null)
                                                    @php($cellClasses = $status === 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($status === 'fail' ? 'bg-rose-50 text-rose-700 border-rose-200' : ($status === 'freeze' ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-white text-gray-700 border-gray-200')))
                                                    @php($icon = $status === 'success' ? '✔' : ($status === 'fail' ? '✖' : ($status === 'freeze' ? '❄' : '')))
                                                    <div class="h-12 rounded-xl border flex items-center justify-center text-sm font-semibold {{ $cellClasses }}">
                                                        <div class="flex flex-col items-center leading-none">
                                                            <span>{{ $cell['day'] }}</span>
                                                            @if ($icon !== '')
                                                                <span class="text-[10px]">{{ $icon }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Completion Summary</h2>
                <p class="text-sm text-gray-600 mt-1">Habit-wise and overall completion rates.</p>
            </div>
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    @forelse ($habits as $habit)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $habit->name }}</p>
                                <p class="text-xs text-gray-500">{{ $habit->frequency_label }}</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ $habitCompletion[$habit->id] ?? 0 }}%</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No completion data available.</p>
                    @endforelse
                </div>
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-6 flex flex-col justify-center">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wider">Overall completion</p>
                    <p class="text-3xl font-bold text-indigo-700 mt-2">{{ $overallCompletion }}%</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
