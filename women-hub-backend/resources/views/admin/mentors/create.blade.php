{{-- resources/views/admin/mentors/create.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Add New Mentor')
@section('page-title', 'Add New Mentor')
@section('page-subtitle', 'Create and configure a professional mentor profile')

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
        <li class="font-medium" style="color: var(--text-primary);">Add New Mentor</li>
    </ol>
</nav>

<!-- Form Card -->
<div class="rounded-2xl shadow-lg overflow-hidden" style="background: var(--card-bg);">
    <form id="mentorForm" action="{{ route('admin.mentors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Header -->
        <div class="px-6 md:px-8 py-6 border-b" style="background: linear-gradient(135deg, var(--light-purple) 0%, var(--card-bg) 100%); border-color: var(--border-color);">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold" style="color: var(--purple);">Mentor Registration</h2>
                    <p class="mt-2 text-sm" style="color: var(--text-secondary);">
                        Complete the form below to add a new mentor to the platform.
                        Fields marked <span style="color: var(--red); font-weight: 600;">*</span> are required.
                        A secure password will be generated automatically.
                    </p>
                </div>
                <div class="hidden sm:block">
                    <div class="inline-flex items-center space-x-2 backdrop-blur px-4 py-2 rounded-lg border shadow-sm" style="background: var(--card-bg); border-color: var(--purple);">
                        <i class="fas fa-info-circle text-sm" style="color: var(--purple);"></i>
                        <span class="text-sm font-medium" style="color: var(--text-secondary);">
                            Step <span id="currentStep">1</span> of <span id="totalSteps">3</span>
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
                                    <div class="text-center">
                                        <i class="fas fa-user text-5xl mb-2" style="color: var(--text-secondary);"></i>
                                        <p class="text-xs" style="color: var(--text-secondary);">Click to upload</p>
                                    </div>
                                </div>
                                <label class="absolute inset-0 rounded-xl cursor-pointer">
                                    <input type="file" name="photo" id="photo" accept="image/*" class="w-full opacity-0 cursor-pointer">
                                </label>
                            </div>
                            <p class="mt-3 text-xs text-center" style="color: var(--text-secondary);">
                                <i class="fas fa-check-circle mr-1" style="color: var(--teal-green);"></i> PNG, JPG
                                <br><i class="fas fa-info-circle mr-1" style="color: var(--purple);"></i> Max 2MB
                            </p>
                        </div>
                    </div>

                    <!-- Basic Fields -->
                    <div class="lg:col-span-3 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Full Name <span style="color: var(--red);">*</span></label>
                                <input type="text" name="name" id="name" placeholder="e.g., Jane Smith"
                                       class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2"
                                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Email Address <span style="color: var(--red);">*</span></label>
                                <input type="email" name="email" id="email" placeholder="jane.smith@example.com"
                                       class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2"
                                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Phone Number</label>
                                <input type="tel" name="phone" id="phone" placeholder="+265 99 123 4567"
                                       class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2"
                                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            </div>
                            <div>
                                <label for="location" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Location</label>
                                <input type="text" name="location" id="location" placeholder="e.g., Lilongwe, Malawi"
                                       class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2"
                                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            </div>
                        </div>

                        <!-- Auto-password notice -->
                        <div class="flex items-start gap-3 p-4 rounded-lg border" style="background: var(--light-purple); border-color: var(--purple);">
                            <i class="fas fa-wand-magic-sparkles mt-0.5" style="color: var(--purple);"></i>
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--purple);">Password will be auto-generated</p>
                                <p class="text-xs mt-0.5" style="color: var(--text-secondary);">A secure, random password will be created for this mentor. It will be shown to you after the account is created so you can share it with them.</p>
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
                                    class="flex-1 px-4 py-3 border rounded-lg shadow-sm transition-all text-sm focus:ring-2"
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
                                    class="px-4 py-3 rounded-lg text-sm font-semibold text-white transition flex items-center gap-2 shrink-0"
                                    style="background: var(--purple);">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>

                        <!-- Custom expertise input -->
                        <div id="customExpertiseWrap" class="hidden mb-4">
                            <div class="flex gap-2">
                                <input type="text" id="customExpertiseInput" placeholder="Type a custom area of expertise..."
                                       class="flex-1 px-4 py-3 border border-dashed rounded-lg text-sm focus:ring-2"
                                       style="border-color: var(--purple); background: var(--card-bg); color: var(--text-primary);">
                                <button type="button" id="addCustomExpertiseBtn"
                                        class="px-4 py-3 rounded-lg text-sm font-semibold text-white transition"
                                        style="background: var(--teal-green);">
                                    <i class="fas fa-check"></i> Add
                                </button>
                                <button type="button" id="cancelCustomExpertiseBtn"
                                        class="px-3 py-3 rounded-lg text-sm transition border"
                                        style="color: var(--text-secondary); border-color: var(--border-color);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" id="showCustomExpertiseBtn"
                                class="mb-4 flex items-center gap-2 text-sm font-medium transition px-3 py-2 rounded-lg border border-dashed"
                                style="color: var(--purple); border-color: var(--purple); background: var(--light-purple);">
                            <i class="fas fa-plus-circle"></i> Add other (not in the list)
                        </button>

                        <div id="expertiseTags" class="flex flex-wrap gap-2 min-h-[2rem]">
                            <p class="text-xs italic" id="expertiseEmptyHint" style="color: var(--text-secondary);">No areas selected yet. Use the dropdown above to add expertise.</p>
                        </div>
                        <div id="expertiseHiddenInputs"></div>
                    </div>

                    <!-- Professional Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Professional Bio <span style="color: var(--red);">*</span></label>
                        <textarea name="bio" id="bio" rows="5"
                                  placeholder="Share your professional background, achievements, and what value you bring as a mentor..."
                                  class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2"
                                  style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                        <div class="mt-2 flex justify-between items-center">
                            <p class="text-xs" style="color: var(--text-secondary);"><i class="fas fa-pen-fancy mr-1" style="color: var(--purple);"></i> 2–4 sentences recommended</p>
                            <span id="bio-count" class="text-xs" style="color: var(--text-secondary);">0 / 500 characters</span>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Status</label>
                        <select name="status" id="status"
                                class="block w-full md:w-1/2 px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2"
                                style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="active"   {{ old('status') === 'inactive' ? '' : 'selected' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">
                            <i class="fas fa-info-circle mr-1" style="color: var(--purple);"></i>
                            Active mentors can immediately access the platform and be matched with mentees.
                        </p>
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
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition"
                                    style="background: var(--light-purple); color: var(--purple);">
                                <i class="fas fa-plus"></i> Add Profile
                            </button>
                        </div>

                        <div id="profilesList" class="space-y-3">
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
                                <input type="url" name="profile_url[]" placeholder="https://linkedin.com/in/..." class="flex-1">
                                <button type="button" class="remove-profile-btn" title="Remove"><i class="fas fa-times"></i></button>
                            </div>
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
                        </div>
                        <p class="text-xs mt-3" style="color: var(--text-secondary);"><i class="fas fa-lightbulb mr-1" style="color: var(--orange);"></i> You can add as many profiles as needed.</p>
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Additional Notes (Internal)</label>
                        <textarea name="notes" id="notes" rows="4"
                                  placeholder="Add internal notes about this mentor — special training needs, recommendations, background context..."
                                  class="block w-full px-4 py-3 border rounded-lg shadow-sm transition-all focus:ring-2"
                                  style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                    </div>

                    <!-- Notification Preferences -->
                    <div class="p-4 rounded-lg" style="background: var(--light-purple); border: 1px solid var(--border-color);">
                        <h4 class="font-semibold text-sm mb-3 flex items-center" style="color: var(--text-primary);">
                            <i class="fas fa-bell mr-2" style="color: var(--purple);"></i> Notification Preferences
                        </h4>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="notify_welcome" value="1" checked class="w-4 h-4 rounded focus:ring-2" style="accent-color: var(--purple);">
                                <span class="ml-2 text-sm" style="color: var(--text-primary);">Send welcome email to mentor</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="notify_training" value="1" checked class="w-4 h-4 rounded focus:ring-2" style="accent-color: var(--purple);">
                                <span class="ml-2 text-sm" style="color: var(--text-primary);">Notify about upcoming training sessions</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="notify_schedule_approved" value="1" checked class="w-4 h-4 rounded focus:ring-2" style="accent-color: var(--purple);">
                                <span class="ml-2 text-sm" style="color: var(--text-primary);">Notify mentor when their schedule is approved or declined</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="notify_new_mentee" value="1" checked class="w-4 h-4 rounded focus:ring-2" style="accent-color: var(--purple);">
                                <span class="ml-2 text-sm" style="color: var(--text-primary);">Notify mentor when a new mentee is matched with them</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions — Desktop -->
        <div class="px-6 md:px-8 py-6 border-t flex flex-col sm:flex-row sm:justify-between gap-4" style="border-color: var(--border-color); background: var(--light-gray);">
            <div class="flex gap-3">
                <a href="{{ route('admin.mentors.index') }}"
                   class="flex items-center justify-center px-6 py-2.5 border rounded-lg font-medium transition-colors"
                   style="border-color: var(--border-color); color: var(--text-primary); background: var(--card-bg);">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
            <div class="flex gap-3">
                <button type="button" id="prevBtn"
                        class="hidden sm:flex items-center justify-center px-6 py-2.5 border rounded-lg font-medium transition-colors"
                        style="border-color: var(--border-color); color: var(--text-primary); background: var(--card-bg);">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                <button type="button" id="nextBtn"
                        class="hidden sm:flex items-center justify-center px-6 py-2.5 text-white font-medium rounded-lg transition-colors"
                        style="background: var(--purple);">
                    Next <i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="submit" id="submitBtn"
                        class="hidden sm:flex items-center justify-center px-8 py-2.5 text-white font-medium rounded-lg transition-all shadow-md"
                        style="background: linear-gradient(135deg, var(--purple), var(--purple));">
                    <i class="fas fa-check mr-2"></i> Create Mentor
                </button>
            </div>
        </div>

        <!-- Form Actions — Mobile -->
        <div class="sm:hidden px-6 py-4 border-t space-y-3" style="border-color: var(--border-color); background: var(--light-gray);">
            <button type="button" id="mobileNextBtn"
                    class="w-full flex items-center justify-center px-6 py-3 text-white font-medium rounded-lg transition-colors"
                    style="background: var(--purple);">
                Continue <i class="fas fa-arrow-right ml-2"></i>
            </button>
            <button type="submit" id="mobileSubmitBtn"
                    class="hidden w-full items-center justify-center px-6 py-3 text-white font-medium rounded-lg transition-all shadow-md"
                    style="background: linear-gradient(135deg, var(--purple), var(--purple));">
                <i class="fas fa-check mr-2"></i> Create Mentor
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
        <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Mentor Created Successfully!</h2>
        <p class="mb-5" style="color: var(--text-secondary);">The mentor profile has been created. Share the login credentials below with the mentor.</p>

        <!-- Credentials box -->
        <div class="credentials-box" style="background: var(--light-purple); border: 1.5px solid var(--purple); border-radius: 0.75rem; padding: 1rem 1.25rem; text-align: left; margin-bottom: 1.25rem;">
            <p class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--purple);">
                <i class="fas fa-key mr-1.5"></i> Login Credentials
            </p>
            <div class="space-y-2">
                <div class="credential-row">
                    <span class="credential-label">Email</span>
                    <span id="modalEmail" class="credential-value"></span>
                    <button type="button" class="copy-btn" data-copy="email" title="Copy email">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Password</span>
                    <span id="modalPassword" class="credential-value font-mono"></span>
                    <button type="button" class="copy-btn" data-copy="password" title="Copy password">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <p class="text-xs mt-3" style="color: var(--text-secondary);">
                <i class="fas fa-triangle-exclamation mr-1" style="color: var(--orange);"></i>
                Copy these credentials now — the password won't be shown again.
            </p>
        </div>

        <!-- Copy all button -->
        <button type="button" id="copyAllBtn" class="copy-all-btn" style="background: var(--light-gray); color: var(--text-primary); border: 1px solid var(--border-color);">
            <i class="fas fa-clipboard mr-2"></i> Copy All Credentials
        </button>

        <div class="success-buttons">
            <button onclick="addAnotherMentor()" class="btn-secondary" style="background: var(--light-gray); color: var(--text-primary);">
                <i class="fas fa-plus-circle"></i> Add Another
            </button>
            <button onclick="viewMentors()" class="btn-primary" style="background: linear-gradient(135deg, var(--purple), var(--purple)); color: white;">
                <i class="fas fa-eye"></i> View Mentors
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-section { opacity: 0; transform: translateY(10px); animation: fadeInUp 0.4s ease-out forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .tab-button { transition: all 0.3s ease; }
    .tab-button.active { border-bottom-width: 3px; }

    .expertise-tag {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.4rem 0.85rem; border-radius: 9999px;
        font-size: 0.8rem; font-weight: 600;
        border: 1.5px solid var(--purple);
        background: var(--light-purple); color: var(--purple);
    }
    .expertise-tag button { background: none; border: none; cursor: pointer; font-size: 0.7rem; padding: 0; opacity: 0.7; color: var(--purple); }
    .expertise-tag button:hover { opacity: 1; }

    .profile-row {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.75rem 1rem; border-radius: 0.5rem;
        border: 1.5px solid var(--border-color); transition: all 0.2s;
        background: var(--light-gray);
    }
    .profile-row:hover { border-color: var(--purple); background: var(--light-purple); }
    .profile-row .icon-wrap { flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; background: var(--card-bg); }
    .profile-row input { flex: 1; border: 0; background: transparent; font-size: 0.875rem; color: var(--text-primary); }
    .profile-row input:focus { outline: none; box-shadow: none; }
    .remove-profile-btn { cursor: pointer; font-size: 0.85rem; background: none; border: none; padding: 0 0.25rem; color: var(--text-secondary); transition: color 0.2s; }
    .remove-profile-btn:hover { color: var(--red); }

    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1000; display: none; align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .success-modal { border-radius: 1.5rem; max-width: 520px; width: 90%; padding: 2rem; text-align: center; animation: modalSlideIn 0.3s ease-out; background: var(--card-bg); max-height: 90vh; overflow-y: auto; }
    @keyframes modalSlideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .checkmark-circle { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
    .checkmark { font-size: 3rem; color: white; }
    .success-buttons { display: flex; gap: 1rem; justify-content: center; margin-top: 1.25rem; flex-wrap: wrap; }
    .btn-primary, .btn-secondary { padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-weight: 600; transition: all 0.3s; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; border: none; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(135,65,121,0.3); }
    .btn-secondary:hover { opacity: 0.8; }

    /* Credential rows */
    .credential-row { display: flex; align-items: center; gap: 0.5rem; }
    .credential-label { font-size: 0.75rem; font-weight: 600; width: 4.5rem; flex-shrink: 0; color: var(--text-secondary); }
    .credential-value { flex: 1; font-size: 0.875rem; color: var(--text-primary); word-break: break-all; }
    .copy-btn { flex-shrink: 0; background: none; border: none; cursor: pointer; padding: 0.25rem 0.4rem; border-radius: 0.35rem; color: var(--purple); font-size: 0.8rem; transition: background 0.2s; }
    .copy-btn:hover { background: var(--card-bg); }
    .copy-all-btn { width: 100%; padding: 0.6rem 1rem; border-radius: 0.65rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; margin-bottom: 0; }
    .copy-all-btn:hover { opacity: 0.85; }

    .toast { visibility: hidden; min-width: 280px; text-align: center; border-radius: 8px; padding: 14px 20px; position: fixed; z-index: 9999; left: 50%; bottom: 30px; font-size: 14px; font-weight: 500; transform: translateX(-50%); color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .toast.show { visibility: visible; animation: fadein 0.4s, fadeout 0.5s 2.5s; }
    @keyframes fadein { from { bottom: 0; opacity: 0; } to { bottom: 30px; opacity: 1; } }
    @keyframes fadeout { from { bottom: 30px; opacity: 1; } to { bottom: 0; opacity: 0; } }
</style>
@endpush

@push('scripts')
<script>
// ─── Toast ────────────────────────────────────────────────────────────────────
function showToast(message, isError = false) {
    document.querySelectorAll('.toast').forEach(t => t.remove());
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toast.style.backgroundColor = isError ? '#dc2626' : '#22c55e';
    document.body.appendChild(toast);
    toast.offsetHeight;
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 500); }, 3000);
}

// ─── Photo Preview ────────────────────────────────────────────────────────────
document.getElementById('photo')?.addEventListener('change', function (e) {
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

// ─── Tab Navigation ───────────────────────────────────────────────────────────
let currentTab = 'basic';
const tabs = ['basic', 'professional', 'additional'];

function showTab(name) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-button').forEach(b => {
        b.classList.remove('active');
        b.style.borderColor = 'transparent';
        b.style.color = 'var(--text-secondary)';
    });
    document.getElementById(name)?.classList.add('active');
    const activeBtn = document.querySelector(`[data-tab="${name}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.style.borderColor = 'var(--purple)';
        activeBtn.style.color = 'var(--purple)';
    }
    currentTab = name;
    const stepEl = document.getElementById('currentStep');
    if (stepEl) stepEl.textContent = { basic: 1, professional: 2, additional: 3 }[name];
    updateNavButtons();
}

function updateNavButtons() {
    const idx = tabs.indexOf(currentTab);
    const isLast = idx === tabs.length - 1;

    document.getElementById('prevBtn')?.classList.toggle('hidden', idx === 0);
    document.getElementById('nextBtn')?.classList.toggle('hidden', isLast);
    document.getElementById('submitBtn')?.classList.toggle('hidden', !isLast);

    const mobileNext   = document.getElementById('mobileNextBtn');
    const mobileSubmit = document.getElementById('mobileSubmitBtn');
    if (mobileNext)   mobileNext.classList.toggle('hidden', isLast);
    if (mobileSubmit) mobileSubmit.classList.toggle('hidden', !isLast);
}

document.querySelectorAll('.tab-button').forEach(btn => btn.addEventListener('click', () => showTab(btn.dataset.tab)));
document.getElementById('nextBtn')?.addEventListener('click',       () => { const i = tabs.indexOf(currentTab); if (i < tabs.length - 1) { showTab(tabs[i + 1]); scrollTo({ top: 0, behavior: 'smooth' }); } });
document.getElementById('prevBtn')?.addEventListener('click',       () => { const i = tabs.indexOf(currentTab); if (i > 0)               { showTab(tabs[i - 1]); scrollTo({ top: 0, behavior: 'smooth' }); } });
document.getElementById('mobileNextBtn')?.addEventListener('click', () => { const i = tabs.indexOf(currentTab); if (i < tabs.length - 1) { showTab(tabs[i + 1]); scrollTo({ top: 0, behavior: 'smooth' }); } });

// ─── Bio Counter ──────────────────────────────────────────────────────────────
const bioField = document.getElementById('bio');
const bioCount  = document.getElementById('bio-count');
bioField?.addEventListener('input', () => { if (bioCount) bioCount.textContent = `${bioField.value.length} / 500 characters`; });

// ─── Expertise System ─────────────────────────────────────────────────────────
const selectedExpertise = new Set();

function renderExpertiseTags() {
    const tagsContainer   = document.getElementById('expertiseTags');
    const hiddenContainer = document.getElementById('expertiseHiddenInputs');
    const emptyHint       = document.getElementById('expertiseEmptyHint');
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
            tag.querySelector('button').addEventListener('click', () => { selectedExpertise.delete(val); renderExpertiseTags(); });
            tagsContainer.appendChild(tag);

            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'expertise[]';
            input.value = val;
            hiddenContainer.appendChild(input);
        });
    }
}

document.getElementById('addExpertiseBtn')?.addEventListener('click', () => {
    const dropdown = document.getElementById('expertiseDropdown');
    const val = dropdown?.value.trim();
    if (!val)                          { showToast('Please select an area of expertise first', true); return; }
    if (selectedExpertise.has(val))    { showToast('Already added!', true); return; }
    selectedExpertise.add(val);
    renderExpertiseTags();
    if (dropdown) dropdown.value = '';
});

document.getElementById('showCustomExpertiseBtn')?.addEventListener('click', () => {
    document.getElementById('customExpertiseWrap')?.classList.remove('hidden');
    document.getElementById('customExpertiseInput')?.focus();
});

function addCustomExpertise() {
    const input = document.getElementById('customExpertiseInput');
    const val   = input?.value.trim();
    if (!val)                       { showToast('Please type an area of expertise', true); return; }
    if (selectedExpertise.has(val)) { showToast('Already added!', true); return; }
    selectedExpertise.add(val);
    renderExpertiseTags();
    if (input) input.value = '';
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

// ─── Dynamic Profile Rows ─────────────────────────────────────────────────────
const platformIcons = {
    linkedin: '<i class="fab fa-linkedin" style="color:#0077b5;"></i>',
    twitter:  '<i class="fab fa-twitter"  style="color:#1DA1F2;"></i>',
    facebook: '<i class="fab fa-facebook" style="color:#1877F2;"></i>',
    instagram:'<i class="fab fa-instagram"style="color:#E4405F;"></i>',
    youtube:  '<i class="fab fa-youtube"  style="color:#FF0000;"></i>',
    tiktok:   '<i class="fab fa-tiktok"></i>',
    github:   '<i class="fab fa-github"></i>',
    website:  '<i class="fas fa-globe"    style="color:var(--purple);"></i>',
    other:    '<i class="fas fa-link"></i>',
};
const platformPlaceholders = {
    linkedin:  'https://linkedin.com/in/...',
    twitter:   'https://x.com/...',
    facebook:  'https://facebook.com/...',
    instagram: 'https://instagram.com/...',
    youtube:   'https://youtube.com/@...',
    tiktok:    'https://tiktok.com/@...',
    github:    'https://github.com/...',
    website:   'https://yourwebsite.com',
    other:     'https://...',
};

function buildProfileRow(platform = 'other') {
    const row = document.createElement('div');
    row.className = 'profile-row';
    row.innerHTML = `
        <div class="icon-wrap">${platformIcons[platform] || platformIcons.other}</div>
        <select name="profile_platform[]" class="w-28 text-xs border-0 bg-transparent focus:ring-0" style="color:var(--text-primary);">
            ${Object.keys(platformIcons).map(k => `<option value="${k}"${k === platform ? ' selected' : ''}>${k.charAt(0).toUpperCase() + k.slice(1)}</option>`).join('')}
        </select>
        <input type="url" name="profile_url[]" placeholder="${platformPlaceholders[platform] || 'https://...'}" class="flex-1" style="background:transparent;color:var(--text-primary);">
        <button type="button" class="remove-profile-btn" title="Remove"><i class="fas fa-times"></i></button>
    `;
    const select   = row.querySelector('select');
    const iconWrap = row.querySelector('.icon-wrap');
    const urlInput = row.querySelector('input[type="url"]');
    select.addEventListener('change', () => {
        iconWrap.innerHTML    = platformIcons[select.value] || platformIcons.other;
        urlInput.placeholder  = platformPlaceholders[select.value] || 'https://...';
    });
    row.querySelector('.remove-profile-btn').addEventListener('click', () => row.remove());
    return row;
}

document.querySelectorAll('#profilesList .remove-profile-btn').forEach(btn => btn.addEventListener('click', () => btn.closest('.profile-row').remove()));
document.querySelectorAll('#profilesList select').forEach(select => {
    const row      = select.closest('.profile-row');
    const iconWrap = row.querySelector('.icon-wrap');
    const urlInput = row.querySelector('input[type="url"]');
    select.addEventListener('change', () => {
        iconWrap.innerHTML   = platformIcons[select.value] || platformIcons.other;
        urlInput.placeholder = platformPlaceholders[select.value] || 'https://...';
    });
});
document.getElementById('addProfileBtn')?.addEventListener('click', () => document.getElementById('profilesList').appendChild(buildProfileRow('other')));

// ─── Credentials store ────────────────────────────────────────────────────────
let _createdEmail    = '';
let _createdPassword = '';

// ─── Success Modal ────────────────────────────────────────────────────────────
function showSuccessModal(email, password) {
    _createdEmail    = email;
    _createdPassword = password;

    const emailEl    = document.getElementById('modalEmail');
    const passwordEl = document.getElementById('modalPassword');
    if (emailEl)    emailEl.textContent    = email;
    if (passwordEl) passwordEl.textContent = password;

    document.getElementById('successModal')?.classList.add('active');
}

// Copy individual field
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const text = btn.dataset.copy === 'email' ? _createdEmail : _createdPassword;
        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('i');
            if (icon) { icon.className = 'fas fa-check'; setTimeout(() => { icon.className = 'fas fa-copy'; }, 1500); }
        });
    });
});

// Copy all
document.getElementById('copyAllBtn')?.addEventListener('click', () => {
    const text = `Email: ${_createdEmail}\nPassword: ${_createdPassword}`;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyAllBtn');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-check mr-2"></i> Copied!';
            btn.style.background = '#22c55e';
            btn.style.color = '#fff';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-clipboard mr-2"></i> Copy All Credentials';
                btn.style.background = '';
                btn.style.color = '';
            }, 2000);
        }
    });
});

window.addAnotherMentor = function () {
    document.getElementById('successModal')?.classList.remove('active');
    document.getElementById('mentorForm')?.reset();
    const photoPreview = document.getElementById('photo-preview');
    if (photoPreview) photoPreview.innerHTML = `<div class="text-center"><i class="fas fa-user text-5xl mb-2" style="color:var(--text-secondary);"></i><p class="text-xs" style="color:var(--text-secondary);">Click to upload</p></div>`;
    if (bioCount) bioCount.textContent = '0 / 500 characters';
    selectedExpertise.clear();
    renderExpertiseTags();
    showTab('basic');
    scrollTo({ top: 0, behavior: 'smooth' });
    showToast('Ready to add another mentor!');
};

window.viewMentors = function () { window.location.href = "{{ route('admin.mentors.index') }}"; };

document.getElementById('successModal')?.addEventListener('click', function (e) { if (e.target === this) this.classList.remove('active'); });

// ─── Form Submission ──────────────────────────────────────────────────────────
async function submitMentorForm(e) {
    e.preventDefault();

    const name            = document.getElementById('name')?.value.trim()  || '';
    const email           = document.getElementById('email')?.value.trim() || '';
    const expertiseInputs = document.querySelectorAll('#expertiseHiddenInputs input');
    const bio             = document.getElementById('bio')?.value.trim()   || '';

    // ── Tab 1 validations ──
    if (!name) {
        showTab('basic');
        showToast('Please enter the mentor\'s full name', true);
        document.getElementById('name')?.focus();
        return;
    }
    if (!email) {
        showTab('basic');
        showToast('Please enter an email address', true);
        document.getElementById('email')?.focus();
        return;
    }

    // ── Tab 2 validations ──
    if (expertiseInputs.length === 0) {
        showTab('professional');
        showToast('Please select at least one area of expertise', true);
        return;
    }
    if (!bio) {
        showTab('professional');
        showToast('Please enter a professional bio', true);
        document.getElementById('bio')?.focus();
        return;
    }

    // ── All good — submit ──
    const submitBtn       = document.getElementById('submitBtn');
    const mobileSubmitBtn = document.getElementById('mobileSubmitBtn');
    const disableBtn = btn => { if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...'; } };
    const enableBtn  = btn => { if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check mr-2"></i> Create Mentor'; } };

    disableBtn(submitBtn);
    disableBtn(mobileSubmitBtn);

    try {
        const formData = new FormData(document.getElementById('mentorForm'));
        const response = await fetch(document.getElementById('mentorForm').action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        });

        const data = await response.json();

        if (data.success) {
            showSuccessModal(data.mentor_email, data.plain_password);
        } else if (data.errors) {
            const firstField = Object.keys(data.errors)[0];
            const firstMsg   = data.errors[firstField][0];
            if (['name', 'email'].includes(firstField)) {
                showTab('basic');
            } else if (['bio', 'expertise'].includes(firstField)) {
                showTab('professional');
            } else {
                showTab('additional');
            }
            showToast(firstMsg, true);
        } else {
            showToast(data.message || 'Failed to create mentor. Please try again.', true);
        }
    } catch (error) {
        console.error('Submit error:', error);
        showToast('An unexpected error occurred. Please try again.', true);
    } finally {
        enableBtn(submitBtn);
        enableBtn(mobileSubmitBtn);
    }
}

document.getElementById('mentorForm')?.addEventListener('submit', submitMentorForm);

// ─── Init ─────────────────────────────────────────────────────────────────────
updateNavButtons();
</script>
@endpush