<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-8 p-4 bg-green-500/20 border border-green-400/30 rounded-2xl backdrop-blur-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 mr-3 text-lg"></i>
                <span class="text-green-100 text-sm font-medium">{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-8">
        @csrf

        <!-- Email Address -->
        <div class="space-y-3">
            <label for="email" class="block text-sm font-semibold text-gray-200">
                <i class="fas fa-envelope mr-2 text-amber-400"></i>Email Address
            </label>
            <div class="relative group">
                <input id="email" 
                       class="w-full px-5 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 backdrop-blur-sm hover:bg-white/15 @error('email') border-red-400/50 focus:ring-red-400 @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="Enter your email address" />
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <i class="fas fa-at text-gray-400 group-focus-within:text-amber-400 transition-colors duration-300"></i>
                </div>
            </div>
            @error('email')
                <div class="flex items-center mt-2 text-sm text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-3">
            <label for="password" class="block text-sm font-semibold text-gray-200">
                <i class="fas fa-lock mr-2 text-amber-400"></i>Password
            </label>
            <div class="relative group">
                <input id="password" 
                       class="w-full px-5 py-4 pr-14 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300 backdrop-blur-sm hover:bg-white/15 @error('password') border-red-400/50 focus:ring-red-400 @enderror"
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password"
                       placeholder="Enter your password" />
                <button type="button" 
                        class="absolute inset-y-0 right-0 pr-4 flex items-center hover:scale-110 transition-transform duration-200"
                        onclick="togglePassword()">
                    <i id="password-icon" class="fas fa-eye text-gray-400 hover:text-amber-400 transition-colors duration-300 cursor-pointer"></i>
                </button>
            </div>
            @error('password')
                <div class="flex items-center mt-2 text-sm text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Login Button -->
        <div class="pt-4">
            <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 text-white font-bold py-4 px-6 rounded-2xl focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-300 transform hover:scale-105 hover:shadow-2xl shadow-lg group">
                <span class="flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-3 group-hover:mr-4 transition-all duration-300"></i>
                    Sign In to Dashboard
                </span>
            </button>
        </div>
    </form>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Enhanced form validation with visual feedback
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required]');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.add('border-red-400/50', 'focus:ring-red-400');
                        this.classList.remove('border-green-400/50', 'focus:ring-green-400');
                    } else {
                        this.classList.remove('border-red-400/50', 'focus:ring-red-400');
                        this.classList.add('border-green-400/50', 'focus:ring-green-400');
                    }
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('border-red-400/50')) {
                        this.classList.remove('border-red-400/50', 'focus:ring-red-400');
                    }
                });

                // Add floating label effect
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });

            // Add loading state to submit button
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Signing In...';
                submitBtn.disabled = true;
                
                // Re-enable after 3 seconds in case of error
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            });
        });
    </script>
</x-guest-layout>
