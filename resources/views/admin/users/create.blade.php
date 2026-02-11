@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent">
                    Create New User
                </h1>
                <p class="mt-2 text-gray-600">Add a new user to the system</p>
            </div>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Card -->
            <div class="lg:col-span-2">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-user-plus mr-3"></i>
                            User Information
                        </h2>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 sm:p-8">
                        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                            @csrf

                            <!-- Name Field -->
                            <div class="group">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user mr-2 text-blue-500"></i>
                                    Full Name
                                </label>
                                <input type="text" 
                                       class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all duration-300 @error('name') border-red-500 bg-red-50 @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Enter full name"
                                       required>
                                @error('name')
                                    <div class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="group">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                    Email Address
                                </label>
                                <input type="email" 
                                       class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all duration-300 @error('email') border-red-500 bg-red-50 @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Enter email address"
                                       required>
                                @error('email')
                                    <div class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="group">
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-blue-500"></i>
                                    Password
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           class="w-full px-4 py-3 pr-12 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all duration-300 @error('password') border-red-500 bg-red-50 @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter password"
                                           required>
                                    <button type="button" 
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                            onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-toggle-icon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="group">
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-blue-500"></i>
                                    Confirm Password
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           class="w-full px-4 py-3 pr-12 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all duration-300" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirm password"
                                           required>
                                    <button type="button" 
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                            onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-toggle-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 pt-6">
                                <a href="{{ route('admin.users.index') }}" 
                                   class="flex-1 sm:flex-none px-6 py-3 bg-gradient-to-r from-gray-400 to-gray-500 text-white font-semibold rounded-xl shadow-lg hover:from-gray-500 hover:to-gray-600 transform hover:scale-105 transition-all duration-300 text-center">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="flex-1 sm:flex-none px-8 py-3 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:via-purple-600 hover:to-indigo-600 transform hover:scale-105 transition-all duration-300">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        User Guidelines
                    </h3>
                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <p>Use a strong password with at least 8 characters</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <p>Email address must be unique and valid</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <p>Full name should include first and last name</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            <p>All fields are required for user creation</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Stats</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-3 rounded-lg text-center">
                                <div class="text-lg font-bold text-blue-600">{{ \App\Models\User::where('role', '!=', 'admin')->count() }}</div>
                                <div class="text-xs text-gray-600">Total Users</div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-3 rounded-lg text-center">
                                <div class="text-lg font-bold text-green-600">{{ \App\Models\User::where('role', '!=', 'admin')->whereDate('created_at', today())->count() }}</div>
                                <div class="text-xs text-gray-600">Today</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId + '-toggle-icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
