<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tithandizane Women Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Admin Login</h1>
        <p class="text-gray-600 mt-2">Tithandizane Women Hub</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <div class="mb-6">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                Email Address
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                placeholder="admin@tithandizane.mw"
                required
                autocomplete="email"
                autofocus
            >
        </div>

        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            >
        </div>

        <div class="mb-6">
            <button
                type="submit"
                class="w-full bg-purple-600 text-white font-bold py-2 px-4 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition duration-200"
            >
                Sign In
            </button>
        </div>

    </form>

    <!-- Register Link -->
    <div class="text-center mb-4">
        <p class="text-gray-600 text-sm">
            Don't have an account?
            <a href="{{ route('admin.register') }}" class="text-purple-600 font-semibold hover:underline">
                Register
            </a>
        </p>
    </div>

    <div class="text-center">
        <p class="text-gray-600 text-sm">
            Admin Portal - Tithandizane Women Hub
        </p>
    </div>

</div>

</body>
</html>