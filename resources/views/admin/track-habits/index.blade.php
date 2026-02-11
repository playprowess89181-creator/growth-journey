@extends('layouts.admin')

@section('title', 'Track Habits')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Track Habits
            </h1>
            <p class="mt-2 text-gray-600">Monitor habits across community groups</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-purple-50 opacity-60"></div>
                <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-indigo-500 to-purple-600"></div>
                <div class="relative p-6">
                    <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-2">Total Groups</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $groups->count() }}</p>
                </div>
            </div>
            <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 opacity-60"></div>
                <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-emerald-500 to-teal-600"></div>
                <div class="relative p-6">
                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-2">Active Habits</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalHabits }}</p>
                </div>
            </div>
            <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 opacity-60"></div>
                <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-amber-500 to-orange-600"></div>
                <div class="relative p-6">
                    <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-2">Tracked Members</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $groups->sum('members_count') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-users mr-3"></i>
                    Groups
                </h2>
                <span class="text-sm text-white/90">{{ $groups->count() }} total</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Group Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total Members</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Active Habits Count</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($groups as $group)
                            <tr class="hover:bg-indigo-50/40 transition">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $group->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $group->category ?? 'General' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ $group->members_count }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $group->track_habits_count }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.track-habits.show', $group) }}"
                                       class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700 transition">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-users text-gray-400"></i>
                                    </div>
                                    <p class="text-sm text-gray-500">No community groups available.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
