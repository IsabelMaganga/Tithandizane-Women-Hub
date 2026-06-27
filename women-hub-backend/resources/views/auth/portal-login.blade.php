<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="short icon" href="{{ asset('images/Ellipse 3.png') }}">
    <title>Staff Portal Login — Tithandizane Women Hub</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.5)), url('/images/background.png');
            background-attachment: fixed;
            background-size: cover;
            background-repeat: no-repeat;
        }
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px rgba(150, 41, 128, 0.15) inset;
            -webkit-text-fill-color: #ffffff;
            transition: background-color 5000s ease-in-out 0s;
        }
        .password-toggle {
            position: absolute; right: 16px; top: 50%;
            transform: translateY(-50%); cursor: pointer;
            color: #9ca3af; transition: color 0.2s ease;
            background: transparent; border: none;
            display: flex; align-items: center; justify-content: center; padding: 0;
        }
        .password-toggle:hover { color: #962980; }
        .loading-spinner {
            display: inline-block; width: 20px; height: 20px;
            border: 2px solid rgba(255,255,255,0.3); border-radius: 50%;
            border-top-color: white; animation: spin 0.8s linear infinite; margin-right: 8px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-loading { opacity: 0.8; cursor: not-allowed; }
        .animate-fade-in { animation: fadeInUp 1.2s ease-out forwards; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-[#874179]/80 relative text-[#1b1b18] flex p-0 lg:p-0 items-center lg:justify-center min-h-screen flex-col">

    <img src="{{ asset('images/shape (1).png') }}" class="fixed top-0 left-0 z-10 object-cover w-40" alt="">
    <img src="{{ asset('images/shape (1).png') }}" class="fixed bottom-0 right-0 z-10 object-cover w-40 -rotate-180" alt="">

    <header class="w-[90%] md:w-full sticky flex flex-wrap items-center shadow-2xl backdrop-blur-2xl rounded-3xl px-5 justify-between z-50 py-2 top-5 lg:max-w-4xl max-w-7xl text-sm mb-6">
        <a href="{{ route('get.started') }}" class="logo decoration-0 text-[#ffff] flex justify-start items-center gap-3">
            <img src="{{ asset('images/Ellipse 3.png') }}" class="w-10" alt="">
            <h1 class="font-bold">Tithandizane Women Hub</h1>
        </a>
        <nav class="flex items-center text-[#ffff] justify-end gap-4">
            <a href="{{ route('welcome') }}" class="inline-block px-5 py-3 hover:text-[#ee8edb] cursor-pointer text-sm leading-normal">Home</a>
            <a class="inline-block px-5 py-3 bg-[#874179] rounded-3xl font-semibold hover:bg-[#44273e] cursor-pointer text-sm leading-normal">About</a>
        </nav>
    </header>

    <div class="relative grid items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">

        <main class="z-20 grid items-center justify-center w-full max-w-4xl grid-cols-1 gap-5 pt-4 mx-auto mb-5 text-gray-200 animate-fade-in md:pt-4">

            <div class="flex-1 mx-auto w-[90%] md:w-[480px] border-2 border-amber-50/20 hover:border-[#962980]/80 transition delay-75 p-8  text-[#ffffff] shadow-2xl rounded-2xl">

                <!-- Title -->
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-full  border border-[#962980]/60 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-pink-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Staff Portal Login</h1>
                        <p class="text-xs text-gray-400">Authorised personnel only</p>
                    </div>
                </div>

                <!-- Role pills -->
                <div class="flex gap-2 mb-6">
                    <span class="px-3 py-1 text-xs font-semibold bg-[#962980]/30 border border-[#962980]/50 rounded-full text-pink-200">Admin</span>
                    <span class="px-3 py-1 text-xs font-semibold bg-[#962980]/20 border border-[#962980]/40 rounded-full text-pink-300">Mentor</span>
                </div>

                @if (session('status'))
                    <div class="px-4 py-3 mb-4 text-sm text-green-300 border bg-green-900/30 border-green-500/30 rounded-xl">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="px-4 py-3 mb-4 text-sm text-green-300 border bg-green-900/30 border-green-500/30 rounded-xl">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('portal.login.post') }}" class="space-y-5" id="loginForm">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block mb-1 text-sm font-medium text-gray-200">Email Address</label>
                        <input
                            id="email" type="email" name="email"
                            value="{{ old('email') }}"
                            required autofocus autocomplete="username"
                            placeholder="your@email.com"
                            class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-400 text-sm focus:outline-none focus:border-[#962980] focus:ring-1 focus:ring-[#962980] transition"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-pink-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block mb-1 text-sm font-medium text-gray-200">Password</label>
                        <div class="relative">
                            <input
                                id="password" type="password" name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-gray-400 text-sm focus:outline-none focus:border-[#962980] focus:ring-1 focus:ring-[#962980] transition pr-12"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
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
                            id="remember_me" type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-white/20 bg-white/10 text-[#962980] focus:ring-[#962980] focus:ring-offset-0 cursor-pointer"
                        >
                        <label for="remember_me" class="text-sm text-gray-300 cursor-pointer">Remember me</label>
                    </div>

                    <!-- Submit -->
                    <div class="pt-1">
                        <button
                            type="submit" id="loginButton"
                            class="w-full bg-[#962980] active:bg-[#5c1a4f] hover:bg-[#af2a95] border border-[#eeeeec]/20 transition delay-75 px-5 py-3 rounded-3xl text-white text-sm font-semibold leading-normal flex items-center justify-center gap-2"
                        >
                            <span id="buttonText">Sign In to Portal</span>
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('get.started') }}" class="text-xs text-gray-400 underline transition hover:text-pink-300 underline-offset-2">
                        ← Back to portal selection
                    </a>
                </div>
            </div>
        </main>

        <p class="flex justify-center w-full py-2 text-sm text-center text-gray-300">
            &copy; 2026 Tithandizane-women-hub. All rights reserved
        </p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }

        const loginForm   = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const buttonText  = document.getElementById('buttonText');

        loginForm.addEventListener('submit', function (e) {
            const email    = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            if (email && password) {
                loginButton.disabled = true;
                loginButton.classList.add('btn-loading');
                buttonText.innerHTML = '<span class="loading-spinner"></span>Signing in...';
            } else {
                e.preventDefault();
                alert('Please fill in all fields');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            @if($errors->any())
                loginButton.disabled = false;
                loginButton.classList.remove('btn-loading');
                buttonText.innerHTML = 'Sign In to Portal';
            @endif
        });
    </script>

    @include('components.inactive-modal')
</body>
</html>
