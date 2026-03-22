@extends('mentor.layouts.dashboard')

@section('title') profile @endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-0">
     <!-- Header with Back Navigation -->
    <div class="mb-4">

        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('mentor.dashboard') }}" class="hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>back</span>
            </a>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">Profile </h1>
        <p class="mt-2 text-sm text-gray-600">Update your personal information and how others see you on the platform.</p>
    </div>

    <!-- Profile Form -->
    <div class=" rounded-xl overflow-hidden">

        <!-- header Section -->
        <div class="p-6 sm:p-0 border-b bg-gradient-to-br from-purple-100 to-purple-200 border-gray-200 ">
            <div class=" grid grid-cols-1 gap-2 md:grid-cols-3">
                {{-- image section --}}
                <div class="flex items-center justify-center space-x-6">
                    <!--profile Image -->
                    <div class="relative">
                        <div class="w-40 h-40 rounded-full overflow-hidden bg-gray-100 ring-4 ring-white">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&size=96&background=6366f1&color=fff"
                                alt="Profile"
                                class="w-full h-full object-cover">
                        </div>
                        <!-- Online Status -->
                        <span class="absolute bottom-1 right-1 w-6 h-6 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>

                </div>

                {{-- left section --}}
                <div class="bg-gradient-to-br from-[#874179] to-[#ce64bc] col-span-2 rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-50 mb-4">Profile Status</h3>
                    <div class="space-y-4 text-gray-50">
                        {{-- email verity --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-100">Email verified</span>
                            @if ($mentorEmail)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-400 text-gray-100">
                                    Verification required
                                </span>
                            @endif

                        </div>

                        {{-- phone details --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-50">Phone verified</span>
                            @if ($mentorPhone)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">verified</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-400 text-gray-100">Not verified</span>
                            @endif
                        </div>

                        {{-- registered date --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-50">Member since</span>
                            <span class="text-sm font-light text-gray-50">{{  $mentorCreatedAt }}</span>
                        </div>

                        {{-- availability --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-100">Availability</span>

                            @if ( $mentorAvailable == 1 )

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    True
                                </span>
                            @else

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    False
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information Form -->
        <div class=" bg-gradient-to-br from-[#ffffff] to-[#ffe9fb] rounded-3xl shadow p-6 sm:p-8  md:mx-4 my-4 space-y-6">
            <div>
                <h2 class="text-lg font-medium text-gray-700 mb-4">Personal Information</h2>

                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-600 mb-6">Update your personal details and public information.</p>
                    <a href="{{ route('mentor.showProfile')}}" class="text-sm text-gray-100 mb-6 px-10 shadow py-3 rounded-3xl font-semibold bg-[#64305a] hover:bg-[#9e4b8d] active:bg-[#64305a] cursor-pointer select-none transition delay-75">edit.</a>
                </div>
            </div>

             <!-- Name Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-600 mb-2">Mentor name </label>
                    <p class="mt-1 text-xs text-gray-500">{{ $mentorUser->name }}</p>
                </div>
            </div>

            <!-- Email and Phone -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-600 mb-2">Email Address</label>
                    <div class="relative flex gap-1 flex-row">
                        <div class="  flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m12 0a4 4 0 01-4 4H8a4 4 0 01-4-4V8a4 4 0 014-4h8a4 4 0 014 4v4z" />
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500">{{$mentorUser->email}}</p>
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-600 mb-2">Phone Number</label>
                    <div class="relative flex items-center gap-2 justify-start">
                        <div class=" in left-0 pl-0 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <p class=" text-xs text-gray-500">
                            @if ($mentorPhone)
                                {{ $mentorPhone }}
                            @else
                                Null
                            @endif</p>
                    </div>
                </div>
            </div>

            <!-- Bio Section -->
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-600 mb-2">Bio</label>
                <div class="mt-2 flex justify-between items-center">
                    <p class="text-xs text-gray-500">{{$mentorBio}}.</p>
                </div>
            </div>

        </div>

        <!-- footer -->
        <div class="px-6 sm:px-8 py-4 text-center bg-gray-50 border-t border-gray-200 flex items-center justify-start space-x-4">
           <p class=" text-sm text-gray-400 text-center">&copy; 2026 Tithandizane-women-Hub</p>
        </div>
    </div>


</div>
@endsection
