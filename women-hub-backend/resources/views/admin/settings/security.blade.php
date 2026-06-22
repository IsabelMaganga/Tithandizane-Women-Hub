@extends('admin.layouts.admin')

@section('title', 'Security Settings')
@section('page-title', 'Security Settings')
@section('page-subtitle', 'Configure 2FA, session timeout, and security policies.')

@section('content')
<div class="px-4 py-0 mx-auto max-w-4xl sm:px-6 lg:px-8">
    <div class="mb-6">
    <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition" style="background: #7c3aed; color: white; hover:background: #6d28d9;">
        <i class="fas fa-arrow-left"></i> Back to Settings
    </a>
</div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" style="background: var(--card-bg); border-color: var(--border-color);">
        <form action="{{ route('admin.settings.security.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 rounded-lg" style="background: var(--light-gray);">
                    <div>
                        <span class="font-semibold" style="color: var(--text-primary);">Two-Factor Authentication</span>
                        <p class="text-xs" style="color: var(--text-secondary);">Require 2FA for admin accounts</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="two_factor_auth" value="1" {{ isset($settings['two_factor_auth']) && $settings['two_factor_auth'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                
                <div class="flex items-center justify-between p-4 rounded-lg" style="background: var(--light-gray);">
                    <div>
                        <span class="font-semibold" style="color: var(--text-primary);">Session Timeout</span>
                        <p class="text-xs" style="color: var(--text-secondary);">Auto-logout after inactivity</p>
                    </div>
                    <select name="session_timeout" class="px-3 py-1.5 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="30" {{ (isset($settings['session_timeout']) && $settings['session_timeout'] == 30) ? 'selected' : '' }}>30 minutes</option>
                        <option value="60" {{ (isset($settings['session_timeout']) && $settings['session_timeout'] == 60) ? 'selected' : '' }}>1 hour</option>
                        <option value="120" {{ (isset($settings['session_timeout']) && $settings['session_timeout'] == 120) ? 'selected' : '' }}>2 hours</option>
                    </select>
                </div>
                
                <div class="flex items-center justify-between p-4 rounded-lg" style="background: var(--light-gray);">
                    <div>
                        <span class="font-semibold" style="color: var(--text-primary);">Max Login Attempts</span>
                        <p class="text-xs" style="color: var(--text-secondary);">Lock account after failed attempts</p>
                    </div>
                    <select name="max_login_attempts" class="px-3 py-1.5 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="3" {{ (isset($settings['max_login_attempts']) && $settings['max_login_attempts'] == 3) ? 'selected' : '' }}>3 attempts</option>
                        <option value="5" {{ (isset($settings['max_login_attempts']) && $settings['max_login_attempts'] == 5) ? 'selected' : '' }}>5 attempts</option>
                        <option value="10" {{ (isset($settings['max_login_attempts']) && $settings['max_login_attempts'] == 10) ? 'selected' : '' }}>10 attempts</option>
                    </select>
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