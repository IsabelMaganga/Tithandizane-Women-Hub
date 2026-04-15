<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Mentor – Tithandizane Women Hub</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Color Palette Variables - Matching Dashboard Purple Theme */
        :root {
            --purple-primary: #874179;
            --purple-dark: #6d3661;
            --purple-light: #af5c9c;
            --purple-soft: #F3E6F1;
            --purple-gradient-start: #874179;
            --purple-gradient-end: #af5c9c;
            --vibrant-green: #4CAF50;
            --lime-green: #8BC34A;
            --bright-blue: #5CB8E4;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--purple-primary) !important;
        }

        .form-section {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.4s ease-out forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .file-input-label {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .file-input-label input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .expertise-checkbox { display: none; }

        .expertise-label {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            width: 100%;
        }

        .expertise-checkbox:checked + .expertise-label {
            border-color: var(--purple-primary);
            background: var(--purple-soft);
            color: var(--purple-primary);
        }

        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-button { transition: all 0.3s ease; }
        .tab-button.active {
            border-bottom: 3px solid var(--purple-primary);
            color: var(--purple-primary);
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--purple-soft);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--purple-primary);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--purple-dark);
        }
        
        /* Toast notification styles */
        .toast {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 16px;
            position: fixed;
            z-index: 1000;
            left: 50%;
            bottom: 30px;
            font-size: 14px;
            transform: translateX(-50%);
        }
        .toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }
        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen antialiased">
<div class="flex min-h-screen">

   <!-- Sidebar -->
<aside class="hidden lg:flex lg:flex-col lg:w-72 xl:w-80 text-white flex-shrink-0 fixed h-screen" style="background: #874179; border-right: 1px solid #6d3661;">
    <div class="p-6 border-b" style="border-color: #6d3661;">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo2.png') }}" alt="Tithandizane Logo" class="w-12 h-12 rounded-full object-cover shadow-md border-2 border-white/30">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Tithandizane</h1>
                <p class="text-xs opacity-90 text-white">Women Hub</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" style="color: #F1E6EF;">
            <i class="fas fa-home w-5 mr-3" style="color: #FFFFFF;"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.mentors.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" style="background: #6d3661; color: #FFFFFF;">
            <i class="fas fa-chalkboard-user w-5 mr-3" style="color: #9C27B0;"></i>
            <span class="font-medium">Mentors</span>
        </a>
        <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" style="color: #F1E6EF;">
            <i class="fas fa-flag w-5 mr-3" style="color: #8BC34A;"></i>
            <span>Harassment Reports</span>
        </a>
        <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" style="color: #F1E6EF;">
            <i class="fas fa-book-open w-5 mr-3" style="color: #4CAF50;"></i>
            <span>Guidance Content</span>
        </a>
        <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" style="color: #F1E6EF;">
            <i class="fas fa-user-circle w-5 mr-3" style="color: #5CB8E4;"></i>
            <span>Users</span>
        </a>
        <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" style="color: #F1E6EF;">
            <i class="fas fa-cog w-5 mr-3" style="color: #8BC34A;"></i>
            <span>Settings</span>
        </a>
        <div class="pt-8 mt-auto">
            <button type="button" onclick="handleLogout()" class="w-full flex items-center px-4 py-3 rounded-lg transition hover:bg-rose-800/50 text-stone-200 hover:text-white">
                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                <span>Logout</span>
            </button>
        </div>
    </nav>
    <div class="p-5 m-3 rounded-xl mt-auto" style="background: #6d3661; border: 1px solid #af5c9c;">
        <div class="flex items-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($adminName ?? 'Admin User') }}&background=af5c9c&color=fff&size=40" class="w-10 h-10 rounded-full border-2 border-white" alt="{{ $adminName ?? 'Admin' }}">
            <div class="ml-3 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $adminName ?? 'Admin User' }}</p>
                <p class="text-xs text-white/70 truncate">{{ $adminEmail ?? 'admin@tithandizane.org' }}</p>
            </div>
        </div>
    </div>
</aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col lg:ml-72 xl:ml-80">

        <!-- Topbar -->
        <header class="bg-white border-b shadow-sm sticky top-0 z-10" style="border-color: #E2E8F0;">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900">Add New Mentor</h1>
                    <p class="text-sm text-gray-600 mt-0.5">Create and configure a professional mentor profile</p>
                </div>
                <div class="flex items-center space-x-5">
                    <button type="button" class="text-gray-500 hover:text-[#874179] transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                    </button>
                    <button type="button" class="text-gray-500 hover:text-[#874179] transition-colors">
                        <i class="fas fa-envelope text-xl"></i>
                    </button>
                    <div class="hidden sm:flex items-center space-x-3 pl-4 border-l" style="border-color: #E2E8F0;">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $adminName ?? 'Admin User' }}</p>
                            <p class="text-xs text-gray-500">{{ $adminEmail ?? 'admin@tithandizane.org' }}</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($adminName ?? 'Admin User') }}&background=af5c9c&color=fff&size=48" class="w-10 h-10 rounded-full border-2 border-[#874179]" alt="{{ $adminName ?? 'Admin' }}">
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- Breadcrumb -->
            <nav class="mb-8 text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-gray-600">
                    <li><a href="{{ route('admin.dashboard') }}" class="transition-colors flex items-center" style="color: #874179;">
                        <i class="fas fa-home mr-1.5 text-xs"></i> Dashboard
                    </a></li>
                    <li class="text-gray-400"><i class="fas fa-chevron-right text-xs"></i></li>
                    <li><a href="{{ route('admin.mentors.index') }}" class="transition-colors" style="color: #874179;">Mentors</a></li>
                    <li class="text-gray-400"><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-gray-500 font-medium">Add New Mentor</li>
                </ol>
            </nav>

            <!-- Session Messages -->
            <div id="alertContainer"></div>
            
            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-100 border-red-400 text-red-700 border">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden max-w-4xl mx-auto">
                <form action="{{ route('admin.mentors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Header -->
                    <div class="px-6 md:px-8 py-6 border-b" style="background: linear-gradient(135deg, #F9F0F7 0%, #F3E6F1 100%); border-color: #E9D5FF;">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-2xl font-bold" style="color: #874179;">Mentor Registration</h2>
                                <p class="mt-2 text-sm text-gray-700">
                                    Complete the form below to add a new mentor to the platform.
                                    All fields marked with <span class="text-red-500 font-semibold">*</span> are required.
                                </p>
                            </div>
                            <div class="hidden sm:block">
                                <div class="inline-flex items-center space-x-2 bg-white/80 backdrop-blur px-4 py-2 rounded-lg border shadow-sm" style="border-color: #874179;">
                                    <i class="fas fa-info-circle text-sm" style="color: #874179;"></i>
                                    <span class="text-sm font-medium text-gray-700">
                                        Step <span id="currentStep">1</span> of <span id="totalSteps">3</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="hidden md:block px-6 md:px-8 bg-gray-50 border-b border-gray-200">
                        <div class="flex space-x-8">
                            <button type="button" class="tab-button active py-4 px-1 font-medium border-b-2" data-tab="basic" style="border-color: #874179; color: #874179;">
                                <i class="fas fa-user mr-2"></i> Basic Info
                            </button>
                            <button type="button" class="tab-button py-4 px-1 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900" data-tab="professional">
                                <i class="fas fa-briefcase mr-2"></i> Professional
                            </button>
                            <button type="button" class="tab-button py-4 px-1 font-medium text-gray-600 border-b-2 border-transparent hover:text-gray-900" data-tab="additional">
                                <i class="fas fa-link mr-2"></i> Additional
                            </button>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="p-6 md:p-8 space-y-8">

                        <!-- TAB 1: BASIC INFO -->
                        <div id="basic" class="tab-content form-section active">
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                                <!-- Profile Photo -->
                                <div class="lg:col-span-1">
                                    <div class="sticky top-0">
                                        <label class="block text-sm font-semibold text-gray-900 mb-4">Profile Photo</label>
                                        <div class="relative">
                                            <div id="photo-preview"
                                                 class="w-full aspect-square rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden shadow-inner transition-all hover:border-[#874179] hover:from-purple-50 hover:to-purple-100">
                                                <div class="text-center">
                                                    <i class="fas fa-user text-5xl text-gray-400 mb-2"></i>
                                                    <p class="text-xs text-gray-500">Click to upload</p>
                                                </div>
                                            </div>
                                            <label class="file-input-label absolute inset-0 rounded-xl">
                                                <input type="file" name="photo" id="photo" accept="image/*" class="w-full">
                                            </label>
                                        </div>
                                        <p class="mt-3 text-xs text-gray-600 text-center">
                                            <i class="fas fa-check-circle mr-1" style="color: var(--vibrant-green);"></i> PNG, JPG
                                            <br><i class="fas fa-info-circle mr-1" style="color: #874179;"></i> Max 2MB
                                        </p>
                                    </div>
                                </div>

                                <!-- Basic Fields -->
                                <div class="lg:col-span-3 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="name" id="name" required
                                                   placeholder="e.g., Jane Smith"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all">
                                        </div>
                                        <div>
                                            <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address <span class="text-red-500">*</span></label>
                                            <input type="email" name="email" id="email" required
                                                   placeholder="jane.smith@example.com"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all">
                                        </div>
                                        <div>
                                            <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">Phone Number</label>
                                            <input type="tel" name="phone" id="phone"
                                                   placeholder="+265 99 123 4567"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all">
                                        </div>
                                        <div>
                                            <label for="location" class="block text-sm font-semibold text-gray-900 mb-2">Location</label>
                                            <input type="text" name="location" id="location"
                                                   placeholder="e.g., Lilongwe, Malawi"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all">
                                        </div>
                                    </div>

                                    <!-- Password Section -->
                                    <div class="pt-6 border-t border-gray-200">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-1">Account Credentials</h4>
                                        <p class="text-xs text-gray-500 mb-4">
                                            Password must be at least 12 characters and include uppercase letters, lowercase letters, numbers, and special characters.
                                        </p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">Password <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="password" name="password" id="password" required
                                                           placeholder="••••••••••••"
                                                           class="block w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all">
                                                    <button type="button" class="toggle-pw absolute right-3 top-3.5 text-gray-400 hover:text-gray-600" data-target="password">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </button>
                                                </div>
                                                <div class="mt-2 space-y-1">
                                                    <div class="flex gap-1">
                                                        <div class="strength-bar flex-1 bg-gray-200" id="bar1"></div>
                                                        <div class="strength-bar flex-1 bg-gray-200" id="bar2"></div>
                                                        <div class="strength-bar flex-1 bg-gray-200" id="bar3"></div>
                                                        <div class="strength-bar flex-1 bg-gray-200" id="bar4"></div>
                                                    </div>
                                                    <p id="strength-label" class="text-xs text-gray-500"></p>
                                                </div>
                                            </div>
                                            <div>
                                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                                           placeholder="••••••••••••"
                                                           class="block w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all">
                                                    <button type="button" class="toggle-pw absolute right-3 top-3.5 text-gray-400 hover:text-gray-600" data-target="password_confirmation">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </button>
                                                </div>
                                                <div id="password-match" class="mt-2 text-xs text-gray-500"></div>
                                            </div>
                                        </div>
                                        <div class="mt-4 grid grid-cols-2 gap-1 text-xs" id="pw-reqs">
                                            <span id="req-length" class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> 12+ characters</span>
                                            <span id="req-upper" class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> Uppercase letter</span>
                                            <span id="req-lower" class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> Lowercase letter</span>
                                            <span id="req-number" class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> Number</span>
                                            <span id="req-special" class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> Special character</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: PROFESSIONAL INFO -->
                        <div id="professional" class="tab-content form-section">
                            <div class="space-y-8">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-4">Areas of Expertise <span class="text-red-500">*</span></label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @php
                                        $expertiseOptions = [
                                            'Career Development',
                                            'Business & Entrepreneurship',
                                            'Mental Health & Wellness',
                                            'Financial Literacy',
                                            'Leadership Skills',
                                            'Technical Skills (IT/Tech)',
                                            'Education & Training',
                                            'Legal Advice',
                                            'Health & Nutrition',
                                            'Life Coaching',
                                        ];
                                        @endphp
                                        @foreach($expertiseOptions as $option)
                                        <label class="flex items-center cursor-pointer group">
                                            <input type="checkbox" name="expertise[]" value="{{ $option }}"
                                                   class="expertise-checkbox">
                                            <span class="expertise-label group-hover:border-[#874179]">
                                                <i class="fas fa-check text-[#874179] mr-2 hidden"></i>
                                                {{ $option }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label for="bio" class="block text-sm font-semibold text-gray-900 mb-2">Professional Bio <span class="text-red-500">*</span></label>
                                    <textarea name="bio" id="bio" rows="5" required
                                              placeholder="Share your professional background, achievements, and what value you bring as a mentor..."
                                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all"></textarea>
                                    <div class="mt-2 flex justify-between items-center">
                                        <p class="text-xs text-gray-600"><i class="fas fa-pen-fancy mr-1" style="color: #874179;"></i> 2–4 sentences recommended</p>
                                        <span id="bio-count" class="text-xs text-gray-500">0 / 500 characters</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">Initial Status</label>
                                        <select name="status" id="status"
                                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all">
                                            <option value="active">Active</option>
                                            <option value="pending">Pending Approval</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="availability" class="block text-sm font-semibold text-gray-900 mb-2">Availability & Schedule</label>
                                        <input type="text" name="availability" id="availability"
                                               placeholder="e.g., Mon–Thu 9:00–14:00, Sat 10:00–13:00"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: ADDITIONAL INFO -->
                        <div id="additional" class="tab-content form-section">
                            <div class="space-y-8">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Social & Professional Profiles</h3>
                                    <div class="space-y-4" id="socialProfilesContainer">
                                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-[#F3E6F1] hover:border-[#874179] transition-all">
                                            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                                <i class="fab fa-linkedin text-2xl text-blue-700"></i>
                                            </div>
                                            <input type="url" name="linkedin_url" id="linkedin_url" placeholder="https://linkedin.com/in/..."
                                                   class="flex-1 bg-transparent border-0 focus:ring-0 text-sm placeholder-gray-500">
                                        </div>
                                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-[#F3E6F1] hover:border-[#874179] transition-all">
                                            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                                <i class="fab fa-twitter text-2xl text-sky-500"></i>
                                            </div>
                                            <input type="url" name="twitter_url" id="twitter_url" placeholder="https://twitter.com/..."
                                                   class="flex-1 bg-transparent border-0 focus:ring-0 text-sm placeholder-gray-500">
                                        </div>
                                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-[#F3E6F1] hover:border-[#874179] transition-all">
                                            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                                <i class="fas fa-globe text-2xl" style="color: #874179;"></i>
                                            </div>
                                            <input type="url" name="website_url" id="website_url" placeholder="https://yourwebsite.com"
                                                   class="flex-1 bg-transparent border-0 focus:ring-0 text-sm placeholder-gray-500">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-gray-900 mb-2">Additional Notes (Internal)</label>
                                    <textarea name="notes" id="notes" rows="4"
                                              placeholder="Add any internal notes about this mentor, e.g., special training needs, recommendations..."
                                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] transition-all"></textarea>
                                </div>

                                <div class="p-4 rounded-lg" style="background: #F9F0F7; border: 1px solid #E9D5FF;">
                                    <h4 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                                        <i class="fas fa-bell mr-2" style="color: #874179;"></i> Notification Preferences
                                    </h4>
                                    <div class="space-y-3">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="notify_welcome" value="1" class="w-4 h-4 rounded border-gray-300 focus:ring-2 focus:ring-[#874179]">
                                            <span class="ml-2 text-sm text-gray-700">Send welcome email to mentor</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="notify_training" value="1" class="w-4 h-4 rounded border-gray-300 focus:ring-2 focus:ring-[#874179]">
                                            <span class="ml-2 text-sm text-gray-700">Notify about upcoming training sessions</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 md:px-8 py-6 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-between gap-4">
                        <div class="flex gap-3">
                            <a href="{{ route('admin.mentors.index') }}"
                               class="flex items-center justify-center px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" id="prevBtn"
                                    class="hidden sm:flex items-center justify-center px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <button type="button" id="nextBtn"
                                    class="hidden sm:flex items-center justify-center px-6 py-2.5 text-white font-medium rounded-lg transition-colors" style="background: #874179;">
                                Next <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="hidden sm:flex items-center justify-center px-8 py-2.5 text-white font-medium rounded-lg transition-all shadow-md" style="background: linear-gradient(135deg, #874179, #af5c9c);">
                                <i class="fas fa-check mr-2"></i> Create Mentor
                            </button>
                        </div>
                    </div>

                    <div class="sm:hidden px-6 py-4 bg-gray-50 border-t border-gray-200 space-y-3">
                        <button type="button" id="mobileNextBtn"
                                class="w-full flex items-center justify-center px-6 py-3 text-white font-medium rounded-lg transition-colors" style="background: #874179;">
                            Continue <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        <button type="submit"
                                class="w-full flex items-center justify-center px-6 py-3 text-white font-medium rounded-lg transition-all shadow-md" style="background: linear-gradient(135deg, #874179, #af5c9c);">
                            <i class="fas fa-check mr-2"></i> Create Mentor
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    // Toast notification
    function showToast(message, isError = false) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        toast.style.backgroundColor = isError ? '#dc2626' : '#10b981';
        document.body.appendChild(toast);
        toast.classList.add('show');
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Logout handler
    window.handleLogout = function() {
        showToast("✅ Logged out successfully.");
        setTimeout(() => {
            window.location.href = "{{ route('admin.login') }}";
        }, 1000);
    };

    // Photo Preview
    document.getElementById('photo')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) { showToast('Please select an image file', true); return; }
        if (file.size > 2 * 1024 * 1024) { showToast('File size must be less than 2MB', true); return; }
        const reader = new FileReader();
        reader.onload = ev => {
            document.getElementById('photo-preview').innerHTML =
                `<img src="${ev.target.result}" alt="Preview" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(file);
    });

    // Tab Navigation
    let currentTab = 'basic';
    const tabs = ['basic', 'professional', 'additional'];

    document.querySelectorAll('.tab-button').forEach(btn =>
        btn.addEventListener('click', () => showTab(btn.dataset.tab))
    );

    function showTab(name) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-button').forEach(b => {
            b.classList.remove('active', 'border-purple-600', 'text-gray-900');
            b.classList.add('border-transparent', 'text-gray-600');
        });
        document.getElementById(name).classList.add('active');
        const activeBtn = document.querySelector(`[data-tab="${name}"]`);
        activeBtn.classList.add('active');
        activeBtn.style.borderColor = '#874179';
        activeBtn.style.color = '#874179';
        currentTab = name;
        const stepMap = { basic: 1, professional: 2, additional: 3 };
        document.getElementById('currentStep').textContent = stepMap[name];
        updateNavButtons();
    }

    function updateNavButtons() {
        const idx = tabs.indexOf(currentTab);
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (prevBtn) prevBtn.classList.toggle('hidden', idx === 0);
        if (nextBtn) nextBtn.classList.toggle('hidden', idx === tabs.length - 1);
        if (submitBtn) submitBtn.classList.toggle('hidden', idx !== tabs.length - 1);
    }

    document.getElementById('nextBtn')?.addEventListener('click', () => {
        const idx = tabs.indexOf(currentTab);
        if (idx < tabs.length - 1) { showTab(tabs[idx + 1]); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    });
    document.getElementById('prevBtn')?.addEventListener('click', () => {
        const idx = tabs.indexOf(currentTab);
        if (idx > 0) { showTab(tabs[idx - 1]); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    });
    document.getElementById('mobileNextBtn')?.addEventListener('click', () => {
        const idx = tabs.indexOf(currentTab);
        if (idx < tabs.length - 1) { showTab(tabs[idx + 1]); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    });

    // Password Show/Hide Toggle
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Password Strength Meter
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const matchEl = document.getElementById('password-match');
    const bars = [1,2,3,4].map(n => document.getElementById(`bar${n}`));
    const strengthLabel = document.getElementById('strength-label');

    const reqs = {
        length: { el: document.getElementById('req-length'), test: v => v.length >= 12 },
        upper: { el: document.getElementById('req-upper'), test: v => /[A-Z]/.test(v) },
        lower: { el: document.getElementById('req-lower'), test: v => /[a-z]/.test(v) },
        number: { el: document.getElementById('req-number'), test: v => /[0-9]/.test(v) },
        special: { el: document.getElementById('req-special'), test: v => /[^A-Za-z0-9]/.test(v) },
    };

    const strengthConfig = [
        { color: 'bg-red-500', label: 'Weak', labelClass: 'text-red-600' },
        { color: 'bg-orange-400', label: 'Fair', labelClass: 'text-orange-500' },
        { color: 'bg-yellow-400', label: 'Good', labelClass: 'text-yellow-600' },
        { color: 'bg-green-500', label: 'Strong', labelClass: 'text-green-600' },
        { color: 'bg-emerald-600', label: 'Very Strong', labelClass: 'text-emerald-700' },
    ];

    passwordInput?.addEventListener('input', function () {
        const val = this.value;

        Object.values(reqs).forEach(({ el, test }) => {
            const pass = test(val);
            el.classList.toggle('text-green-600', pass);
            el.classList.toggle('text-gray-400', !pass);
            el.querySelector('i').className = pass ? 'fas fa-check-circle' : 'fas fa-circle-dot';
        });

        const score = Object.values(reqs).filter(({ test }) => test(val)).length;
        bars.forEach((bar, i) => {
            if (bar) bar.className = `strength-bar flex-1 ${i < score ? strengthConfig[score - 1].color : 'bg-gray-200'}`;
        });
        if (val.length === 0) {
            if (strengthLabel) strengthLabel.textContent = '';
        } else {
            const cfg = strengthConfig[score - 1] || strengthConfig[0];
            if (strengthLabel) {
                strengthLabel.textContent = cfg.label;
                strengthLabel.className = `text-xs font-medium ${cfg.labelClass}`;
            }
        }
        checkPasswordMatch();
    });

    function checkPasswordMatch() {
        if (!confirmInput || !confirmInput.value) { if(matchEl) matchEl.innerHTML = ''; return; }
        if (passwordInput.value === confirmInput.value) {
            if(matchEl) {
                matchEl.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-1"></i> Passwords match';
                matchEl.className = 'mt-2 text-xs text-green-600';
            }
        } else {
            if(matchEl) {
                matchEl.innerHTML = '<i class="fas fa-times-circle text-red-600 mr-1"></i> Passwords do not match';
                matchEl.className = 'mt-2 text-xs text-red-600';
            }
        }
    }
    confirmInput?.addEventListener('input', checkPasswordMatch);

    // Bio character counter
    const bioField = document.getElementById('bio');
    const bioCount = document.getElementById('bio-count');
    if (bioField && bioCount) {
        bioField.addEventListener('input', () => {
            bioCount.textContent = `${bioField.value.length} / 500 characters`;
        });
    }

    // Expertise checkbox icon toggle
    document.querySelectorAll('.expertise-checkbox').forEach(cb => {
        const icon = cb.nextElementSibling?.querySelector('i');
        const toggle = () => icon?.classList.toggle('hidden', !cb.checked);
        cb.addEventListener('change', toggle);
        toggle();
    });

    // Initialize
    updateNavButtons();
</script>
</body>
</html>