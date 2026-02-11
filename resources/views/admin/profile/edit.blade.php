@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-purple-800 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-11 h-11 bg-white/20 rounded-xl">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white">Profile</h1>
                            <p class="text-white/80 text-sm">Manage your account information and security</p>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 bg-white/15 rounded-xl px-4 py-2">
                        <i class="fas fa-shield-alt text-white/90"></i>
                        <span class="text-white/90 text-sm font-medium">Admin Account</span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                            @include('admin.profile.partials.update-profile-information-form')
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                            @include('profile.partials.update-password-form')
                        </div>

                        <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-6">
                            @include('admin.profile.partials.delete-user-form')
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Summary</h2>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Name</span>
                                    <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Email</span>
                                    <span class="font-semibold text-gray-900">{{ $user->email }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Joined</span>
                                    <span class="font-semibold text-gray-900">{{ $user->created_at?->format('M j, Y') ?? '—' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h2>
                            <div class="space-y-2">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors border border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                                            <i class="fas fa-tachometer-alt text-indigo-600"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">Dashboard</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors border border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center">
                                            <i class="fas fa-users text-purple-600"></i>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">User Management</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
