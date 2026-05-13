@extends('admin.layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')
@section('page-subtitle', 'Configure platform settings, manage users, and customize your experience')

@section('content')
<!-- Settings Tabs -->
<div class="mb-6 border-b flex gap-2" style="border-color: var(--border-color);">
    <button class="settings-tab active px-6 py-3 rounded-t-lg font-medium transition" data-tab="general" style="background: var(--blue); color: white;">
        <i class="fas fa-sliders-h mr-2"></i> General
    </button>
    <button class="settings-tab px-6 py-3 rounded-t-lg font-medium transition" data-tab="admins" style="background: var(--light-gray); color: var(--text-primary);">
        <i class="fas fa-user-shield mr-2"></i> Admin Users
    </button>
    <button class="settings-tab px-6 py-3 rounded-t-lg font-medium transition" data-tab="email" style="background: var(--light-gray); color: var(--text-primary);">
        <i class="fas fa-envelope mr-2"></i> Email Templates
    </button>
    <button class="settings-tab px-6 py-3 rounded-t-lg font-medium transition" data-tab="security" style="background: var(--light-gray); color: var(--text-primary);">
        <i class="fas fa-lock mr-2"></i> Security
    </button>
    <button class="settings-tab px-6 py-3 rounded-t-lg font-medium transition" data-tab="backup" style="background: var(--light-gray); color: var(--text-primary);">
        <i class="fas fa-database mr-2"></i> Backup
    </button>
</div>

<!-- Save Button -->
<div class="flex justify-end mb-6">
    <button id="saveAllSettings" class="px-5 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2" style="background: var(--teal-green); color: white;">
        <i class="fas fa-save"></i> Save All Changes
    </button>
</div>

<!-- TAB 1: GENERAL -->
<div id="tab-general" class="settings-content active">
    <div class="rounded-2xl shadow-md p-6" style="background: var(--card-bg);">
        <h2 class="text-xl font-bold mb-4" style="color: var(--text-primary);">General Configuration</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Platform Name</label>
                <input type="text" value="Tithandizane Women Hub" class="w-full px-4 py-2 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Platform Email</label>
                <input type="email" value="info@tithandizane.org" class="w-full px-4 py-2 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Platform Logo</label>
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo2.png') }}" class="w-20 h-20 rounded-lg object-cover border">
                <button class="px-4 py-2 rounded-lg text-sm" style="background: var(--light-blue); color: var(--blue);">
                    <i class="fas fa-upload mr-2"></i> Upload New Logo
                </button>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-3" style="color: var(--text-primary);">Feature Toggles</label>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--light-gray);">
                    <span class="font-medium" style="color: var(--text-primary);">Enable Mentor Registration</span>
                    <label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--light-gray);">
                    <span class="font-medium" style="color: var(--text-primary);">Enable Harassment Reports</span>
                    <label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--light-gray);">
                    <span class="font-medium" style="color: var(--text-primary);">Maintenance Mode</span>
                    <label class="toggle-switch"><input type="checkbox"><span class="toggle-slider"></span></label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TAB 2: ADMIN USERS -->
<div id="tab-admins" class="settings-content hidden">
    <div class="rounded-2xl shadow-md" style="background: var(--card-bg);">
        <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color);">
            <h2 class="text-xl font-bold" style="color: var(--text-primary);">Admin Users</h2>
            <button onclick="alert('Add admin')" class="px-4 py-2 rounded-lg text-sm" style="background: var(--teal-green); color: white;">
                <i class="fas fa-plus"></i> Add New Admin
            </button>
        </div>
        <div class="p-6">
            <p style="color: var(--text-secondary);">Admin management table will appear here</p>
        </div>
    </div>
</div>

<!-- TAB 3: EMAIL TEMPLATES -->
<div id="tab-email" class="settings-content hidden">
    <div class="rounded-2xl shadow-md" style="background: var(--card-bg);">
        <div class="p-6 border-b" style="border-color: var(--border-color);">
            <h2 class="text-xl font-bold" style="color: var(--text-primary);">Email Templates</h2>
        </div>
        <div class="divide-y">
            <div class="p-4 flex justify-between items-center">
                <div><h3 class="font-semibold">Welcome Email (Mentor)</h3><p class="text-xs text-secondary">Sent when a new mentor registers</p></div>
                <button class="px-3 py-1.5 rounded-lg text-sm" style="background: var(--light-blue); color: var(--blue);">Edit</button>
            </div>
            <div class="p-4 flex justify-between items-center">
                <div><h3 class="font-semibold">Welcome Email (User)</h3><p class="text-xs text-secondary">Sent to new platform users</p></div>
                <button class="px-3 py-1.5 rounded-lg text-sm" style="background: var(--light-blue); color: var(--blue);">Edit</button>
            </div>
        </div>
    </div>
</div>

<!-- TAB 4: SECURITY -->
<div id="tab-security" class="settings-content hidden">
    <div class="rounded-2xl shadow-md p-6" style="background: var(--card-bg);">
        <h2 class="text-xl font-bold mb-4" style="color: var(--text-primary);">Security Settings</h2>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--light-gray);">
                <div><span class="font-semibold">Two-Factor Authentication</span><p class="text-xs">Require 2FA for admin accounts</p></div>
                <label class="toggle-switch"><input type="checkbox"><span class="toggle-slider"></span></label>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg" style="background: var(--light-gray);">
                <div><span class="font-semibold">Session Timeout</span><p class="text-xs">Auto-logout after inactivity</p></div>
                <select class="px-3 py-1 border rounded-lg" style="background: var(--card-bg);"><option>30 minutes</option><option>1 hour</option></select>
            </div>
        </div>
    </div>
</div>

<!-- TAB 5: BACKUP -->
<div id="tab-backup" class="settings-content hidden">
    <div class="rounded-2xl shadow-md p-6" style="background: var(--card-bg);">
        <h2 class="text-xl font-bold mb-4" style="color: var(--text-primary);">Database Backup</h2>
        
        <div class="p-4 rounded-lg mb-6" style="background: var(--light-teal);">
            <p class="font-semibold"><i class="fas fa-check-circle text-green-600 mr-2"></i> Last Backup: Today at 02:00 AM</p>
            <p class="text-xs mt-1" style="color: var(--text-secondary);">Backup size: 45.2 MB</p>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <button class="px-4 py-3 rounded-lg text-sm font-semibold" style="background: var(--teal-green); color: white;">Run Manual Backup</button>
            <button class="px-4 py-3 rounded-lg text-sm font-semibold" style="background: var(--blue); color: white;">Download Backup</button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .settings-tab { transition: all 0.3s ease; cursor: pointer; }
    .settings-tab:hover:not(.active) { background: var(--light-gray); transform: translateY(-2px); }
    .settings-content.hidden { display: none; }
    .settings-content.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .text-secondary { color: var(--text-secondary); }
</style>
@endpush

@push('scripts')
<script>
    // Tab switching
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.settings-tab').forEach(t => {
                t.style.background = 'var(--light-gray)';
                t.style.color = 'var(--text-primary)';
            });
            this.style.background = 'var(--blue)';
            this.style.color = 'white';
            
            document.querySelectorAll('.settings-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            document.getElementById(`tab-${this.dataset.tab}`).classList.remove('hidden');
            document.getElementById(`tab-${this.dataset.tab}`).classList.add('active');
        });
    });
    
    // Save button
    document.getElementById('saveAllSettings')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-check mr-2"></i> Saved!';
            setTimeout(() => this.innerHTML = '<i class="fas fa-save mr-2"></i> Save All Changes', 2000);
        }, 1000);
    });
</script>
@endpush