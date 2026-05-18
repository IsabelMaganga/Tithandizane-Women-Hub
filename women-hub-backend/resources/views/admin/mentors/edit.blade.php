{{-- resources/views/admin/mentors/edit.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Edit Mentor - ' . $mentor->name)
@section('page-title', 'Edit Mentor')
@section('page-subtitle', 'Update mentor profile information (Admin only)')

@section('content')
<!-- Breadcrumb -->
<nav class="mb-6 text-sm" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="transition-colors flex items-center" style="color: var(--purple);">
                <i class="fas fa-home mr-1.5 text-xs"></i> Dashboard
            </a>
        </li>
        <li class="text-secondary-color"><i class="fas fa-chevron-right text-xs"></i></li>
        <li>
            <a href="{{ route('admin.mentors.index') }}" class="transition-colors" style="color: var(--purple);">Mentors</a>
        </li>
        <li class="text-secondary-color"><i class="fas fa-chevron-right text-xs"></i></li>
        <li>
            <a href="{{ route('admin.mentors.show', $mentor->id) }}" class="transition-colors" style="color: var(--purple);">{{ $mentor->name }}</a>
        </li>
        <li class="text-secondary-color"><i class="fas fa-chevron-right text-xs"></i></li>
        <li class="font-medium" style="color: var(--text-primary);">Edit</li>
    </ol>
</nav>

<!-- Form Card -->
<div class="rounded-2xl shadow-lg overflow-hidden" style="background: var(--card-bg);">
    <form id="mentorForm" action="{{ route('admin.mentors.update', $mentor->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Header -->
        <div class="px-6 md:px-8 py-6 border-b" style="background: linear-gradient(135deg, var(--light-purple) 0%, var(--card-bg) 100%); border-color: var(--border-color);">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold" style="color: var(--purple);">Edit Mentor Profile</h2>
                    <p class="mt-2 text-sm" style="color: var(--text-secondary);">
                        Update the mentor's information. Fields marked <span style="color: var(--red); font-weight: 600;">*</span> are required.
                    </p>
                </div>
                <div class="hidden sm:block">
                    <div class="inline-flex items-center space-x-2 backdrop-blur px-4 py-2 rounded-lg border shadow-sm" style="background: var(--card-bg); border-color: var(--purple);">
                        <i class="fas fa-info-circle text-sm" style="color: var(--purple);"></i>
                        <span class="text-sm font-medium" style="color: var(--text-secondary);">
                            Step <span id="currentStep">1</span> of <span id="totalSteps">2</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="hidden md:block px-6 md:px-8 border-b" style="background: var(--light-gray); border-color: var(--border-color);">
            <div class="flex space-x-8">
                <button type="button" class="tab-button active py-4 px-1 font-medium border-b-2" data-tab="basic" style="border-color: var(--purple); color: var(--purple);">
                    <i class="fas fa-user mr-2"></i> Basic Info
                </button>
                <button type="button" class="tab-button py-4 px-1 font-medium border-b-2 border-transparent" data-tab="professional" style="color: var(--text-secondary);">
                    <i class="fas fa-briefcase mr-2"></i> Professional
                </button>
                <button type="button" class="tab-button py-4 px-1 font-medium border-b-2 border-transparent" data-tab="additional" style="color: var(--text-secondary);">
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
                            <label class="block text-sm font-semibold mb-4" style="color: var(--text-primary);">Profile Photo</label>
                            <div class="relative">
                                <div id="photo-preview"
                                     class="w-full aspect-square rounded-xl border-2 border-dashed flex items-center justify-center overflow-hidden shadow-inner transition-all cursor-pointer"
                                     style="background: linear-gradient(135deg, var(--light-gray), var(--border-color)); border-color: var(--border-color);">
                                    @if($mentor->photo)
                                        <img src="{{ Storage::url($mentor->photo) }}" alt="Current photo" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-center">
                                            <i class="fas fa-user text-5xl mb-2" style="color: var(--text-secondary);"></i>
                                            <p class="text-xs" style="color: var(--text-secondary);">Click to upload</p>
                                        </div>
                                    @endif
                                </div>
                                <label class="absolute inset-0 rounded-xl cursor-pointer">
                                    <input type="file" name="photo" id="photo" accept="image/*" class="w-full opacity-0 cursor-pointer">
                                </label>
                            </div>
                            <p class="mt-3 text-xs text-center" style="color: var(--text-secondary);">
                                <i class="fas fa-check-circle mr-1" style="color: #10b981;"></i> PNG, JPG
                                <br><i class="fas fa-info-circle mr-1" style="color: var(--purple);"></i> Max 2MB
                                <br><i class="fas fa-sync-alt mr-1" style="color: #f59e0b;"></i> Leave empty to keep current photo
                            </p>
                        </div>
                    </div>

                    <!-- Basic Fields -->
                    <div class="lg:col-span-3 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Full Name <span style="color: var(--red);">*</span></label>
                                <input type="text" name="name" id="name" required placeholder="e.g., Jane Smith"
                                       value="{{ old('name', $mentor->name) }}"
                                       class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Email Address <span style="color: var(--red);">*</span></label>
                                <input type="email" name="email" id="email" required placeholder="jane.smith@example.com"
                                       value="{{ old('email', $mentor->email) }}"
                                       class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Phone Number</label>
                                <input type="tel" name="phone" id="phone" placeholder="+265 99 123 4567"
                                       value="{{ old('phone', $mentor->phone) }}"
                                       class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="location" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Location</label>
                                <input type="text" name="location" id="location" placeholder="e.g., Lilongwe, Malawi"
                                       value="{{ old('location', $mentor->location) }}"
                                       class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Password Section (Optional for edit) -->
                        <div class="pt-6 border-t" style="border-color: var(--border-color);">
                            <h4 class="text-sm font-semibold mb-1" style="color: var(--text-primary);">Change Password</h4>
                            <p class="text-xs mb-4" style="color: var(--text-secondary);">Leave blank to keep current password. Password must be at least 12 characters with uppercase, lowercase, numbers, and special characters.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">New Password</label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password" placeholder="••••••••••••"
                                               class="block w-full px-4 py-3 pr-10 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                               style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                        <button type="button" class="toggle-pw absolute right-3 top-3.5" data-target="password" style="color: #6b7280; transition: color 0.2s;" onmouseover="this.style.color='#9b59b6'" onmouseout="this.style.color='#6b7280'">
                                            <i class="fas fa-eye-slash text-sm"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2 space-y-1">
                                        <div class="flex gap-1">
                                            <div class="strength-bar flex-1" style="background: var(--border-color);" id="bar1"></div>
                                            <div class="strength-bar flex-1" style="background: var(--border-color);" id="bar2"></div>
                                            <div class="strength-bar flex-1" style="background: var(--border-color);" id="bar3"></div>
                                            <div class="strength-bar flex-1" style="background: var(--border-color);" id="bar4"></div>
                                        </div>
                                        <p id="strength-label" class="text-xs" style="color: var(--text-secondary);"></p>
                                    </div>
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Confirm Password</label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••••••"
                                               class="block w-full px-4 py-3 pr-10 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                               style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                        <button type="button" class="toggle-pw absolute right-3 top-3.5" data-target="password_confirmation" style="color: #6b7280; transition: color 0.2s;" onmouseover="this.style.color='#9b59b6'" onmouseout="this.style.color='#6b7280'">
                                            <i class="fas fa-eye-slash text-sm"></i>
                                        </button>
                                    </div>
                                    <div id="password-match" class="mt-2 text-xs"></div>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-1 text-xs" id="pw-reqs">
                                <span id="req-length" class="flex items-center gap-1" style="color: var(--text-secondary);"><i class="fas fa-circle-dot"></i> 12+ characters</span>
                                <span id="req-upper" class="flex items-center gap-1" style="color: var(--text-secondary);"><i class="fas fa-circle-dot"></i> Uppercase letter</span>
                                <span id="req-lower" class="flex items-center gap-1" style="color: var(--text-secondary);"><i class="fas fa-circle-dot"></i> Lowercase letter</span>
                                <span id="req-number" class="flex items-center gap-1" style="color: var(--text-secondary);"><i class="fas fa-circle-dot"></i> Number</span>
                                <span id="req-special" class="flex items-center gap-1" style="color: var(--text-secondary);"><i class="fas fa-circle-dot"></i> Special character</span>
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
                        <label class="block text-sm font-semibold mb-1" style="color: var(--text-primary);">Areas of Expertise <span style="color: var(--red);">*</span></label>
                        
                        <div class="flex gap-2 mb-4">
                            <select id="expertiseDropdown"
                                    class="flex-1 px-4 py-3 border rounded-lg shadow-sm transition-all text-sm focus:ring-2 focus:ring-purple-500"
                                    style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Choose an area of expertise</option>
                                @php
                                $expertiseOptions = [
                                    'Career Development', 'Business & Entrepreneurship', 'Mental Health & Wellness',
                                    'Financial Literacy', 'Leadership Skills', 'Technical Skills (IT/Tech)',
                                    'Education & Training', 'Legal Advice', 'Health & Nutrition', 'Life Coaching',
                                    'Agriculture & Rural Development', 'Arts & Creative Industries', 'Gender & Human Rights',
                                    'Community Development', 'Parenting & Family Support',
                                ];
                                @endphp
                                @foreach($expertiseOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="addExpertiseBtn"
                                    class="px-4 py-3 rounded-lg text-sm font-semibold text-white transition flex items-center gap-2 shrink-0 hover:opacity-90"
                                    style="background: var(--purple);">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>

                        <div id="customExpertiseWrap" class="hidden mb-4">
                            <div class="flex gap-2">
                                <input type="text" id="customExpertiseInput" placeholder="Type a custom area of expertise..."
                                       class="flex-1 px-4 py-3 border border-dashed rounded-lg text-sm focus:ring-2 focus:ring-purple-500"
                                       style="border-color: var(--purple); background: var(--card-bg); color: var(--text-primary);">
                                <button type="button" id="addCustomExpertiseBtn"
                                        class="px-4 py-3 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
                                        style="background: #10b981;">
                                    <i class="fas fa-check"></i> Add
                                </button>
                                <button type="button" id="cancelCustomExpertiseBtn"
                                        class="px-3 py-3 rounded-lg text-sm transition border hover:bg-gray-100"
                                        style="color: var(--text-secondary); border-color: var(--border-color);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" id="showCustomExpertiseBtn"
                                class="mb-4 flex items-center gap-2 text-sm font-medium transition px-3 py-2 rounded-lg border border-dashed hover:opacity-80"
                                style="color: var(--purple); border-color: var(--purple); background: var(--light-purple);">
                            <i class="fas fa-plus-circle"></i> Add other (not in the list)
                        </button>

                        <div id="expertiseTags" class="flex flex-wrap gap-2 min-h-[2rem]">
                            <p class="text-xs italic" id="expertiseEmptyHint" style="color: var(--text-secondary);">No areas selected yet. Use the dropdown above to add expertise.</p>
                        </div>
                        <div id="expertiseHiddenInputs">
                            @php
                                $expertiseArray = is_string($mentor->expertise) ? json_decode($mentor->expertise, true) : $mentor->expertise;
                                if (!is_array($expertiseArray)) $expertiseArray = [];
                            @endphp
                            @foreach($expertiseArray as $skill)
                                <input type="hidden" name="expertise[]" value="{{ $skill }}">
                            @endforeach
                        </div>
                    </div>

                    <!-- Professional Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Professional Bio <span style="color: var(--red);">*</span></label>
                        <textarea name="bio" id="bio" rows="5" required
                                  placeholder="Share your professional background, achievements, and what value you bring as a mentor..."
                                  class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                  style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">{{ old('bio', $mentor->bio) }}</textarea>
                        <div class="mt-2 flex justify-between items-center">
                            <p class="text-xs" style="color: var(--text-secondary);"><i class="fas fa-pen-fancy mr-1" style="color: var(--purple);"></i> 2–4 sentences recommended</p>
                            <span id="bio-count" class="text-xs" style="color: var(--text-secondary);">{{ strlen($mentor->bio ?? '') }} / 500 characters</span>
                        </div>
                        @error('bio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status (Admin approval) -->
                    <div>
                        <label for="status" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Approval Status</label>
                        <select name="status" id="status"
                                class="block w-full md:w-1/2 px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="active" {{ $mentor->status == 'active' ? 'selected' : '' }}>✅ Active - Approved</option>
                            <option value="pending" {{ $mentor->status == 'pending' ? 'selected' : '' }}>⏳ Pending - Awaiting Approval</option>
                            <option value="inactive" {{ $mentor->status == 'inactive' ? 'selected' : '' }}>❌ Inactive - Rejected/Suspended</option>
                        </select>
                        <p class="text-xs mt-2" style="color: var(--text-secondary);">
                            <i class="fas fa-info-circle mr-1" style="color: var(--purple);"></i> 
                            <strong>Active:</strong> Mentor can be matched with mentees<br>
                            <strong>Pending:</strong> Mentor is waiting for approval<br>
                            <strong>Inactive:</strong> Mentor is hidden from the platform
                        </p>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- TAB 3: ADDITIONAL INFO -->
            <div id="additional" class="tab-content form-section">
                <div class="space-y-8">
                    <!-- Online Profiles & Links -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Online Profiles & Links</h3>
                                <p class="text-xs mt-1" style="color: var(--text-secondary);">Add any professional or social profile links relevant to this mentor</p>
                            </div>
                            <button type="button" id="addProfileBtn"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition hover:opacity-80"
                                    style="background: var(--light-purple); color: var(--purple);">
                                <i class="fas fa-plus"></i> Add Profile
                            </button>
                        </div>

                        <div id="profilesList" class="space-y-3">
                            <!-- LinkedIn if exists -->
                            @if($mentor->linkedin_url)
                            <div class="profile-row">
                                <div class="icon-wrap"><i class="fab fa-linkedin" style="color: #0077b5;"></i></div>
                                <select name="profile_platform[]" class="w-28 text-xs border-0 bg-transparent focus:ring-0">
                                    <option value="linkedin" selected>LinkedIn</option>
                                    <option value="twitter">X / Twitter</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="github">GitHub</option>
                                    <option value="website">Website</option>
                                    <option value="other">Other</option>
                                </select>
                                <input type="url" name="profile_url[]" value="{{ $mentor->linkedin_url }}" placeholder="https://linkedin.com/in/..." class="flex-1">
                                <button type="button" class="remove-profile-btn" title="Remove"><i class="fas fa-times"></i></button>
                            </div>
                            @endif

                            <!-- Twitter if exists -->
                            @if($mentor->twitter_url)
                            <div class="profile-row">
                                <div class="icon-wrap"><i class="fab fa-twitter" style="color: #1DA1F2;"></i></div>
                                <select name="profile_platform[]" class="w-28 text-xs border-0 bg-transparent focus:ring-0">
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="twitter" selected>X / Twitter</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="github">GitHub</option>
                                    <option value="website">Website</option>
                                    <option value="other">Other</option>
                                </select>
                                <input type="url" name="profile_url[]" value="{{ $mentor->twitter_url }}" placeholder="https://twitter.com/..." class="flex-1">
                                <button type="button" class="remove-profile-btn" title="Remove"><i class="fas fa-times"></i></button>
                            </div>
                            @endif

                            <!-- Website if exists -->
                            @if($mentor->website_url)
                            <div class="profile-row">
                                <div class="icon-wrap"><i class="fas fa-globe" style="color: var(--purple);"></i></div>
                                <select name="profile_platform[]" class="w-28 text-xs border-0 bg-transparent focus:ring-0">
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="twitter">X / Twitter</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="github">GitHub</option>
                                    <option value="website" selected>Website</option>
                                    <option value="other">Other</option>
                                </select>
                                <input type="url" name="profile_url[]" value="{{ $mentor->website_url }}" placeholder="https://yourwebsite.com" class="flex-1">
                                <button type="button" class="remove-profile-btn" title="Remove"><i class="fas fa-times"></i></button>
                            </div>
                            @endif

                            @if(!$mentor->linkedin_url && !$mentor->twitter_url && !$mentor->website_url)
                            <div class="profile-row">
                                <div class="icon-wrap"><i class="fas fa-globe" style="color: var(--purple);"></i></div>
                                <select name="profile_platform[]" class="w-28 text-xs border-0 bg-transparent focus:ring-0">
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="twitter">X / Twitter</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="github">GitHub</option>
                                    <option value="website" selected>Website</option>
                                    <option value="other">Other</option>
                                </select>
                                <input type="url" name="profile_url[]" placeholder="https://yourwebsite.com" class="flex-1">
                                <button type="button" class="remove-profile-btn" title="Remove"><i class="fas fa-times"></i></button>
                            </div>
                            @endif
                        </div>
                        <p class="text-xs mt-3" style="color: var(--text-secondary);"><i class="fas fa-lightbulb mr-1" style="color: #f59e0b;"></i> You can add as many profiles as needed — LinkedIn, GitHub, Facebook, personal websites, and more.</p>
                    </div>

                    <!-- Additional Notes (Internal) -->
                    <div>
                        <label for="notes" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Internal Notes (Admin Only)</label>
                        <textarea name="notes" id="notes" rows="4"
                                  placeholder="Add internal notes about this mentor — special training needs, recommendations, background context..."
                                  class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-purple-500"
                                  style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">{{ old('notes', $mentor->notes) }}</textarea>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);"><i class="fas fa-lock mr-1"></i> These notes are only visible to admins</p>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 md:px-8 py-6 border-t flex flex-col sm:flex-row sm:justify-between gap-4" style="border-color: var(--border-color); background: var(--light-gray);">
            <div class="flex gap-3">
                <a href="{{ route('admin.mentors.show', $mentor->id) }}"
                   class="flex items-center justify-center px-6 py-2.5 border rounded-lg font-medium transition-colors hover:bg-gray-50"
                   style="border-color: var(--border-color); color: var(--text-primary); background: var(--card-bg);">
                    <i class="fas fa-eye mr-2"></i> View Profile
                </a>
                <a href="{{ route('admin.mentors.index') }}"
                   class="flex items-center justify-center px-6 py-2.5 border rounded-lg font-medium transition-colors hover:bg-gray-50"
                   style="border-color: var(--border-color); color: var(--text-primary); background: var(--card-bg);">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
            <div class="flex gap-3">
                <button type="button" id="prevBtn"
                        class="hidden sm:flex items-center justify-center px-6 py-2.5 border rounded-lg font-medium transition-colors hover:bg-gray-50"
                        style="border-color: var(--border-color); color: var(--text-primary); background: var(--card-bg);">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button type="button" id="nextBtn"
                        class="hidden sm:flex items-center justify-center px-6 py-2.5 text-white font-medium rounded-lg transition-colors hover:opacity-90"
                        style="background: var(--purple);">
                    Next <i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="submit" id="submitBtn"
                        class="hidden sm:flex items-center justify-center px-8 py-2.5 text-white font-medium rounded-lg transition-all shadow-md hover:opacity-90"
                        style="background: linear-gradient(135deg, var(--purple), var(--purple));">
                    <i class="fas fa-save mr-2"></i> Update Mentor
                </button>
            </div>
        </div>

        <div class="sm:hidden px-6 py-4 border-t space-y-3" style="border-color: var(--border-color); background: var(--light-gray);">
            <button type="button" id="mobileNextBtn"
                    class="w-full flex items-center justify-center px-6 py-3 text-white font-medium rounded-lg transition-colors hover:opacity-90"
                    style="background: var(--purple);">
                Continue <i class="fas fa-arrow-right ml-2"></i>
            </button>
            <button type="submit"
                    class="w-full flex items-center justify-center px-6 py-3 text-white font-medium rounded-lg transition-all shadow-md hover:opacity-90"
                    style="background: linear-gradient(135deg, var(--purple), var(--purple));">
                <i class="fas fa-save mr-2"></i> Update Mentor
            </button>
        </div>
    </form>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal-overlay">
    <div class="success-modal" style="background: var(--card-bg);">
        <div class="checkmark-circle" style="background: linear-gradient(135deg, var(--purple), var(--purple));">
            <i class="fas fa-check checkmark"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Mentor Updated Successfully!</h2>
        <p class="mb-6" style="color: var(--text-secondary);">The mentor profile has been updated and changes are now saved.</p>
        <div class="success-buttons">
            <a href="{{ route('admin.mentors.show', $mentor->id) }}" class="btn-primary" style="background: linear-gradient(135deg, var(--purple), var(--purple)); color: white; text-decoration: none;">
                <i class="fas fa-eye"></i> View Profile
            </a>
            <a href="{{ route('admin.mentors.index') }}" class="btn-secondary" style="background: var(--light-gray); color: var(--text-primary); text-decoration: none;">
                <i class="fas fa-list"></i> Back to List
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-section {
        opacity: 0;
        transform: translateY(10px);
        animation: fadeInUp 0.4s ease-out forwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    .tab-button {
        transition: all 0.3s ease;
    }
    .tab-button.active {
        border-bottom-width: 3px;
    }
    
    .strength-bar {
        height: 4px;
        border-radius: 2px;
        transition: all 0.3s ease;
    }
    
    .expertise-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1.5px solid var(--purple);
        background: var(--light-purple);
        color: var(--purple);
    }
    .expertise-tag button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 0.7rem;
        padding: 0;
        opacity: 0.7;
        color: var(--purple);
    }
    .expertise-tag button:hover { opacity: 1; }
    
    .profile-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border: 1.5px solid var(--border-color);
        transition: all 0.2s;
        background: var(--light-gray);
    }
    .profile-row:hover {
        border-color: var(--purple);
        background: var(--light-purple);
    }
    .profile-row .icon-wrap {
        flex-shrink: 0;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: var(--card-bg);
    }
    .profile-row input {
        flex: 1;
        border: 0;
        background: transparent;
        font-size: 0.875rem;
        color: var(--text-primary);
    }
    .profile-row input:focus { outline: none; box-shadow: none; }
    .profile-row input::placeholder { color: var(--text-secondary); opacity: 0.7; }
    .remove-profile-btn {
        cursor: pointer;
        font-size: 0.85rem;
        background: none;
        border: none;
        padding: 0 0.25rem;
        color: var(--text-secondary);
        transition: color 0.2s;
    }
    .remove-profile-btn:hover { color: #ef4444; }
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    
    .success-modal {
        border-radius: 1.5rem;
        max-width: 500px;
        width: 90%;
        padding: 2rem;
        text-align: center;
        animation: modalSlideIn 0.3s ease-out;
        background: var(--card-bg);
    }
    @keyframes modalSlideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .checkmark-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    .checkmark { font-size: 3rem; color: white; }
    .success-buttons { display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; }
    .btn-primary, .btn-secondary { 
        padding: 0.75rem 1.5rem; 
        border-radius: 0.75rem; 
        font-weight: 600; 
        transition: all 0.3s; 
        display: inline-flex; 
        align-items: center; 
        gap: 0.5rem; 
        cursor: pointer; 
        border: none; 
    }
    .btn-primary:hover, .btn-secondary:hover { transform: translateY(-2px); opacity: 0.9; }
    
    .toast {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        text-align: center;
        border-radius: 8px;
        padding: 16px;
        position: fixed;
        z-index: 1000;
        left: 50%;
        bottom: 30px;
        font-size: 14px;
        transform: translateX(-50%);
        color: #fff;
    }
    .toast.show {
        visibility: visible;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }
    @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
    @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
</style>
@endpush

@push('scripts')
<script>
    function showToast(message, isError = false) {
        document.querySelectorAll('.toast').forEach(t => t.remove());
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        toast.style.backgroundColor = isError ? '#dc2626' : '#10b981';
        document.body.appendChild(toast);
        toast.offsetHeight;
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
    }

    // Password Toggle Functionality
    document.querySelectorAll('.toggle-pw').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            }
        });
    });

    // Initialize existing expertise from the server
    let selectedExpertise = new Set();
    @php
        $expertiseArray = is_string($mentor->expertise) ? json_decode($mentor->expertise, true) : $mentor->expertise;
        if (!is_array($expertiseArray)) $expertiseArray = [];
    @endphp
    @foreach($expertiseArray as $skill)
        selectedExpertise.add('{{ addslashes($skill) }}');
    @endforeach

    function renderExpertiseTags() {
        const tagsContainer = document.getElementById('expertiseTags');
        const hiddenContainer = document.getElementById('expertiseHiddenInputs');
        const emptyHint = document.getElementById('expertiseEmptyHint');
        if (!tagsContainer || !hiddenContainer) return;

        Array.from(tagsContainer.children).forEach(c => { if (c.id !== 'expertiseEmptyHint') c.remove(); });
        hiddenContainer.innerHTML = '';

        if (selectedExpertise.size === 0) {
            if (emptyHint) emptyHint.style.display = '';
        } else {
            if (emptyHint) emptyHint.style.display = 'none';
            selectedExpertise.forEach(val => {
                const tag = document.createElement('span');
                tag.className = 'expertise-tag';
                tag.innerHTML = `${val} <button type="button" data-val="${val}" title="Remove"><i class="fas fa-times"></i></button>`;
                tag.querySelector('button').addEventListener('click', () => {
                    selectedExpertise.delete(val);
                    renderExpertiseTags();
                });
                tagsContainer.appendChild(tag);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'expertise[]';
                input.value = val;
                hiddenContainer.appendChild(input);
            });
        }
    }

    // Photo Preview
    const photoInput = document.getElementById('photo');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) { showToast('Please select an image file', true); return; }
            if (file.size > 2 * 1024 * 1024) { showToast('File size must be less than 2MB', true); return; }
            const reader = new FileReader();
            reader.onload = ev => {
                const preview = document.getElementById('photo-preview');
                if (preview) preview.innerHTML = `<img src="${ev.target.result}" alt="Preview" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(file);
        });
    }

    // Tab Navigation
    let currentTab = 'basic';
    const tabs = ['basic', 'professional', 'additional'];

    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', () => showTab(btn.dataset.tab));
    });

    function showTab(name) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-button').forEach(b => {
            b.classList.remove('active');
            b.style.borderColor = 'transparent';
            b.style.color = 'var(--text-secondary)';
        });
        const activeContent = document.getElementById(name);
        if (activeContent) activeContent.classList.add('active');
        const activeBtn = document.querySelector(`[data-tab="${name}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.style.borderColor = 'var(--purple)';
            activeBtn.style.color = 'var(--purple)';
        }
        currentTab = name;
        const stepMap = { basic: 1, professional: 2, additional: 3 };
        const el = document.getElementById('currentStep');
        if (el) el.textContent = stepMap[name];
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

    // Password Strength
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const matchEl = document.getElementById('password-match');
    const bars = [1,2,3,4].map(n => document.getElementById(`bar${n}`));
    const strengthLabel = document.getElementById('strength-label');

    const reqs = {
        length:  { el: document.getElementById('req-length'),  test: v => v.length >= 12 },
        upper:   { el: document.getElementById('req-upper'),   test: v => /[A-Z]/.test(v) },
        lower:   { el: document.getElementById('req-lower'),   test: v => /[a-z]/.test(v) },
        number:  { el: document.getElementById('req-number'),  test: v => /[0-9]/.test(v) },
        special: { el: document.getElementById('req-special'), test: v => /[^A-Za-z0-9]/.test(v) },
    };

    const strengthConfig = [
        { color: '#ef4444', label: 'Weak' },
        { color: '#f97316', label: 'Fair' },
        { color: '#eab308', label: 'Good' },
        { color: '#22c55e', label: 'Strong' },
        { color: '#10b981', label: 'Very Strong' },
    ];

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const val = this.value;
            Object.values(reqs).forEach(({ el, test }) => {
                if (!el) return;
                const pass = test(val);
                el.style.color = pass ? '#22c55e' : 'var(--text-secondary)';
                const icon = el.querySelector('i');
                if (icon) icon.className = pass ? 'fas fa-check-circle' : 'fas fa-circle-dot';
            });
            const score = Object.values(reqs).filter(({ test }) => test(val)).length;
            bars.forEach((bar, i) => {
                if (bar) bar.style.background = i < score ? strengthConfig[score - 1].color : 'var(--border-color)';
            });
            if (strengthLabel) {
                if (val.length === 0) { strengthLabel.textContent = ''; }
                else { strengthLabel.textContent = strengthConfig[score - 1]?.label || 'Weak'; }
            }
            checkPasswordMatch();
        });
    }

    function checkPasswordMatch() {
        if (!matchEl || !confirmInput || !confirmInput.value) { if (matchEl) matchEl.innerHTML = ''; return; }
        if (passwordInput && passwordInput.value === confirmInput.value) {
            matchEl.innerHTML = '<i class="fas fa-check-circle mr-1" style="color: #22c55e;"></i> Passwords match';
            matchEl.style.color = '#22c55e';
        } else {
            matchEl.innerHTML = '<i class="fas fa-times-circle mr-1" style="color: #ef4444;"></i> Passwords do not match';
            matchEl.style.color = '#ef4444';
        }
    }
    confirmInput?.addEventListener('input', checkPasswordMatch);

    // Bio Counter
    const bioField = document.getElementById('bio');
    const bioCount = document.getElementById('bio-count');
    if (bioField && bioCount) {
        bioField.addEventListener('input', () => { bioCount.textContent = `${bioField.value.length} / 500 characters`; });
    }

    // Expertise handlers
    document.getElementById('addExpertiseBtn')?.addEventListener('click', () => {
        const dropdown = document.getElementById('expertiseDropdown');
        if (!dropdown) return;
        const val = dropdown.value.trim();
        if (!val) { showToast('Please select an area of expertise first', true); return; }
        if (selectedExpertise.has(val)) { showToast('Already added!', true); return; }
        selectedExpertise.add(val);
        renderExpertiseTags();
        dropdown.value = '';
    });

    document.getElementById('showCustomExpertiseBtn')?.addEventListener('click', () => {
        document.getElementById('customExpertiseWrap')?.classList.remove('hidden');
        document.getElementById('customExpertiseInput')?.focus();
    });

    function addCustomExpertise() {
        const input = document.getElementById('customExpertiseInput');
        if (!input) return;
        const val = input.value.trim();
        if (!val) { showToast('Please type an area of expertise', true); return; }
        if (selectedExpertise.has(val)) { showToast('Already added!', true); return; }
        selectedExpertise.add(val);
        renderExpertiseTags();
        input.value = '';
        document.getElementById('customExpertiseWrap')?.classList.add('hidden');
    }

    document.getElementById('addCustomExpertiseBtn')?.addEventListener('click', addCustomExpertise);
    document.getElementById('customExpertiseInput')?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addCustomExpertise(); } });
    document.getElementById('cancelCustomExpertiseBtn')?.addEventListener('click', () => {
        document.getElementById('customExpertiseWrap')?.classList.add('hidden');
        const input = document.getElementById('customExpertiseInput');
        if (input) input.value = '';
    });

    renderExpertiseTags();

    // Dynamic Profile Rows
    const platformIcons = {
        linkedin: '<i class="fab fa-linkedin" style="color: #0077b5;"></i>',
        twitter: '<i class="fab fa-twitter" style="color: #1DA1F2;"></i>',
        facebook: '<i class="fab fa-facebook" style="color: #1877F2;"></i>',
        instagram: '<i class="fab fa-instagram" style="color: #E4405F;"></i>',
        youtube: '<i class="fab fa-youtube" style="color: #FF0000;"></i>',
        tiktok: '<i class="fab fa-tiktok"></i>',
        github: '<i class="fab fa-github"></i>',
        website: '<i class="fas fa-globe" style="color: var(--purple);"></i>',
        other: '<i class="fas fa-link"></i>',
    };

    const platformPlaceholders = {
        linkedin: 'https://linkedin.com/in/...',
        twitter: 'https://x.com/...',
        facebook: 'https://facebook.com/...',
        instagram: 'https://instagram.com/...',
        youtube: 'https://youtube.com/@...',
        tiktok: 'https://tiktok.com/@...',
        github: 'https://github.com/...',
        website: 'https://yourwebsite.com',
        other: 'https://...',
    };

    function buildProfileRow(platform = 'website') {
        const row = document.createElement('div');
        row.className = 'profile-row';
        row.innerHTML = `
            <div class="icon-wrap">${platformIcons[platform] || platformIcons.other}</div>
            <select name="profile_platform[]" class="w-28 text-xs border-0 bg-transparent focus:ring-0" style="color: var(--text-primary);">
                ${Object.keys(platformIcons).map(k =>
                    `<option value="${k}" ${k === platform ? 'selected' : ''}>${k.charAt(0).toUpperCase() + k.slice(1)}</option>`
                ).join('')}
            </select>
            <input type="url" name="profile_url[]" placeholder="${platformPlaceholders[platform] || 'https://...'}" class="flex-1" style="background: transparent; color: var(--text-primary);">
            <button type="button" class="remove-profile-btn" title="Remove"><i class="fas fa-times"></i></button>
        `;

        const select = row.querySelector('select');
        const iconWrap = row.querySelector('.icon-wrap');
        const urlInput = row.querySelector('input[type="url"]');
        select.addEventListener('change', () => {
            iconWrap.innerHTML = platformIcons[select.value] || platformIcons.other;
            urlInput.placeholder = platformPlaceholders[select.value] || 'https://...';
        });
        row.querySelector('.remove-profile-btn').addEventListener('click', () => row.remove());
        return row;
    }

    document.getElementById('addProfileBtn')?.addEventListener('click', () => {
        document.getElementById('profilesList').appendChild(buildProfileRow('website'));
    });

    // Success Modal
    function showSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) modal.classList.add('active');
    }

    // Form Submission
    const mentorForm = document.getElementById('mentorForm');
    if (mentorForm) {
        mentorForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('name')?.value || '';
            const email = document.getElementById('email')?.value || '';
            const expertiseHidden = document.querySelectorAll('#expertiseHiddenInputs input');
            const bio = document.getElementById('bio')?.value || '';

            if (!name || !email || expertiseHidden.length === 0 || !bio) {
                showToast('Please fill in all required fields correctly', true);
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...'; }

            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showSuccessModal();
                } else {
                    showToast(data.message || 'Failed to update mentor', true);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', true);
            } finally {
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Update Mentor'; }
            }
        });
    }

    document.getElementById('successModal')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });

    updateNavButtons();
</script>
@endpush