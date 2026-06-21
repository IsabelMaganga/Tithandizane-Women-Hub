@extends('admin.layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'Settings')
@section('page-subtitle', 'Manage platform configuration, users, and security preferences.')

@section('content')
<div class="px-4 py-0 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <!-- Settings Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

        <!-- General Configuration -->
        <a href="{{ route('admin.settings.general') }}" class="block p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md settings-card" style="background: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg" style="background: var(--light-blue);">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium" style="color: var(--text-primary);">General</h3>
            </div>
            <p class="mb-4 text-sm" style="color: var(--text-secondary);">Configure platform name, logo, and feature toggles.</p>
            <span class="text-sm font-medium text-blue-600 hover:text-blue-800">Configure →</span>
        </a>

        <!-- Admin Users -->
        <a href="{{ route('admin.settings.admins') }}" class="block p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md settings-card" style="background: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg" style="background: var(--light-purple);">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium" style="color: var(--text-primary);">Admin Users</h3>
            </div>
            <p class="mb-4 text-sm" style="color: var(--text-secondary);">Manage administrator accounts and permissions.</p>
            <span class="text-sm font-medium text-blue-600 hover:text-blue-800">Configure →</span>
        </a>

        <!-- Email Templates -->
        <a href="{{ route('admin.settings.email') }}" class="block p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md settings-card" style="background: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg" style="background: var(--light-teal);">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium" style="color: var(--text-primary);">Email Templates</h3>
            </div>
            <p class="mb-4 text-sm" style="color: var(--text-secondary);">Customize system email notifications and templates.</p>
            <span class="text-sm font-medium text-blue-600 hover:text-blue-800">Configure →</span>
        </a>

        <!-- Security -->
        <a href="{{ route('admin.settings.security') }}" class="block p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md settings-card" style="background: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg" style="background: var(--light-red);">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium" style="color: var(--text-primary);">Security</h3>
            </div>
            <p class="mb-4 text-sm" style="color: var(--text-secondary);">Configure 2FA, session timeout, and security policies.</p>
            <span class="text-sm font-medium text-blue-600 hover:text-blue-800">Configure →</span>
        </a>

        <!-- Backup -->
        <a href="{{ route('admin.settings.backup') }}" class="block p-6 transition-shadow bg-white border border-gray-200 rounded-lg hover:shadow-md settings-card" style="background: var(--card-bg); border-color: var(--border-color);">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg" style="background: var(--light-orange);">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4zm0 5c0 2.21 3.582 4 8 4s8-1.79 8-4" />
                    </svg>
                </div>
                <h3 class="ml-3 text-lg font-medium" style="color: var(--text-primary);">Backup</h3>
            </div>
            <p class="mb-4 text-sm" style="color: var(--text-secondary);">Manage database backups and restore operations.</p>
            <span class="text-sm font-medium text-blue-600 hover:text-blue-800">Configure →</span>
        </a>

    </div>
</div>
@endsection

@push('styles')
<style>
    .settings-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .settings-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15);
    }
    .settings-card:hover .text-blue-600 {
        color: #1d4ed8 !important;
    }
</style>
@endpush