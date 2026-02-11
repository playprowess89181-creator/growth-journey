@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            Dashboard
        </h1>
        <p class="mt-2 text-gray-600">Welcome back! Here's what's happening with your worship platform.</p>
    </div>

    <!-- Statistics Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-50"></div>
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-500 to-indigo-600"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">
                            Total Users
                        </p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-50"></div>
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-green-500 to-emerald-600"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-2">
                            Active Users (30 days)
                        </p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users']) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-check text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Lessons Card -->
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-50 to-blue-50 opacity-50"></div>
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-cyan-500 to-blue-600"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-cyan-600 uppercase tracking-wider mb-2">
                            Total Lessons
                        </p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_lessons']) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-book text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Modules Card -->
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 opacity-50"></div>
            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-amber-500 to-orange-600"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-2">
                            Total Modules
                        </p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_modules']) }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-layer-group text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" x-data="{ open: false }">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-clock mr-3"></i>
                    Recent Activity
                </h3>
                @if ($remainingActivities->isNotEmpty())
                    <button type="button" @click="open = true" class="text-sm text-white/90 hover:text-white font-semibold">
                        View All
                    </button>
                @endif
            </div>
            <div class="p-6">
                @if ($recentActivities->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-lg">No recent activity to display</p>
                        <p class="text-gray-400 text-sm mt-2">Activity will appear here as users interact with your platform</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach ($recentActivities as $activity)
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-xl {{ $activity['bg'] }} flex items-center justify-center">
                                        <i class="fas {{ $activity['icon'] }} {{ $activity['color'] }}"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $activity['title'] }}</p>
                                        <span class="text-xs text-gray-500">{{ $activity['time']->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $activity['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($remainingActivities->isNotEmpty())
                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gray-900/60" @click="open = false"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4">
                        <div class="flex items-center justify-between px-6 py-4 border-b">
                            <h4 class="text-lg font-semibold text-gray-900">All Recent Activity</h4>
                            <button type="button" @click="open = false" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="p-6 max-h-[70vh] overflow-y-auto space-y-6">
                            @foreach ($remainingActivities as $activity)
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-xl {{ $activity['bg'] }} flex items-center justify-center">
                                            <i class="fas {{ $activity['icon'] }} {{ $activity['color'] }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $activity['title'] }}</p>
                                            <span class="text-xs text-gray-500">{{ $activity['time']->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $activity['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-bolt mr-3"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('admin.users.index') }}" 
                       class="group relative overflow-hidden bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="relative flex items-center justify-center flex-col text-center">
                            <i class="fas fa-users text-2xl mb-2"></i>
                            <span class="font-semibold">Manage Users</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.modules.index') }}" 
                       class="group relative overflow-hidden bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="relative flex items-center justify-center flex-col text-center">
                            <i class="fas fa-book text-2xl mb-2"></i>
                            <span class="font-semibold">Manage Content</span>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.community.groups.index') }}" 
                       class="group relative overflow-hidden bg-gradient-to-br from-cyan-500 to-blue-600 text-white rounded-xl p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                        <div class="relative flex items-center justify-center flex-col text-center">
                            <i class="fas fa-comments text-2xl mb-2"></i>
                            <span class="font-semibold">Community</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
