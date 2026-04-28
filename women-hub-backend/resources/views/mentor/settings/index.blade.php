@extends('mentor.layouts.dashboard')

@section('content')
<div class="px-4 py-0 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mt-5 mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Settings</h1>
        <p class="mt-2 text-sm text-gray-600">Manage your account preferences and application settings.</p>
    </div>

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

        <!-- Profile Settings -->
        <div class="p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg bg-blue-50">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Profile</h3>
            </div>
            <p class="mb-4 text-sm text-gray-600">Update your personal information and public profile.</p>
            <a href="{{ route('mentor.showProfile')}}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Configure →</a>
        </div>

        <!-- Account Security -->
        <div class="p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg bg-green-50">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Security</h3>
            </div>
            <p class="mb-4 text-sm text-gray-600">Manage password, 2FA, and account security.</p>
            <a href="{{ route('mentor.showSecurity')}}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Configure →</a>
        </div>

        <!-- Privacy -->
        <div class="p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg bg-yellow-50">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Privacy</h3>
            </div>
            <p class="mb-4 text-sm text-gray-600">Control your data and privacy preferences.</p>
            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-800">Configure →</a>
        </div>




    </div>


</div>
@endsection
