@extends('admin.layouts.admin')

@section('title', 'View Mentor - ' . $mentor->name)
@section('page-title', 'Mentor Details')
@section('page-subtitle', 'View complete mentor profile')

@section('content')
<div class="rounded-2xl shadow-lg overflow-hidden" style="background: var(--card-bg);">
    <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color); background: var(--light-gray);">
        <div>
            <h3 class="text-xl font-bold" style="color: var(--text-primary);">
                <i class="fas fa-user-circle mr-2" style="color: var(--purple);"></i>{{ $mentor->name }}
            </h3>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Mentor Profile Details</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.mentors.edit', $mentor->id) }}" class="px-4 py-2 rounded-lg text-white transition" style="background: var(--orange);">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.mentors.index') }}" class="px-4 py-2 rounded-lg border transition" style="border-color: var(--border-color); color: var(--text-primary);">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Mentor details here - similar to the show view I provided earlier -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Profile -->
            <div class="lg:col-span-1">
                <div class="text-center p-6 rounded-lg" style="background: var(--light-gray);">
                    @php
                        $photoUrl = $mentor->photo ? Storage::url($mentor->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($mentor->name) . '&background=9b59b6&color=fff&size=150';
                    @endphp
                    <img src="{{ $photoUrl }}" class="w-40 h-40 rounded-full mx-auto object-cover border-4" style="border-color: var(--purple);">
                    <h2 class="text-2xl font-bold mt-4" style="color: var(--text-primary);">{{ $mentor->name }}</h2>
                    
                    <div class="mt-4">
                        @if($mentor->status === 'active')
                            <span class="px-4 py-2 rounded-full text-sm font-medium" style="background: #10b98120; color: #10b981;">Active</span>
                        @elseif($mentor->status === 'pending')
                            <span class="px-4 py-2 rounded-full text-sm font-medium" style="background: #f59e0b20; color: #f59e0b;">Pending</span>
                        @else
                            <span class="px-4 py-2 rounded-full text-sm font-medium" style="background: #ef444420; color: #ef4444;">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Details -->
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    <!-- Contact Info -->
                    <div class="p-4 rounded-lg" style="background: var(--light-gray);">
                        <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Contact Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs uppercase" style="color: var(--text-secondary);">Email</label>
                                <p class="mt-1" style="color: var(--text-primary);">{{ $mentor->email }}</p>
                            </div>
                            <div>
                                <label class="text-xs uppercase" style="color: var(--text-secondary);">Phone</label>
                                <p class="mt-1" style="color: var(--text-primary);">{{ $mentor->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="text-xs uppercase" style="color: var(--text-secondary);">Location</label>
                                <p class="mt-1" style="color: var(--text-primary);">{{ $mentor->location ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bio -->
                    <div class="p-4 rounded-lg" style="background: var(--light-gray);">
                        <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Biography</h4>
                        <p style="color: var(--text-primary);">{{ $mentor->bio ?? 'No bio provided' }}</p>
                    </div>
                    
                    <!-- Expertise -->
                    <div class="p-4 rounded-lg" style="background: var(--light-gray);">
                        <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Areas of Expertise</h4>
                        <div class="flex flex-wrap gap-2">
                            @if($mentor->expertise && is_array($mentor->expertise))
                                @foreach($mentor->expertise as $skill)
                                    <span class="px-3 py-1 rounded-full text-sm" style="background: var(--purple); color: white;">{{ $skill }}</span>
                                @endforeach
                            @else
                                <p style="color: var(--text-secondary);">No expertise listed</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Availability -->
                    <div class="p-4 rounded-lg" style="background: var(--light-gray);">
                        <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Availability</h4>
                        <p style="color: var(--text-primary);">{{ $mentor->availability ?? 'Not specified' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection