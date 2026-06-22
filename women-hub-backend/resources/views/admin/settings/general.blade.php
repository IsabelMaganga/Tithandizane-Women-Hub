@extends('admin.layouts.admin')

@section('title', 'General Settings')
@section('page-title', 'General Settings')
@section('page-subtitle', 'Configure platform name, logo, and feature toggles.')

@section('content')
<div class="px-4 py-0 mx-auto max-w-4xl sm:px-6 lg:px-8">
    <!-- Back Button -->
    <div class="mb-6">
    <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition" style="background: #7c3aed; color: white; hover:background: #6d28d9;">
        <i class="fas fa-arrow-left"></i> Back to Settings
    </a>
</div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" style="background: var(--card-bg); border-color: var(--border-color);">
        <form action="{{ route('admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Platform Name</label>
                    <input type="text" name="platform_name" value="{{ old('platform_name', $settings['platform_name'] ?? 'Tithandizane Women Hub') }}" class="w-full px-4 py-2 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('platform_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Platform Email</label>
                    <input type="email" name="platform_email" value="{{ old('platform_email', $settings['platform_email'] ?? 'info@tithandizane.org') }}" class="w-full px-4 py-2 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                    @error('platform_email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Platform Logo</label>
                <div class="flex items-center gap-4">
                    @if(isset($settings['logo_path']) && $settings['logo_path'])
                        <img src="{{ asset($settings['logo_path']) }}" class="w-20 h-20 rounded-lg object-cover border" style="border-color: var(--border-color);">
                    @else
                        <img src="{{ asset('images/logo2.png') }}" class="w-20 h-20 rounded-lg object-cover border" style="border-color: var(--border-color);">
                    @endif
                    <input type="file" name="logo" accept="image/*" class="px-4 py-2 rounded-lg text-sm cursor-pointer" style="background: var(--light-blue); color: var(--blue);">
                    @error('logo')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-3" style="color: var(--text-primary);">Feature Toggles</label>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--light-gray);">
                        <span class="font-medium" style="color: var(--text-primary);">Enable Mentor Registration</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="mentor_registration" value="1" {{ isset($settings['mentor_registration']) && $settings['mentor_registration'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--light-gray);">
                        <span class="font-medium" style="color: var(--text-primary);">Enable Harassment Reports</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="harassment_reports" value="1" {{ isset($settings['harassment_reports']) && $settings['harassment_reports'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--light-gray);">
                        <span class="font-medium" style="color: var(--text-primary);">Maintenance Mode</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" {{ isset($settings['maintenance_mode']) && $settings['maintenance_mode'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-8 pt-4 border-t" style="border-color: var(--border-color);">
                <a href="{{ route('admin.settings.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold" style="background: var(--light-gray); color: var(--text-primary);">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2" style="background: var(--teal-green); color: white;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection