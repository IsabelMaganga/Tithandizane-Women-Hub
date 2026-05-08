@extends('mentor.layouts.dashboard')

@section('title') profile @endsection

@section('content')
<div class="max-w-4xl px-4 py-10 mx-auto sm:px-6 lg:px-8">
     <!-- Header with Back Navigation -->
    <div class="mb-4">

        <div class="flex items-center gap-2 mb-2 text-sm text-gray-600">
            <a href="{{ route('mentor.settings') }}" class="flex items-center gap-1 hover:text-gray-900">
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

    {{--  @if($user->profile_picture)
        <img src="{{ asset('storage/' . $user->profile_picture) }}"
            alt="Profile Picture"
            class="object-cover w-32 h-32 rounded-full">
    @else
        <img src="{{ asset('images/default-avatar.png') }}"
            alt="Default Avatar"
            class="object-cover w-32 h-32 rounded-full">
    @endif  --}}

    <!-- Profile Form -->
    <form method="POST" action="{{ route('mentor.updateProfile') }}" enctype="multipart/form-data" class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
        @csrf
        @method('PUT')

        <!-- Profile Image Section -->
        <div class="p-6 border-b border-gray-200 sm:p-8 bg-gray-50/50">
            <h2 class="mb-4 text-lg font-medium text-gray-900">Profile Picture</h2>
            <div class="flex items-center space-x-6">
                <!-- Current Profile Image -->
                <div class="relative">
                    <div class="w-24 h-24 overflow-hidden bg-gray-100 rounded-full ring-4 ring-white">
                        <img src="https://ui-avatars.com/api/?name=John+Doe&size=96&background=6366f1&color=fff"
                             alt="Profile"
                             class="object-cover w-full h-full">
                    </div>
                    <!-- Online Status (optional) -->
                    <span class="absolute w-4 h-4 bg-green-500 border-2 border-white rounded-full bottom-1 right-1"></span>
                </div>

                <!-- Image Upload Controls -->
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <label for="profile-image"
                               class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span>Upload new photo</span>
                            <input type="file" id="profile-image" class="hidden" accept="image/*">
                        </label>
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Remove
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">JPG, GIF or PNG. Max size 2MB.</p>
                </div>
            </div>
        </div>

        {{-- successfull message --}}
         @if(session('success'))
            <div class="p-4 mb-6 border border-green-200 rounded-lg bg-green-50">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
         @endif

        <!-- Personal Information Form -->
        <div class="p-6 space-y-6 sm:p-8">
            <div>
                <h2 class="mb-4 text-lg font-medium text-gray-900">Personal Information</h2>
                <p class="mb-6 text-sm text-gray-600">Update your personal details and public information.</p>
            </div>

            @if ($errors->any())
                <div class="px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
             @endif


             <!-- Name Fields -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-700">
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
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">
                        Phone Number
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <label for="bio" class="block mb-2 text-sm font-medium text-gray-700">
                    Bio
                </label>
                <textarea id="bio"
                          name="bio"
                          rows="4"
                          class="w-full px-4 py-2.5 border text-left border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                > {{ old('bio',$mentorBio ) }}
                </textarea>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-gray-500">Write a short introduction. Maximum 500 characters.</p>
                    <span class="text-xs text-gray-400">124/500</span>
                </div>
            </div>

        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end px-6 py-4 space-x-4 border-t border-gray-200 sm:px-8 bg-gray-50">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:text-gray-900">
                Cancel
            </button>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Save Changes
            </button>
        </div>
    </form>

    <!-- Additional Profile Sections -->
    <div class="grid grid-cols-1 gap-6 mt-8 md:grid-cols-2">

        <!-- Account Status -->
        <div class="p-6 bg-white border border-gray-200 rounded-lg">
            <h3 class="mb-4 text-lg font-medium text-gray-900">Account Status</h3>
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
