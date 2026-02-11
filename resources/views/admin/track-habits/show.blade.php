@extends('layouts.admin')

@section('title', 'Track Habits')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.track-habits.index') }}"
               class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                <span class="mr-2">←</span>
                Back to groups
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $group->name }}</h1>
                        <p class="text-white/80 mt-1">Track habits for this community group</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 rounded-xl bg-white/15 text-white text-sm font-semibold">
                            Members: {{ $group->members()->count() }}
                        </div>
                        <div class="px-4 py-2 rounded-xl bg-white/15 text-white text-sm font-semibold">
                            Active Habits: {{ $habitsCount }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ tab: 'habits' }" class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="border-b border-gray-100 px-6">
                <div class="flex flex-wrap gap-2 py-4">
                    <button type="button"
                            @click="tab = 'habits'"
                            :class="tab === 'habits' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:text-gray-900 border border-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Habits
                    </button>
                    <button type="button"
                            @click="tab = 'members'"
                            :class="tab === 'members' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:text-gray-900 border border-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Members
                    </button>
                    <button type="button"
                            @click="tab = 'stats'"
                            :class="tab === 'stats' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:text-gray-900 border border-gray-200'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Stats / Reports
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div x-show="tab === 'habits'" class="space-y-6">
                    @include('admin.track-habits.tabs.habits')
                </div>

                <div x-show="tab === 'members'" class="space-y-6">
                    @include('admin.track-habits.tabs.members')
                </div>

                <div x-show="tab === 'stats'" class="space-y-6">
                    @include('admin.track-habits.tabs.stats')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
