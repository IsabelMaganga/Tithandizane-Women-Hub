<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forgot-password</title>
    <link rel="short icon" href="{{ asset('images/Ellipse 3.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+DK+Uloopet+Guides&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

        body{
            font-family: "poppins", Arial, Helvetica, sans-serif;
        }

    </style>
</head>

<body class="flex flex-wrap items-center justify-center max-w-5xl min-h-screen grid-cols-1 mx-auto bg-white md:flex-nowrap md:grid-cols-2">

    {{-- leftt-section --}}
    <div class="w-full image">
        <img src="/loginpng.png" class="object-cover scale-80" alt="">
    </div>

    {{-- right-grid container --}}
    <div class="z-10 flex flex-col items-center justify-center w-full left-grid ">

        @if (session('retry_after'))
            <div id="error" class="px-4 py-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-400 rounded">
                Too many attempts. Please wait
                <span id="countdown"></span> before trying again.
            </div>
        @else

            @php
                $remainingTime = $remaining ?? session('remaining');
            @endphp

            @if (!empty($remainingTime))
                @if (session('remaining'))
                    <div id="error" class="p-3 mb-4 text-sm text-yellow-800 bg-yellow-100 rounded">
                        You can change your password again in <span id="remainingcountdown"> {{ $remainingTime }}</span>
                    </div>
                 @endif
            @else

                <div class="w-full py-4 rounded-r-lg px-7">

                    <div class="flex flex-col items-center justify-center w-full gap-2 login-top-section">

                        <!-- Title -->
                        <h2 class="mb-1 text-3xl text-center text-gray-800">
                            Forgot Your Password?
                        </h2>

                        <!-- Description -->
                        <p class="mb-2 text-center text-gray-400">
                            We'll verify your email if your are a real mentor
                        </p>

                    </div>

                    @if (session('success'))
                            <div class="flex items-center justify-between px-4 py-3 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif

                    <form method="POST" action="{{ route('mentor.sendResetLink') }}"  class="w-full ">
                        @csrf

                        {{-- email --}}
                        <div class="mt-5 mb-5">

                            <div class="relative input-field">

                                <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="input w-full px-3 pl-10 py-3 text-sm placeholder:text-sm placeholder:text-[#8D54F7]/50 text-[#8D54F7] transition-all ease-in-out border-2 bg-amber-50/0 border-[#8D54F7]/30 rounded-3xl focus:outline-none focus:ring-2 focus:ring-[#8D54F7] focus:border-transparent"
                                placeholder="mentor@tithandizane.mw"
                                {{-- required --}}
                                autocomplete="email"
                                autofocus
                                >

                                <i class="fa-regular fa-envelope absolute left-3 top-3 text-[#8D54F7]/80 text-xl"></i>
                            </div>
                        </div>



                            <button
                                type="submit"
                                class="w-full mb-3 bg-[#8D54F7] active:bg-[#7043c4] hover:bg-[#7335e7] border border-[#eeeeec]/20 transition delay-75 px-5 py-4 rounded-3xl text-white text-sm font-semibold leading-normal flex items-center justify-center gap-2"
                            >
                                Send Reset Link
                            </button>


                            @if (session('error'))
                                <div class="px-4 py-1 mb-2 text-sm text-red-700 bg-red-100 rounded-lg">
                                    <strong>Error!</strong> {{ session('error') }}
                                </div>
                            @endif

                            <!-- Back to Login -->
                            <div class="mt-6 text-center">
                                <a href="{{ route('mentor.login') }}" class="text-indigo-600 hover:underline">
                                    Back to Sign In
                                </a>
                            </div>


                    </form>



                </div>

            @endif
        @endif

    </div>

    <script>

        // attempts logins timmer
        let remaining = {{ session('retry_after') }}; // seconds from controller
        const countdownEl = document.getElementById('countdown');

        function updateCountdown() {
            let minutes = Math.floor(remaining / 60);
            let seconds = remaining % 60;
            countdownEl.textContent = ` ${minutes}m ${seconds}s `;

            if (remaining > 0) {
                remaining--;
                setTimeout(updateCountdown, 1000);

                if(remaining == 0 ) {
                    window.location.href = '/mentor/forgot-password'
                }
            }
        }

        updateCountdown();


        window.addEventListener('DOMContentLoaded', ()=>{

            const msgSuccess = document.getElementById('success');
            const msgError = document.getElementById('error');

            if(msgSuccess){
                msgSuccess.style.display = 'flex';
                setTimeout(() => {
                    msgSuccess.style.display = 'none';
                }, 5000);
            }

              if(msgError){
                msgError.style.display = 'flex';
                setTimeout(() => {
                    msgError.style.display = 'none';
                    msgError.style.transition = '.2s ease-in-out';
                }, 5000);
            }

        })

    </script>


</body>
</html>
