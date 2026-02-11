@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">
                    Edit User
                </h1>
                <p class="mt-2 text-gray-600">Update user details and account status.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.users.show', $user) }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:via-purple-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-eye mr-2"></i>
                    View User
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Users
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-xl p-4 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-rose-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-800 font-semibold">Please fix the errors below.</p>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-id-card mr-3"></i>
                                Basic Information
                            </h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-user mr-2 text-indigo-500"></i>
                                        Full Name
                                    </label>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $user->name) }}"
                                           class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none transition-all duration-300 @error('name') border-red-500 bg-red-50 @enderror"
                                           required>
                                    @error('name')
                                        <div class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-envelope mr-2 text-indigo-500"></i>
                                        Email Address
                                    </label>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none transition-all duration-300 @error('email') border-red-500 bg-red-50 @enderror"
                                           required>
                                    @error('email')
                                        <div class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-shield-alt mr-2 text-indigo-500"></i>
                                        Role
                                    </label>
                                    <select id="role"
                                            name="role"
                                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none transition-all duration-300 @error('role') border-red-500 bg-red-50 @enderror"
                                            required>
                                        @php $roleValue = old('role', $user->role ?? 'user'); @endphp
                                        <option value="user" {{ $roleValue === 'user' ? 'selected' : '' }}>User</option>
                                        <option value="admin" {{ $roleValue === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    @error('role')
                                        <div class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="flex items-center justify-between bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-800">Email Verified</div>
                                        <div class="text-xs text-gray-500">Controls `email_verified_at`.</div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="hidden" name="email_verified" value="0">
                                        <input type="checkbox"
                                               name="email_verified"
                                               value="1"
                                               class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                               {{ old('email_verified', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">User ID</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-900">{{ $user->id }}</div>
                                </div>
                                <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-4">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Created / Updated</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ optional($user->created_at)->format('M d, Y H:i') ?? '—' }}
                                        <span class="text-gray-400 mx-1">•</span>
                                        {{ optional($user->updated_at)->format('M d, Y H:i') ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-5">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-lock mr-3"></i>
                                Security
                            </h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div class="text-sm text-gray-600">
                                Leave password fields empty to keep the current password.
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    New Password
                                </label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:bg-white focus:outline-none transition-all duration-300 @error('password') border-red-500 bg-red-50 @enderror"
                                       autocomplete="new-password">
                                @error('password')
                                    <div class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Confirm Password
                                </label>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:bg-white focus:outline-none transition-all duration-300"
                                       autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
                        <div class="flex flex-col gap-3">
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:via-purple-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>
                                Save Changes
                            </button>
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-400 to-gray-500 text-white font-semibold rounded-xl shadow-lg hover:from-gray-500 hover:to-gray-600 transform hover:scale-105 transition-all duration-300 text-center">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
