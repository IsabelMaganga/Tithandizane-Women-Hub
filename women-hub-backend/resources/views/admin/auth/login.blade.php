<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="short icon" href="{{ asset('images/Ellipse 3.png') }}">
    <title>{{ 'Admin Login' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.5)), url('/images/background.png');
            background-attachment: fixed;
            background-size: cover;
            background-repeat: no-repeat;
            z-index: -1;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px rgba(150, 41, 128, 0.15) inset;
            -webkit-text-fill-color: #ffffff;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Password toggle button styles */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            transition: color 0.2s ease;
            background: transparent;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .password-toggle:hover {
            color: #962980;
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Button loading state */
        .btn-loading {
            opacity: 0.8;
            cursor: not-allowed;
        }

        /* Hide eye icon when input is autofilled */
        input:-webkit-autofill ~ .password-toggle {
            display: none;
        }
    </style>
</head>

<body class="bg-[#874179]/80 relative text-[#1b1b18] flex p-0 lg:p-0 items-center lg:justify-center min-h-screen flex-col">

    <!-- Decorative corner shapes -->
    <img src="{{ asset('images/shape (1).png')}}" class="z-10  object-cover w-40 fixed top-0 left-0" alt="">
    <img src="{{ asset('images/shape (1).png')}}" class="z-10 object-cover w-40 fixed bottom-0 -rotate-180 right-0" alt="">

    <!-- Header -->
    <header class="w-[90%] md:w-full sticky flex flex-wrap items-center shadow-2xl backdrop-blur-2xl rounded-3xl px-5 justify-between z-50 py-2 top-5 lg:max-w-4xl max-w-7xl text-sm mb-6">
        <a href="{{ route('get.started') }}" class="logo decoration-0 text-[#ffff] flex justify-start items-center gap-3">
            <img src="{{ asset('images/Ellipse 3.png')}}" class="w-10" alt="">
            <h1 class="font-bold">Tithandizane Women Hub</h1>
        </a>
        <nav class="flex items-center text-[#ffff] justify-end gap-4">
            <a href="{{ route('welcome') }}" class="inline-block px-5 py-3 hover:text-[#ee8edb] cursor-pointer text-sm leading-normal">
                Home
            </a>
            <a class="inline-block px-5 py-3 hover:text-[#ee8edb] cursor-pointer text-sm leading-normal">
                Dashboard
            </a>
            <a class="inline-block px-5 py-3 bg-[#874179] rounded-3xl font-semibold hover:bg-[#44273e] cursor-pointer text-sm leading-normal">
                About
            </a>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="grid relative items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">

        <!-- Page Title -->
        {{--  <main class="flex max-w-5xl mt-0 z-20 gap-5 md:pt-6 w-full justify-center items-center text-gray-100 flex-col">
            <p class="mb-2 w-[90%] mt-6 max-w-5xl font-semibold text-3xl md:text-4xl text-center" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                Admin Platform Login
            </p>
            <p class="text-gray-300 text-sm text-center w-[90%] max-w-md -mt-2">
                Control the platform. Empower the community. Let's get to work.
            </p>
        </main>  --}}

        <!-- Login Card -->
        <main class="grid grid-cols-1 max-w-4xl mx-auto z-20 gap-5 mb-5 pt-4 md:pt-4 w-full justify-center items-center text-gray-200">

            <div class="flex-1 mx-auto w-[90%] md:w-[480px] border-2 border-amber-50/20 hover:border-[#962980]/80 transition delay-75 p-8 backdrop-blur-xl text-[#ffffff] shadow-2xl rounded-2xl">

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-full bg-[#962980]/40 border border-[#962980]/60 flex items-center justify-center">
                        <!-- Shield/Admin icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Admin Login</h1>
                        <p class="text-xs text-gray-400">Restricted access — authorised personnel only</p>
                    </div>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-300 bg-green-900/30 border border-green-500/30 rounded-xl px-4 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Login Form - NOW LINKED TO DASHBOARD -->
                <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5" id="loginForm">
                    @csrf
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-200 mb-1">
                            Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin@example.com"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-400 text-sm focus:outline-none focus:border-[#962980] focus:ring-1 focus:ring-[#962980] transition"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-pink-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password with Eye Toggle -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-200">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-pink-300 hover:text-pink-200 transition underline underline-offset-2">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-400 text-sm focus:outline-none focus:border-[#962980] focus:ring-1 focus:ring-[#962980] transition pr-12"
                            >
                            <button
                                type="button"
                                class="password-toggle"
                                onclick="togglePassword()"
                                aria-label="Toggle password visibility"
                            >
                                <!-- Eye Closed Icon (default) -->
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-pink-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-white/20 bg-white/10 text-[#962980] focus:ring-[#962980] focus:ring-offset-0 cursor-pointer"
                        >
                        <label for="remember_me" class="text-sm text-gray-300 cursor-pointer">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit with Loading Animation - NOW REDIRECTS TO DASHBOARD -->
                    <div class="pt-1">
                        <button
                            type="submit"
                            id="loginButton"
                            class="w-full bg-[#962980] active:bg-[#5c1a4f] hover:bg-[#af2a95] border border-[#eeeeec]/20 transition delay-75 px-5 py-3 rounded-3xl text-white text-sm font-semibold leading-normal flex items-center justify-center gap-2"
                        >
                            <span id="buttonText">Sign In to Admin Panel</span>
                        </button>
                    </div>
                </form>

                <!-- Back link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('get.started') }}" class="text-xs text-gray-400 hover:text-pink-300 transition underline underline-offset-2">
                        ← Back to portal selection
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <p class="flex text-sm text-center justify-center text-gray-300 w-full py-2">
            &copy; 2026 Tithandizane-women-hub . All rights reserved
        </p>
    </div>

    <script>
        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Change to eye with slash (hidden)
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                passwordInput.type = 'password';
                // Change back to eye open
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Loading animation on form submit - redirect to dashboard page
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const buttonText = document.getElementById('buttonText');

        loginForm.addEventListener('submit', function(e) {
            // Basic validation
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (email && password) {
                // Show loading state
                loginButton.disabled = true;
                loginButton.classList.add('btn-loading');
                buttonText.innerHTML = '<span class="loading-spinner"></span>Signing in...';

                // Allow the form to submit naturally to Laravel backend
                // The backend will handle authentication and redirect to dashboard
            } else {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });

        // Optional: Reset button if there are validation errors from server
        document.addEventListener('DOMContentLoaded', function() {
            @if($errors->any())
                loginButton.disabled = false;
                loginButton.classList.remove('btn-loading');
                buttonText.innerHTML = 'Sign In to Admin Panel';
            @endif
        });
    </script>
</body>

</html>
