@extends('mentor.layouts.dashboard')

@section('title') privacy @endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-0">
    <!-- Header with Back Navigation -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('mentor.settings') }}" class="hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Settings</span>
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-900">Security</span>
        </div>
        <h1 class="text-3xl font-semibold text-gray-900">Security Settings</h1>
        <p class="mt-2 text-sm text-gray-600">Manage your password and account security preferences.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content - Change Password -->
        <div class="lg:col-span-2">
            <!-- Change Password Card -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-900">Change Password</h2>
                </div>

                <div class="p-6">
                    @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-medium">Please fix the following errors:</p>
                                <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-6"> --}}
                    <form method="POST" action="" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="current_password"
                                       id="current_password"
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('current_password') border-red-500 @enderror"
                                       placeholder="Enter your current password"
                                       required>
                                <button type="button"
                                        onclick="togglePasswordVisibility('current_password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="new_password"
                                       id="new_password"
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('new_password') border-red-500 @enderror"
                                       placeholder="Enter new password"
                                       required>
                                <button type="button"
                                        onclick="togglePasswordVisibility('new_password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Password Strength Meter -->
                            <div class="mt-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div id="password-strength-bar" class="h-full w-0 transition-all duration-300"></div>
                                    </div>
                                    <span id="password-strength-text" class="text-xs font-medium text-gray-600">Enter password</span>
                                </div>
                                <ul class="mt-3 space-y-1 text-xs text-gray-600">
                                    <li class="flex items-center gap-2" id="req-length">
                                        <span class="text-gray-400">•</span> At least 8 characters
                                    </li>
                                    <li class="flex items-center gap-2" id="req-uppercase">
                                        <span class="text-gray-400">•</span> One uppercase letter
                                    </li>
                                    <li class="flex items-center gap-2" id="req-lowercase">
                                        <span class="text-gray-400">•</span> One lowercase letter
                                    </li>
                                    <li class="flex items-center gap-2" id="req-number">
                                        <span class="text-gray-400">•</span> One number
                                    </li>
                                    <li class="flex items-center gap-2" id="req-special">
                                        <span class="text-gray-400">•</span> One special character
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="new_password_confirmation"
                                       id="new_password_confirmation"
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="Confirm your new password"
                                       required>
                                <button type="button"
                                        onclick="togglePasswordVisibility('new_password_confirmation')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <div id="password-match-message" class="mt-2 text-sm hidden"></div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    id="submit-button">
                                Update Password
                            </button>
                            <button type="reset"
                                    class="px-6 py-2.5 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>


        </div>

        <!-- Sidebar - Security Tips & 2FA -->
        <div class="lg:col-span-1 space-y-6">


            <!-- Security Tips -->
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Security Tips
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600">•</span>
                        Use a unique password you don't use elsewhere
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600">•</span>
                        Avoid using personal information in your password
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600">•</span>
                        Change your password regularly
                    </li>
                </ul>
            </div>
            <!-- Account Status -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Security Status</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Strength</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Strong
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Last update</span>
                    <span class="text-sm font-medium text-gray-900">{{  $mentorPasswordUpdatedDate }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Time</span>
                    <span class="text-sm font-medium text-gray-900">{{  $mentorPasswordUpdatedTime }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Availability</span>

                    @if ( $mentorAvailable == 1 )

                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            True
                        </span>
                    @else

                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            False
                        </span>
                    @endif
                </div>
            </div>
        </div>

            <!-- Sessions -->
            <form action="{{ route('mentor.logoutAllSessions')}}"
                onsubmit=" return confirm('Are you sure you want to sign out from all devices. This includes your device.');" method="POST" class="bg-white rounded-xl border border-gray-200 p-6">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">Active Sessions</h3>
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">1 active</span>
                </div>
                <p class="text-xs text-gray-500 mb-4">Manage your active sessions across devices</p>
                <button type="submit" class="w-full bg-red-500 px-4 py-2 border border-gray-300 hover:bg-red-600 text-gray-100 text-sm font-medium rounded-lg transition-colors">
                    Sign out all devices
                </button>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Password Features -->
<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}

// Password strength checker
document.getElementById('new_password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');

    // Requirements
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };

    // Update requirement indicators
    for (let [req, met] of Object.entries(requirements)) {
        const element = document.getElementById(`req-${req}`);
        if (element) {
            element.className = met ? 'flex items-center gap-2 text-green-600' : 'flex items-center gap-2 text-gray-400';
            element.innerHTML = met ?
                `<span class="text-green-600">✓</span> ${element.innerText.slice(2)}` :
                `<span class="text-gray-400">•</span> ${element.innerText.slice(2)}`;
        }
    }

    // Calculate strength
    const metCount = Object.values(requirements).filter(Boolean).length;
    const strengthLevels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const strengthColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];

    const level = Math.min(metCount, 4);
    strengthBar.className = `h-full w-${(level + 1) * 20}% transition-all duration-300 ${strengthColors[level]}`;
    strengthText.textContent = strengthLevels[level];
    strengthText.className = `text-xs font-medium text-${strengthColors[level].replace('bg-', '')}-600`;
});

// Password match checker
document.getElementById('new_password_confirmation').addEventListener('input', function(e) {
    const password = document.getElementById('new_password').value;
    const confirm = e.target.value;
    const matchMessage = document.getElementById('password-match-message');
    const submitButton = document.getElementById('submit-button');

    if (confirm.length > 0) {
        if (password === confirm) {
            matchMessage.className = 'mt-2 text-sm text-green-600';
            matchMessage.textContent = '✓ Passwords match';
            submitButton.disabled = false;
        } else {
            matchMessage.className = 'mt-2 text-sm text-red-600';
            matchMessage.textContent = '✗ Passwords do not match';
            submitButton.disabled = true;
        }
        matchMessage.classList.remove('hidden');
    } else {
        matchMessage.classList.add('hidden');
        submitButton.disabled = false;
    }
});
</script>
@endsection
