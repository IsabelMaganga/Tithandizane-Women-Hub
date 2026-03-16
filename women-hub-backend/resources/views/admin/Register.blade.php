<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - Tithandizane Women Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">


<div class="text-center mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Admin Register</h1>
    <p class="text-gray-600 mt-2">Tithandizane Women Hub</p>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.register.post') }}">
    @csrf

    <!-- Full Name -->
    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2">
            Full Name
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name') }}"
            placeholder="Chisomo Phiri"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            required
            autofocus
        >
    </div>

    <!-- Email -->
    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2">
            Email Address
        </label>

        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="admin@tithandizane.mw"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            required
        >
    </div>

    <!-- Password -->
    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2">
            Password
        </label>

        <input
            type="password"
            name="password"
            id="password"
            placeholder="Minimum 8 characters"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            required
        >

        <!-- Password Strength -->
        <div class="mt-2">
            <div class="h-2 bg-gray-200 rounded">
                <div id="strength-bar" class="h-2 rounded w-0"></div>
            </div>
            <p id="strength-text" class="text-xs mt-1 text-gray-500"></p>
        </div>
    </div>

    <!-- Confirm Password -->
    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2">
            Confirm Password
        </label>

        <input
            type="password"
            name="password_confirmation"
            id="confirm_password"
            placeholder="Re-enter password"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            required
        >

        <p id="match-text" class="text-xs mt-1"></p>
    </div>

    <!-- Register Button -->
    <div class="mb-6">
        <button
            type="submit"
            class="w-full bg-purple-600 text-white font-bold py-2 px-4 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition duration-200"
        >
            Register Admin
        </button>
    </div>

</form>

<!-- Login Link -->
<div class="text-center mb-4">
    <p class="text-gray-600 text-sm">
        Already have an account?
        <a href="{{ route('admin.login') }}" class="text-purple-600 font-semibold hover:underline">
            Login
        </a>
    </p>
</div>

<div class="text-center">
    <p class="text-gray-600 text-sm">
        Admin Portal - Tithandizane Women Hub
    </p>
</div>


</div>

<script>

const password = document.getElementById('password');
const confirm  = document.getElementById('confirm_password');

const bar  = document.getElementById('strength-bar');
const text = document.getElementById('strength-text');
const match = document.getElementById('match-text');

password.addEventListener('input', function(){

    let val = this.value;
    let score = 0;

    if(val.length >= 8) score++;
    if(/[A-Z]/.test(val)) score++;
    if(/[0-9]/.test(val)) score++;
    if(/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        {width:'25%', color:'bg-red-500', text:'Weak'},
        {width:'50%', color:'bg-yellow-500', text:'Fair'},
        {width:'75%', color:'bg-green-500', text:'Good'},
        {width:'100%', color:'bg-purple-600', text:'Strong'}
    ];

    const level = levels[score-1] || levels[0];

    bar.className = "h-2 rounded " + level.color;
    bar.style.width = level.width;

    text.textContent = level.text + " password";

});

confirm.addEventListener('input', function(){

    if(!this.value){
        match.textContent = "";
        return;
    }

    if(password.value === this.value){
        match.textContent = "Passwords match";
        match.className = "text-green-600 text-xs mt-1";
    }else{
        match.textContent = "Passwords do not match";
        match.className = "text-red-600 text-xs mt-1";
    }

});

</script>

</body>
</html>
