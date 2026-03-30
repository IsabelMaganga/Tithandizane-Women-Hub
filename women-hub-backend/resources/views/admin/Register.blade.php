<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - Tithandizane Women Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles to enhance the golden hour glow and overlay effects */
        .golden-overlay {
            background: linear-gradient(135deg, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.3) 100%);
        }
        .register-card {
            backdrop-filter: blur(2px);
            background-color: rgba(255, 255, 255, 0.95);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .register-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
        .input-focus-glow:focus {
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }
        .eye-button {
            cursor: pointer;
            transition: opacity 0.2s ease;
        }
        .eye-button:hover {
            opacity: 0.7;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center relative"
      style="background-image: url('{{ asset('images/background.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">

    <!-- Overlay to enhance readability and add warmth -->
    <div class="absolute inset-0 golden-overlay"></div>

    <!-- Main container with responsive padding -->
    <div class="relative z-10 w-full max-w-md px-4 sm:px-0">
        
        <!-- Register Card with earthy/terracotta accents -->
        <div class="register-card rounded-xl shadow-2xl overflow-hidden">
            
            <!-- Decorative top bar - deep purple/indigo from UI spec -->
            <div class="h-2 bg-gradient-to-r from-indigo-800 via-purple-800 to-indigo-900"></div>
            
            <!-- Card Content -->
            <div class="p-6 sm:p-8">
                
                <!-- Header with women empowerment icon -->
                <div class="text-center mb-8">
                    <div class="inline-block p-3 rounded-full bg-gradient-to-br from-yellow-500/20 to-amber-600/20 mb-3">
                        <!-- Women Empowerment Icon - Two women/figure silhouette representing community -->
                        <svg class="w-12 h-12 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800" style="text-shadow: 0 2px 4px rgba(0,0,0,0.05);">Admin Register</h1>
                    <p class="text-amber-700 mt-2 font-medium">Tithandizane Women Hub</p>
                    <div class="w-24 h-1 bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-600 mx-auto mt-3 rounded-full"></div>
                </div>

                {{-- Success Message --}}
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6 shadow-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-6 shadow-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Please fix the following errors:</span>
                        </div>
                        <ul class="mt-1 ml-6 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.register.post') }}">
                    @csrf

                    <!-- Full Name -->
                    <div class="mb-5">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Full Name
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Chisomo Phiri"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent input-focus-glow transition duration-200"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-5">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                            </div>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="admin@tithandizane.mw"
                                class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent input-focus-glow transition duration-200"
                                required
                            >
                        </div>
                    </div>

                    <!-- Password with Eye Toggle -->
                    <div class="mb-5">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Minimum 8 characters"
                                class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent input-focus-glow transition duration-200"
                                required
                            >
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center eye-button">
                                <svg id="passwordEyeIcon" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Password Strength -->
                        <div class="mt-2">
                            <div class="h-2 bg-gray-200 rounded overflow-hidden">
                                <div id="strength-bar" class="h-2 rounded w-0 transition-all duration-300"></div>
                            </div>
                            <p id="strength-text" class="text-xs mt-1 text-gray-500"></p>
                        </div>
                    </div>

                    <!-- Confirm Password with Eye Toggle -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="confirm_password"
                                placeholder="Re-enter password"
                                class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent input-focus-glow transition duration-200"
                                required
                            >
                            <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center eye-button">
                                <svg id="confirmEyeIcon" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p id="match-text" class="text-xs mt-1"></p>
                    </div>

                    <!-- Register Button - Using deep red/maroon from clothing colors -->
                    <div class="mb-6">
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-maroon-700 to-red-800 text-white font-bold py-2.5 px-4 rounded-lg hover:from-maroon-800 hover:to-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 transform hover:scale-[1.02] shadow-md"
                            style="background-color: #7f1a1a; background-image: linear-gradient(135deg, #7f1a1a 0%, #991b1b 100%);"
                        >
                            Register Admin
                        </button>
                    </div>

                </form>

                <!-- Login Link -->
                <div class="text-center mb-4">
                    <p class="text-gray-600 text-sm">
                        Already have an account?
                        <a href="{{ route('admin.login') }}" class="text-amber-700 font-semibold hover:text-amber-800 transition duration-200 hover:underline">
                            Login
                        </a>
                    </p>
                </div>

                <!-- Footer with neutral black/grey and warm accent -->
                <div class="text-center pt-3 border-t border-gray-200">
                    <p class="text-gray-500 text-xs">
                        Admin Portal • Tithandizane Women Hub
                    </p>
                    <p class="text-gray-400 text-xs mt-1 flex items-center justify-center">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-1"></span>
                        Empowering women, nurturing community
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 ml-1"></span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Subtle brand watermark -->
        <div class="text-center mt-6 text-white/70 text-xs">
            <span>🌾 Together we rise 🌞</span>
        </div>
    </div>

    <script>
        // Password strength checker
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        const match = document.getElementById('match-text');

        password.addEventListener('input', function() {
            let val = this.value;
            let score = 0;

            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { width: '25%', color: 'bg-red-500', text: 'Weak' },
                { width: '50%', color: 'bg-yellow-500', text: 'Fair' },
                { width: '75%', color: 'bg-green-500', text: 'Good' },
                { width: '100%', color: 'bg-purple-600', text: 'Strong' }
            ];

            const level = levels[score - 1] || levels[0];

            bar.className = "h-2 rounded transition-all duration-300 " + level.color;
            bar.style.width = level.width;
            text.textContent = level.text + " password";
            text.className = "text-xs mt-1 " + (score >= 3 ? "text-green-600" : "text-gray-500");

            // Trigger match check
            if (confirm.value) {
                checkMatch();
            }
        });

        function checkMatch() {
            if (!confirm.value) {
                match.textContent = "";
                return;
            }

            if (password.value === confirm.value) {
                match.textContent = "✓ Passwords match";
                match.className = "text-green-600 text-xs mt-1";
            } else {
                match.textContent = "✗ Passwords do not match";
                match.className = "text-red-600 text-xs mt-1";
            }
        }

        confirm.addEventListener('input', checkMatch);

        // Eye toggle for password field
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordEyeIcon = document.getElementById('passwordEyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icon
            if (type === 'text') {
                passwordEyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                passwordEyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        });

        // Eye toggle for confirm password field
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmInput = document.getElementById('confirm_password');
        const confirmEyeIcon = document.getElementById('confirmEyeIcon');

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmInput.setAttribute('type', type);
            
            // Toggle eye icon
            if (type === 'text') {
                confirmEyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                confirmEyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        });
    </script>
</body>
</html>