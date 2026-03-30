<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Mentor – Tithandizane Women Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #3b82f6 !important;
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

        /* Expertise checkboxes */
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
            border-color: #3b82f6;
            background: #eff6ff;
            color: #1e40af;
        }

        /* Tab navigation */
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-button { transition: all 0.3s ease; }
        .tab-button.active {
            border-bottom: 3px solid #3b82f6;
            color: #1e40af;
        }

        /* Password strength meter */
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen antialiased">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="hidden lg:flex lg:flex-col lg:w-72 xl:w-80 bg-gray-900 text-white flex-shrink-0 fixed h-screen">
        <div class="p-6 border-b border-gray-800">
            <h1 class="text-2xl font-bold tracking-tight">Tithandizane</h1>
            <p class="text-sm text-teal-400 font-medium">Women Hub</p>
        </div>
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800/70 rounded-lg transition-colors">
                <i class="fas fa-home w-5 mr-3"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('admin.mentors.index') }}" class="flex items-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium shadow-sm">
                <i class="fas fa-chalkboard-teacher w-5 mr-3"></i><span>Mentors</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800/70 rounded-lg transition-colors">
                <i class="fas fa-flag w-5 mr-3"></i><span>Harassment Reports</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800/70 rounded-lg transition-colors">
                <i class="fas fa-book-open w-5 mr-3"></i><span>Guidance Content</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800/70 rounded-lg transition-colors">
                <i class="fas fa-users w-5 mr-3"></i><span>Users</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800/70 rounded-lg transition-colors">
                <i class="fas fa-cog w-5 mr-3"></i><span>Settings</span>
            </a>
        </nav>
        <!-- Sidebar footer — real admin credentials -->
        <div class="p-6 border-t border-gray-800 mt-auto">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($adminName) }}&background=0D9488&color=fff&size=128"
                     class="w-11 h-11 rounded-full ring-2 ring-gray-700/60" alt="{{ $adminName }}">
                <div class="ml-3 min-w-0">
                    <p class="text-sm font-medium truncate">{{ $adminName }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $adminEmail }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col lg:ml-72 xl:ml-80">

        <!-- Topbar — real admin credentials -->
        <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-10">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900">Add New Mentor</h1>
                    <p class="text-sm text-gray-600 mt-0.5">Create and configure a professional mentor profile</p>
                </div>
                <div class="flex items-center space-x-5">
                    <button type="button" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                    </button>
                    <button type="button" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-envelope text-xl"></i>
                    </button>
                    <div class="hidden sm:flex items-center space-x-3 pl-4 border-l border-gray-200">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $adminName }}</p>
                            <p class="text-xs text-gray-500">{{ $adminEmail }}</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($adminName) }}&background=0D9488&color=fff&size=128"
                             class="w-10 h-10 rounded-full" alt="{{ $adminName }}">
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- Breadcrumb -->
            <nav class="mb-8 text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-gray-600">
                    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors flex items-center">
                        <i class="fas fa-home mr-1.5 text-xs"></i> Dashboard
                    </a></li>
                    <li class="text-gray-400"><i class="fas fa-chevron-right text-xs"></i></li>
                    <li><a href="{{ route('admin.mentors.index') }}" class="hover:text-blue-600 transition-colors">Mentors</a></li>
                    <li class="text-gray-400"><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-gray-500 font-medium">Add New Mentor</li>
                </ol>
            </nav>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden max-w-4xl mx-auto">
                <form action="{{ route('admin.mentors.store') }}" method="POST" enctype="multipart/form-data" id="mentorForm" class="divide-y divide-gray-100">
                    @csrf

                    <!-- Header — more visible blue gradient -->
                    <div class="px-6 md:px-8 py-6 border-b border-blue-200"
                         style="background: linear-gradient(135deg, #bfdbfe 0%, #c7d2fe 50%, #bfdbfe 100%);">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Mentor Registration</h2>
                                <p class="mt-2 text-sm text-gray-700">
                                    Complete the form below to add a new mentor to the platform.
                                    All fields marked with <span class="text-red-500 font-semibold">*</span> are required.
                                </p>
                            </div>
                            <div class="hidden sm:block">
                                <div class="inline-flex items-center space-x-2 bg-white/80 backdrop-blur px-4 py-2 rounded-lg border border-blue-300 shadow-sm">
                                    <i class="fas fa-info-circle text-blue-600 text-sm"></i>
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
                            <button type="button" class="tab-button active py-4 px-1 font-medium text-gray-900 border-b-2 border-blue-600" data-tab="basic">
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
                                                 class="w-full aspect-square rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden shadow-inner transition-all hover:border-blue-400 hover:from-blue-50 hover:to-blue-100">
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
                                            <i class="fas fa-check-circle text-green-600 mr-1"></i> PNG, JPG
                                            <br><i class="fas fa-info-circle text-blue-600 mr-1"></i> Max 2MB
                                        </p>
                                    </div>
                                </div>

                                <!-- Basic Fields -->
                                <div class="lg:col-span-3 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Full Name -->
                                        <div>
                                            <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Full Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                                   placeholder="e.g., Jane Smith"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('name') border-red-400 @enderror">
                                            @error('name')<p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                                        </div>
                                        <!-- Email -->
                                        <div>
                                            <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address <span class="text-red-500">*</span></label>
                                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                                   placeholder="jane.smith@example.com"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('email') border-red-400 @enderror">
                                            @error('email')<p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                                        </div>
                                        <!-- Phone -->
                                        <div>
                                            <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">Phone Number</label>
                                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                                   placeholder="+265 99 123 4567"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('phone') border-red-400 @enderror">
                                            @error('phone')<p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                                        </div>
                                        <!-- Location -->
                                        <div>
                                            <label for="location" class="block text-sm font-semibold text-gray-900 mb-2">Location</label>
                                            <input type="text" name="location" id="location" value="{{ old('location') }}"
                                                   placeholder="e.g., Lilongwe, Malawi"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                        </div>
                                    </div>

                                    <!-- Password Section -->
                                    <div class="pt-6 border-t border-gray-200">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-1">Account Credentials</h4>
                                        <p class="text-xs text-gray-500 mb-4">
                                            Password must be at least 12 characters and include uppercase letters, lowercase letters, numbers, and special characters (e.g. <span class="font-mono">!@#$%^&*</span>).
                                        </p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Password -->
                                            <div>
                                                <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">Password <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="password" name="password" id="password" required
                                                           placeholder="••••••••••••"
                                                           class="block w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('password') border-red-400 @enderror">
                                                    <button type="button" class="toggle-pw absolute right-3 top-3.5 text-gray-400 hover:text-gray-600" data-target="password">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </button>
                                                </div>
                                                <!-- Strength meter -->
                                                <div class="mt-2 space-y-1">
                                                    <div class="flex gap-1">
                                                        <div class="strength-bar flex-1 bg-gray-200" id="bar1"></div>
                                                        <div class="strength-bar flex-1 bg-gray-200" id="bar2"></div>
                                                        <div class="strength-bar flex-1 bg-gray-200" id="bar3"></div>
                                                        <div class="strength-bar flex-1 bg-gray-200" id="bar4"></div>
                                                    </div>
                                                    <p id="strength-label" class="text-xs text-gray-500"></p>
                                                </div>
                                                @error('password')<p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                                            </div>
                                            <!-- Confirm Password -->
                                            <div>
                                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                                           placeholder="••••••••••••"
                                                           class="block w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                                    <button type="button" class="toggle-pw absolute right-3 top-3.5 text-gray-400 hover:text-gray-600" data-target="password_confirmation">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </button>
                                                </div>
                                                <div id="password-match" class="mt-2 text-xs text-gray-500"></div>
                                            </div>
                                        </div>
                                        <!-- Requirements checklist -->
                                        <div class="mt-4 grid grid-cols-2 gap-1 text-xs" id="pw-reqs">
                                            <span id="req-length"  class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> 12+ characters</span>
                                            <span id="req-upper"   class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> Uppercase letter</span>
                                            <span id="req-lower"   class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> Lowercase letter</span>
                                            <span id="req-number"  class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> Number</span>
                                            <span id="req-special" class="flex items-center gap-1 text-gray-400"><i class="fas fa-circle-dot"></i> Special character</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: PROFESSIONAL INFO -->
                        <div id="professional" class="tab-content form-section">
                            <div class="space-y-8">
                                <!-- Areas of Expertise -->
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
                                                   @if(in_array($option, old('expertise', []))) checked @endif
                                                   class="expertise-checkbox">
                                            <span class="expertise-label group-hover:border-blue-400">
                                                <i class="fas fa-check text-blue-600 mr-2 hidden"></i>
                                                {{ $option }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                    @error('expertise')<p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                                </div>

                                <!-- Professional Bio -->
                                <div>
                                    <label for="bio" class="block text-sm font-semibold text-gray-900 mb-2">Professional Bio <span class="text-red-500">*</span></label>
                                    <textarea name="bio" id="bio" rows="5" required
                                              placeholder="Share your professional background, achievements, and what value you bring as a mentor..."
                                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('bio') border-red-400 @enderror">{{ old('bio') }}</textarea>
                                    <div class="mt-2 flex justify-between items-center">
                                        <p class="text-xs text-gray-600"><i class="fas fa-pen-fancy mr-1 text-blue-600"></i> 2–4 sentences recommended</p>
                                        <span id="bio-count" class="text-xs text-gray-500">0 / 500 characters</span>
                                    </div>
                                    @error('bio')<p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                                </div>

                                <!-- Status + Availability (removed: Years of Experience, Maximum Mentees) -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">Initial Status</label>
                                        <select name="status" id="status"
                                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                                            <option value="active"  {{ old('status') == 'active'  ? 'selected' : '' }}>Active</option>
                                            <option value="inactive"{{ old('status') == 'inactive'? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="availability" class="block text-sm font-semibold text-gray-900 mb-2">Availability & Schedule</label>
                                        <input type="text" name="availability" id="availability"
                                               placeholder="e.g., Mon–Thu 9:00–14:00, Sat 10:00–13:00"
                                               value="{{ old('availability') }}"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('availability') border-red-400 @enderror">
                                        <p class="mt-2 text-xs text-gray-600"><i class="fas fa-clock mr-1 text-blue-600"></i> Specify mentor's working hours and days</p>
                                        @error('availability')<p class="mt-1 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: ADDITIONAL INFO -->
                        <div id="additional" class="tab-content form-section">
                            <div class="space-y-8">
                                <!-- Social Profiles -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Social & Professional Profiles</h3>
                                    <div class="space-y-4">
                                        <!-- LinkedIn -->
                                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-200 transition-all">
                                            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                                <i class="fab fa-linkedin text-2xl text-blue-700"></i>
                                            </div>
                                            <input type="url" name="linkedin_url" placeholder="https://linkedin.com/in/..." value="{{ old('linkedin_url') }}"
                                                   class="flex-1 bg-transparent border-0 focus:ring-0 text-sm placeholder-gray-500">
                                        </div>
                                        <!-- Twitter -->
                                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-sky-50 hover:border-sky-200 transition-all">
                                            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                                <i class="fab fa-twitter text-2xl text-sky-500"></i>
                                            </div>
                                            <input type="url" name="twitter_url" placeholder="https://twitter.com/..." value="{{ old('twitter_url') }}"
                                                   class="flex-1 bg-transparent border-0 focus:ring-0 text-sm placeholder-gray-500">
                                        </div>
                                        <!-- Website -->
                                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-purple-50 hover:border-purple-200 transition-all">
                                            <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                                <i class="fas fa-globe text-2xl text-purple-600"></i>
                                            </div>
                                            <input type="url" name="website_url" placeholder="https://yourwebsite.com" value="{{ old('website_url') }}"
                                                   class="flex-1 bg-transparent border-0 focus:ring-0 text-sm placeholder-gray-500">
                                        </div>
                                    </div>
                                    <p class="mt-3 text-xs text-gray-600"><i class="fas fa-info-circle mr-1 text-blue-600"></i> All social profile fields are optional</p>
                                </div>

                                <!-- Additional Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-gray-900 mb-2">Additional Notes (Internal)</label>
                                    <textarea name="notes" id="notes" rows="4"
                                              placeholder="Add any internal notes about this mentor, e.g., special training needs, recommendations..."
                                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">{{ old('notes') }}</textarea>
                                    <p class="mt-2 text-xs text-gray-600"><i class="fas fa-lock mr-1 text-gray-500"></i> These notes are visible only to admin</p>
                                </div>

                                <!-- Notification Preferences -->
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 text-sm mb-3 flex items-center">
                                        <i class="fas fa-bell text-blue-600 mr-2"></i> Notification Preferences
                                    </h4>
                                    <div class="space-y-3">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="notify_welcome" value="1"
                                                   @if(old('notify_welcome')) checked @endif
                                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-200">
                                            <span class="ml-2 text-sm text-gray-700">Send welcome email to mentor</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="notify_training" value="1"
                                                   @if(old('notify_training')) checked @endif
                                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-200">
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
                                    class="hidden sm:flex items-center justify-center px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                Next <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="hidden sm:flex items-center justify-center px-8 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md">
                                <i class="fas fa-check mr-2"></i> Create Mentor
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Submit -->
                    <div class="sm:hidden px-6 py-4 bg-gray-50 border-t border-gray-200 space-y-3">
                        <button type="button" id="mobileNextBtn"
                                class="w-full flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Continue <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        <button type="submit"
                                class="w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all shadow-md">
                            <i class="fas fa-check mr-2"></i> Create Mentor
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    // ── Photo Preview ─────────────────────────────────────────────────────────
    document.getElementById('photo')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) { alert('Please select an image file'); return; }
        if (file.size > 2 * 1024 * 1024)    { alert('File size must be less than 2MB'); return; }
        const reader = new FileReader();
        reader.onload = ev => {
            document.getElementById('photo-preview').innerHTML =
                `<img src="${ev.target.result}" alt="Preview" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(file);
    });

    // ── Tab Navigation ────────────────────────────────────────────────────────
    let currentTab = 'basic';
    const tabs = ['basic', 'professional', 'additional'];

    document.querySelectorAll('.tab-button').forEach(btn =>
        btn.addEventListener('click', () => showTab(btn.dataset.tab))
    );

    function showTab(name) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-button').forEach(b => {
            b.classList.remove('active', 'border-blue-600', 'text-gray-900');
            b.classList.add('border-transparent', 'text-gray-600');
        });
        document.getElementById(name).classList.add('active');
        document.querySelector(`[data-tab="${name}"]`).classList.add('active', 'border-blue-600', 'text-gray-900');
        currentTab = name;
        const stepMap = { basic: 1, professional: 2, additional: 3 };
        document.getElementById('currentStep').textContent = stepMap[name];
        updateNavButtons();
    }

    function updateNavButtons() {
        const idx = tabs.indexOf(currentTab);
        const prevBtn   = document.getElementById('prevBtn');
        const nextBtn   = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        prevBtn?.classList.toggle('hidden', idx === 0);
        nextBtn?.classList.toggle('hidden', idx === tabs.length - 1);
        submitBtn?.classList.toggle('hidden', idx !== tabs.length - 1);
        // Remove sm: prefix toggle to just use hidden on mobile
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

    // ── Password: Show/Hide Toggle ────────────────────────────────────────────
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // ── Password: Strength Meter + Requirements ───────────────────────────────
    const passwordInput = document.getElementById('password');
    const confirmInput  = document.getElementById('password_confirmation');
    const matchEl       = document.getElementById('password-match');
    const bars          = [1,2,3,4].map(n => document.getElementById(`bar${n}`));
    const strengthLabel = document.getElementById('strength-label');

    const reqs = {
        length:  { el: document.getElementById('req-length'),  test: v => v.length >= 12 },
        upper:   { el: document.getElementById('req-upper'),   test: v => /[A-Z]/.test(v) },
        lower:   { el: document.getElementById('req-lower'),   test: v => /[a-z]/.test(v) },
        number:  { el: document.getElementById('req-number'),  test: v => /[0-9]/.test(v) },
        special: { el: document.getElementById('req-special'), test: v => /[^A-Za-z0-9]/.test(v) },
    };

    const strengthConfig = [
        { color: 'bg-red-500',    label: 'Weak',      labelClass: 'text-red-600'    },
        { color: 'bg-orange-400', label: 'Fair',      labelClass: 'text-orange-500' },
        { color: 'bg-yellow-400', label: 'Good',      labelClass: 'text-yellow-600' },
        { color: 'bg-green-500',  label: 'Strong',    labelClass: 'text-green-600'  },
        { color: 'bg-emerald-600',label: 'Very Strong', labelClass: 'text-emerald-700' },
    ];

    passwordInput?.addEventListener('input', function () {
        const val = this.value;

        // Requirements checklist
        Object.values(reqs).forEach(({ el, test }) => {
            const pass = test(val);
            el.classList.toggle('text-green-600', pass);
            el.classList.toggle('text-gray-400', !pass);
            el.querySelector('i').className = pass ? 'fas fa-check-circle' : 'fas fa-circle-dot';
        });

        // Score
        const score = Object.values(reqs).filter(({ test }) => test(val)).length;
        bars.forEach((bar, i) => {
            bar.className = `strength-bar flex-1 ${i < score ? strengthConfig[score - 1].color : 'bg-gray-200'}`;
        });
        if (val.length === 0) {
            strengthLabel.textContent = '';
        } else {
            const cfg = strengthConfig[score - 1] || strengthConfig[0];
            strengthLabel.textContent = cfg.label;
            strengthLabel.className   = `text-xs font-medium ${cfg.labelClass}`;
        }

        checkPasswordMatch();
    });

    function checkPasswordMatch() {
        if (!confirmInput.value) { matchEl.innerHTML = ''; return; }
        if (passwordInput.value === confirmInput.value) {
            matchEl.innerHTML   = '<i class="fas fa-check-circle text-green-600 mr-1"></i> Passwords match';
            matchEl.className   = 'mt-2 text-xs text-green-600';
        } else {
            matchEl.innerHTML   = '<i class="fas fa-times-circle text-red-600 mr-1"></i> Passwords do not match';
            matchEl.className   = 'mt-2 text-xs text-red-600';
        }
    }
    confirmInput?.addEventListener('input', checkPasswordMatch);

    // ── Bio character counter ─────────────────────────────────────────────────
    const bioField  = document.getElementById('bio');
    const bioCount  = document.getElementById('bio-count');
    if (bioField) {
        bioField.addEventListener('input', () => {
            bioCount.textContent = `${bioField.value.length} / 500 characters`;
        });
        if (bioField.value) bioCount.textContent = `${bioField.value.length} / 500 characters`;
    }

    // ── Expertise checkbox icon toggle ────────────────────────────────────────
    document.querySelectorAll('.expertise-checkbox').forEach(cb => {
        const icon = cb.nextElementSibling?.querySelector('i');
        const toggle = () => icon?.classList.toggle('hidden', !cb.checked);
        cb.addEventListener('change', toggle);
        toggle();
    });

    // ── Form validation ───────────────────────────────────────────────────────
    document.querySelector('form')?.addEventListener('submit', function (e) {
        const name     = document.getElementById('name')?.value.trim();
        const email    = document.getElementById('email')?.value.trim();
        const password = passwordInput?.value;
        const confirm  = confirmInput?.value;
        const expertise= document.querySelectorAll('input[name="expertise[]"]:checked').length;
        const bio      = bioField?.value.trim();

        if (!name || !email || !password || !confirm || !expertise || !bio) {
            alert('Please fill in all required fields.');
            e.preventDefault(); return;
        }
        if (password !== confirm) {
            alert("Passwords don't match. Please check.");
            e.preventDefault(); return;
        }

        // Enforce strong password client-side
        const allPass = Object.values(reqs).every(({ test }) => test(password));
        if (!allPass) {
            alert('Password does not meet strength requirements. Please use at least 12 characters with uppercase, lowercase, numbers, and a special character.');
            e.preventDefault(); return;
        }
    });

    // Initialise button visibility
    updateNavButtons();
</script>
</body>
</html>