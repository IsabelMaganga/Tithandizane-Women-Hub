<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reset-password</title>
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

<body class="bg-white min-h-screen  flex max-w-5xl mx-auto flex-wrap md:flex-nowrap grid-cols-1 md:grid-cols-2  items-center justify-center">

    {{-- leftt-section --}}
    <div class="image w-full">
        <img src="/loginpng.png" class=" object-cover scale-80" alt="">
    </div>

    {{-- right-grid container --}}
    <div class="left-grid z-10 flex flex-col items-center justify-center w-full ">

            <div class="min-h-screen flex items-center justify-center">
                <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
                    <!-- Title -->
                    <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">
                        Reset Your Password
                    </h2>

                    <!-- Description -->
                    <p class="text-gray-600 text-center mb-6">
                        Enter your new password below to complete the reset process.
                    </p>

                    <!-- Reset Password Form -->
                    <form method="post" action="/mentor/reset-password" class="space-y-4">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email -->
                        <div>
                            <label for="email" class="flex gap-2" text-sm font-medium text-gray-700 mb-2">
                                Email Address
                                 @error('email')
                                     <p class=" text-red-500"> {{ $message }} </p>
                                @enderror
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                            <p id="emailError" class=" text-red-400 text-sm"></p>
                        <!-- New Password -->
                        <div>
                            <label for="password" class="flex gap-2" text-sm font-medium text-gray-700 mb-2">
                                New Password
                                @error('password')
                                    <p class=" text-red-500"> {{ $message }} </p>
                                @enderror
                            </label>
                            <input type="password" id="password" name="password"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class=" text-sm font-medium text-gray-700 mb-2 flex gap-2">
                                Confirm Password
                                @error('password_confirmation')
                                   <p class=" text-red-500"> {{ $message }} </p>
                                @enderror
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                            Reset Password
                        </button>
                    </form>

                    {{-- successful message --}}
                    @if(session('success'))
                    <div class="mb-0 p-4 bg-green-50 rounded mt-2">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mb-0 p-4 bg-red-50 rounded mt-2">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                    @endif

                </div>
            </div>


    </div>

    <script>

        document.getElementById('email').addEventListener('input', function() {
            const email = this.value.trim();
            const errorEl = document.getElementById('emailError');

            if(email === "")
            {
                errorEl.textContent = "";
            } else if (!email.match(/^[^@]+@[^@]+\.[^@]+$/))
            {
                errorEl.textContent = "Please enter a valid email address.";
            } else
            {
                    errorEl.textContent = "";
            }
        }
    );

    </script>


</body>
</html>
