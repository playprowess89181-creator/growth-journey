<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <nav class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-600 via-purple-600 to-purple-800 shadow-2xl transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 flex flex-col overflow-hidden" id="sidebar">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-center h-16 px-6 py-3 bg-black/20 backdrop-blur-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 text-white hover:text-purple-200 transition-colors duration-200">
                    <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-pray text-lg"></i>
                    </div>
                    <span class="text-xl font-bold tracking-wide">Admin Panel</span>
                </a>
            </div>
            
            <!-- Navigation Links -->
            <div class="px-4 py-6 space-y-2 h-full overflow-y-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 text-white/90 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-3 text-white/90 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                    <i class="fas fa-users w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="font-medium">User Management</span>
                </a>
                
                <!-- Content Management Dropdown -->
                <div class="relative" x-data="{ open: {{ request()->routeIs('admin.modules.*') || request()->routeIs('admin.levels.*') || request()->routeIs('admin.lessons.*') || request()->routeIs('admin.vocabulary.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-3 text-white/90 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.modules.*') || request()->routeIs('admin.levels.*') || request()->routeIs('admin.lessons.*') || request()->routeIs('admin.vocabulary.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-book w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                            <span class="font-medium">Manage Content</span>
                        </div>
                        <i class="fas fa-chevron-down w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mt-2 ml-4 space-y-1">
                        <a href="{{ route('admin.modules.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.modules.*') || request()->routeIs('admin.levels.*') || request()->routeIs('admin.lessons.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-book-open w-4 h-4 mr-3"></i>
                            <span>Lessons</span>
                        </a>
                        <a href="{{ route('admin.vocabulary.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.vocabulary.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-language w-4 h-4 mr-3"></i>
                            <span>Vocabulary</span>
                        </a>
                    </div>
                </div>
                
                <!-- Community Dropdown -->
                <div class="relative" x-data="{ open: {{ request()->routeIs('admin.community.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="flex items-center justify-between w-full px-4 py-3 text-white/90 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.community.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-comments w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                            <span class="font-medium">Community</span>
                        </div>
                        <i class="fas fa-chevron-down w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mt-2 ml-4 space-y-1">
                        <a href="{{ route('admin.community.groups.index') }}" 
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.community.groups.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-users w-4 h-4 mr-3"></i>
                            <span>Community Groups</span>
                        </a>
                        <a href="{{ route('admin.community.posts.index') }}" 
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.community.posts.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-file-alt w-4 h-4 mr-3"></i>
                            <span>Community Posts</span>
                        </a>
                        <a href="{{ route('admin.community.comments.index') }}" 
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.community.comments.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-comments w-4 h-4 mr-3"></i>
                            <span>Comments</span>
                        </a>
                        <a href="{{ route('admin.community.reports.index') }}" 
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.community.reports.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-flag w-4 h-4 mr-3"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                </div>

                <div class="relative" x-data="{ open: {{ request()->routeIs('admin.dialogue.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-3 text-white/90 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.dialogue.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-handshake w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                            <span class="font-medium">Interfaith Dialogue</span>
                        </div>
                        <i class="fas fa-chevron-down w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mt-2 ml-4 space-y-1">
                        <a href="{{ route('admin.dialogue.topics.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.dialogue.topics.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-list w-4 h-4 mr-3"></i>
                            <span>Topics</span>
                        </a>
                        <a href="{{ route('admin.dialogue.topic-requests.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.dialogue.topic-requests.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-clipboard-list w-4 h-4 mr-3"></i>
                            <span>Topic Requests</span>
                        </a>
                        <a href="{{ route('admin.dialogue.comments.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.dialogue.comments.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-comment-dots w-4 h-4 mr-3"></i>
                            <span>Comments</span>
                        </a>
                        <a href="{{ route('admin.dialogue.reports.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.dialogue.reports.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-flag w-4 h-4 mr-3"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                </div>

                <div class="relative" x-data="{ open: {{ request()->routeIs('admin.prayer-wall.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-3 text-white/90 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.prayer-wall.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                        <div class="flex items-center">
                            <i class="fas fa-hands-praying w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                            <span class="font-medium">Prayer Wall</span>
                        </div>
                        <i class="fas fa-chevron-down w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mt-2 ml-4 space-y-1">
                        <a href="{{ route('admin.prayer-wall.requests.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.prayer-wall.requests.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-list w-4 h-4 mr-3"></i>
                            <span>Requests</span>
                        </a>
                        <a href="{{ route('admin.prayer-wall.comments.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.prayer-wall.comments.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-comment-dots w-4 h-4 mr-3"></i>
                            <span>Comments</span>
                        </a>
                        <a href="{{ route('admin.prayer-wall.prayers.index') }}"
                           class="flex items-center px-4 py-2 text-white/80 rounded-lg hover:bg-white/10 hover:text-white transition-all duration-200 text-sm {{ request()->routeIs('admin.prayer-wall.prayers.*') ? 'bg-white/10 text-white' : '' }}">
                            <i class="fas fa-heart w-4 h-4 mr-3"></i>
                            <span>Prayers</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.track-habits.index') }}" 
                   class="flex items-center px-4 py-3 text-white/90 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.track-habits.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                    <i class="fas fa-list-check w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="font-medium">Track Habits</span>
                </a>

                <a href="{{ route('admin.profile.edit') }}" 
                   class="flex items-center px-4 py-3 text-white/90 rounded-xl hover:bg-white/10 hover:text-white transition-all duration-200 group {{ request()->routeIs('admin.profile.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                    <i class="fas fa-user w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                    <span class="font-medium">Profile</span>
                </a>
            </div>
            
            <!-- Sidebar Footer -->
            <div class="p-4">
                <div class="border-t border-white/20 pt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-3 text-white/90 rounded-xl hover:bg-red-500/20 hover:text-white transition-all duration-200 group">
                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="ml-0 lg:ml-64 transition-all duration-300">
            <!-- Top Navigation Bar -->
            <header class="bg-white/95 backdrop-blur-lg shadow-lg border-b border-gray-200/50 sticky top-0 z-40">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Mobile Menu Button -->
                    <button type="button" 
                            class="lg:hidden inline-flex items-center justify-center p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 transition-all duration-200"
                            onclick="toggleSidebar()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Page Title -->
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                    </div>
                    
                    <!-- Right Side Navigation -->
                    <div class="flex items-center space-x-4">
                        
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-3 p-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-all duration-200 group">
                                <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">Administrator</div>
                                </div>
                                <i class="fas fa-chevron-down text-xs group-hover:rotate-180 transition-transform duration-200"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50">
                                <a href="{{ route('admin.profile.edit') }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                                    <i class="fas fa-user-edit w-4 h-4 mr-3"></i>
                                    Edit Profile
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" 
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden hidden"
         onclick="toggleSidebar()"></div>

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        // Initialize sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            // Ensure sidebar is closed on mobile devices by default
            if (window.innerWidth < 1024) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        });
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const menuButton = event.target.closest('[onclick="toggleSidebar()"]');
            
            if (!menuButton && !sidebar.contains(event.target) && !overlay.classList.contains('hidden')) {
                toggleSidebar();
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                // Ensure sidebar is closed on smaller screens
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
