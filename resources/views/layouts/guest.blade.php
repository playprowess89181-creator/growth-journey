<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Growth and Journey') }} - Admin Login</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-inter antialiased">
    <!-- Main Container with Worship Gradient Background -->
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 flex items-center justify-center p-4 py-24 relative overflow-hidden">
        
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- Floating Orbs - Worship Theme -->
            <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-gradient-to-r from-amber-400/15 to-yellow-400/15 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-3/4 right-1/4 w-96 h-96 bg-gradient-to-r from-blue-400/15 to-indigo-400/15 rounded-full blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-80 h-80 bg-gradient-to-r from-purple-400/15 to-blue-400/15 rounded-full blur-3xl animate-pulse delay-2000"></div>
            
            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="1.5"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>
        </div>
        
        <!-- Login Card -->
        <div class="relative z-10 w-full max-w-lg sm:max-w-xl md:max-w-2xl lg:max-w-xl">
            <!-- Glass Card Effect -->
            <div class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-2xl p-8 md:p-10">
                
                <!-- Logo Section -->
                <div class="text-center mb-10">
                    <!-- Logo Icon -->
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-amber-500 to-yellow-600 rounded-2xl mb-6 shadow-lg transform hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-praying-hands text-3xl text-white"></i>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="text-4xl font-black text-white mb-3 bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                        Growth and Journey
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-gray-300 text-lg font-medium">
                        Welcome back! Please sign in to continue.
                    </p>
                </div>
                
                <!-- Main Content -->
                <div class="space-y-6">
                    {{ $slot }}
                </div>
                
                <!-- Footer -->
                <div class="mt-10 pt-8 border-t border-white/10">
                    <div class="text-center">
                        <p class="text-sm text-gray-400 mb-4">
                            © {{ date('Y') }} Growth and Journey. All rights reserved.
                        </p>
                        <div class="flex justify-center items-center space-x-6 text-sm text-gray-400">
                            <span class="flex items-center hover:text-white transition-colors duration-200 cursor-pointer">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Secure Login
                            </span>
                            <span class="text-gray-600">•</span>
                            <span class="flex items-center hover:text-white transition-colors duration-200 cursor-pointer">
                                <i class="fas fa-lock mr-2"></i>
                                Protected
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute -top-4 -left-4 w-8 h-8 bg-gradient-to-r from-amber-400 to-yellow-400 rounded-full opacity-60 animate-bounce"></div>
            <div class="absolute -top-2 -right-6 w-6 h-6 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full opacity-60 animate-bounce delay-300"></div>
            <div class="absolute -bottom-4 -right-2 w-10 h-10 bg-gradient-to-r from-purple-400 to-indigo-400 rounded-full opacity-60 animate-bounce delay-700"></div>
        </div>
    </div>
</body>
</html>
