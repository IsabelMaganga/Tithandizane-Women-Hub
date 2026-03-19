@extends('mentor.layouts.dashboard')

@section('title') profile @endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-0">
     <!-- Header with Back Navigation -->
    <div class="mb-4">

        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('mentor.settings') }}" class="hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Settings</span>
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-900">profile</span>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Profile Settings</h1>
        <p class="mt-2 text-sm text-gray-600">Update your personal information and how others see you on the platform.</p>
    </div>

    <!-- Profile Form -->
    <form method="POST" action="{{ route('mentor.updateProfile') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <!-- Profile Image Section -->
        <div class="p-6 sm:p-8 border-b border-gray-200 bg-gray-50/50">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Profile Picture</h2>
            <div class="flex items-center space-x-6">
                <!-- Current Profile Image -->
                <div class="relative">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 ring-4 ring-white">
                        <img src="https://ui-avatars.com/api/?name=John+Doe&size=96&background=6366f1&color=fff"
                             alt="Profile"
                             class="w-full h-full object-cover">
                    </div>
                    <!-- Online Status (optional) -->
                    <span class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></span>
                </div>

                <!-- Image Upload Controls -->
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <label for="profile-image"
                               class="cursor-pointer px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <span>Upload new photo</span>
                            <input type="file" id="profile-image" class="hidden" accept="image/*">
                        </label>
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Remove
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">JPG, GIF or PNG. Max size 2MB.</p>
                </div>
            </div>
        </div>

        {{-- successfull message --}}
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

        <!-- Personal Information Form -->
        <div class="p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h2>
                <p class="text-sm text-gray-600 mb-6">Update your personal details and public information.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
             @endif


             <!-- Name Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="first_name"
                           name="name"
                           value="{{ $mentorUser->name }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Enter your first name">
                    <p class="mt-1 text-xs text-gray-500">Maximum 50 characters</p>
                </div>

            </div>

            <!-- Email and Phone -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m12 0a4 4 0 01-4 4H8a4 4 0 01-4-4V8a4 4 0 014-4h8a4 4 0 014 4v4z" />
                            </svg>
                        </div>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ $mentorUser->email }}"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="you@example.com">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">We'll never share your email</p>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input type="tel"
                               id="phone"
                               name="phone"
                               value="{{ $mentorPhone }}"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="+1 (555) 000-0000">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">For account recovery and notifications</p>
                </div>
            </div>

            <!-- Bio Section -->
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                    Bio
                </label>
                <textarea id="bio"
                          name="bio"
                          rows="4"
                          class="w-full px-4 py-2.5 border text-left border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                > {{ old('bio',$mentorBio ) }}
                </textarea>
                <div class="mt-2 flex justify-between items-center">
                    <p class="text-xs text-gray-500">Write a short introduction. Maximum 500 characters.</p>
                    <span class="text-xs text-gray-400">124/500</span>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="px-6 sm:px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                Cancel
            </button>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Save Changes
            </button>
        </div>
    </form>

    <!-- Additional Profile Sections -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Account Status -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Status</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Email verified</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Verified
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Phone verified</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Not verified
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Member since</span>
                    <span class="text-sm font-medium text-gray-900">{{  $mentorCreatedAt }}</span>
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
    </div>
</div>
@endsection
