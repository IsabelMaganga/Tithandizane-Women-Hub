<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>verify</title>
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
                    Verify Your Code
                </h2>

                <!-- Instruction -->
                <p class="text-gray-600 text-center mb-6">
                    Please enter the 6‑digit verification code we sent to your email.
                </p>

                <!-- Verification Form -->
                <form class="space-y-4">
                    @csrf
                    <div class="flex justify-center space-x-2">
                        <input type="text" maxlength="1" class="w-12 h-12 text-center border outline-0 rounded-lg focus:ring-2 focus:ring-indigo-500" />
                        <input type="text" maxlength="1" class="w-12 h-12 text-center border rounded-lg focus:ring-2 focus:ring-indigo-500" />
                        <input type="text" maxlength="1" class="w-12 h-12 text-center border rounded-lg focus:ring-2 focus:ring-indigo-500" />
                        <input type="text" maxlength="1" class="w-12 h-12 text-center border rounded-lg focus:ring-2 focus:ring-indigo-500" />
                        <input type="text" maxlength="1" class="w-12 h-12 text-center border rounded-lg focus:ring-2 focus:ring-indigo-500" />
                        <input type="text" maxlength="1" class="w-12 h-12 text-center border rounded-lg focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition duration-200">
                        Verify Code
                    </button>
                </form>

                <!-- Resend link -->
                <div class="text-center mt-6 flex text-sm items-center justify-center gap-3">
                    <a class="text-indigo-600 hover:underline">
                        Resend Code
                    </a>
                    <a href="{{ route('mentor.forgot') }}" class="text-indigo-600 hover:underline">
                        Back to Sign In
                    </a>
                </div>
            </div>
        </div>


    </div>




</body>
</html>
