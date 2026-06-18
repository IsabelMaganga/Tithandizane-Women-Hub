<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="{{ asset('images/Ellipse 3.png') }}">
        <title>{{ config('app.name', 'Tithandizane-Women-hub') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Tailwind CSS v4 styles would go here */
            </style>
        @endif

        <style>
            body {
                background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/images/background.png');
                background-attachment: fixed;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                min-height: 100vh;
            }

            /* Custom scrollbar for better UX */
            ::-webkit-scrollbar {
                width: 6px;
            }
            ::-webkit-scrollbar-track {
                background: rgba(255,255,255,0.1);
            }
            ::-webkit-scrollbar-thumb {
                background: #962980;
                border-radius: 10px;
            }

            /* Animation keyframes */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fadeInUp 0.8s ease-out forwards;
            }

            .btn-hover-effect {
                transition: all 0.3s ease;
            }

            .btn-hover-effect:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -5px rgba(150, 41, 128, 0.4);
            }
        </style>
    </head>
    <body class="flex items-center justify-center min-h-screen p-4 font-sans lg:p-8">

        <!-- Decorative Shapes -->
        <img src="{{ asset('images/shape (1).png') }}" class="fixed top-0 left-0 object-cover w-32 opacity-50 -z-10 md:w-40" alt="">
        <img src="{{ asset('images/shape (1).png') }}" class="fixed bottom-0 right-0 object-cover w-32 -rotate-180 opacity-50 -z-10 md:w-40" alt="">

        <!-- Main Container -->
        <div class="relative w-full max-w-5xl mx-auto animate-fade-in">

            <!-- Card Container -->
            <div class="overflow-hidden border shadow-2xl backdrop-blur-md bg-white/10 rounded-3xl border-white/20">

                <!-- Main Content -->
                <div class="flex flex-col items-stretch lg:flex-row">

                    <!-- Left: Logo/Brand Area (Optional - can add image here) -->
                    <div class="hidden lg:flex lg:w-1/3 bg-gradient-to-br from-[#962980]/30 to-purple-900/20 p-8 items-center justify-center">
                        <div class="text-center">
                            <img src="{{ asset('images/Ellipse 3.png') }}" class="w-32 mx-auto mb-4 drop-shadow-2xl" alt="Tithandizane logo">
                            <h2 class="text-sm tracking-widest uppercase text-white/60">Empowerment Hub</h2>
                        </div>
                    </div>

                    <!-- Right: Content Area -->
                    <div class="flex-1 p-6 md:p-10 lg:p-12">

                        <!-- Header with Logo -->
                        <div class="flex items-center gap-3 mb-4 md:gap-4 md:mb-6">
                            <img src="{{ asset('images/Ellipse 3.png') }}" class="w-10 md:w-14 drop-shadow-xl" alt="Tithandizane logo">
                            <h1 class="text-2xl font-bold leading-tight text-white md:text-4xl">
                                Tithandizane
                                <span class="text-[#f0a0d8] text-xl md:text-4xl font-medium">Women Hub</span>
                            </h1>
                        </div>

                        <!-- Description -->
                        <div class="mb-6 md:mb-8">
                            <p class="max-w-xl text-sm leading-relaxed text-white/90 md:text-base">
                                Welcome to the heart of women's empowerment in Malawi. Tithandizane Women Hub is more than a platform; it is a movement dedicated to the safety, growth, and leadership of women. From 24-hour crisis support to community-led economic initiatives, we provide the tools you need to move from vulnerability to victory. Step inside and discover the power of helping one another.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-3 sm:flex-row md:gap-4">
                            <a href="{{ route('get.started') }}" target="_parent"
                               class="btn-hover-effect flex-1 bg-gradient-to-r from-[#962980] to-[#c73da8] hover:from-[#7a2266] hover:to-[#962980] text-center px-6 py-3.5 md:px-8 md:py-4 rounded-xl text-white font-semibold text-sm md:text-base shadow-lg shadow-[#962980]/30 transition-all duration-300">
                                 Get Started
                            </a>
                            <a href="#" target="_parent"
                               class="btn-hover-effect flex-1 border-2 border-white/40 hover:border-white/80 hover:bg-white/10 text-center px-6 py-3.5 md:px-8 md:py-4 rounded-xl text-white font-semibold text-sm md:text-base transition-all duration-300 backdrop-blur-sm">
                                 Download App
                            </a>
                        </div>

                        <!-- Trust Badge / Stats -->
                        <div class="flex flex-wrap justify-center gap-6 pt-6 mt-6 text-xs border-t border-white/10 md:gap-10 text-white/70 md:text-sm">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                24/7 Support
                            </span>
                            <span class="flex items-center gap-2">
                                <span class="text-[#f0a0d8] font-bold">500+</span>
                                Women Empowered
                            </span>
                            <span class="flex items-center gap-2">
                                {{--  <span class="text-[#f0a0d8] font-bold">★</span>  --}}
                                Trusted Community
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <p class="mt-4 text-xs tracking-wider text-center text-white/40">
                &copy; {{ date('Y') }} Tithandizane Women Hub — Together We Rise
            </p>
        </div>
    </body>
</html>
