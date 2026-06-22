@extends('admin.layouts.admin')
@section('title', 'Admin Users')
@section('page-title', 'Admin Users')
@section('page-subtitle', 'Manage administrator accounts and permissions.')

@section('content')
<div class="px-4 py-0 mx-auto max-w-6xl sm:px-6 lg:px-8">
     <div class="mb-6">
    <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition" style="background: #7c3aed; color: white; hover:background: #6d28d9;">
        <i class="fas fa-arrow-left"></i> Back to Settings
    </a>
</div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200" style="background: var(--card-bg); border-color: var(--border-color);">
        <div class="p-6 border-b flex justify-between items-center" style="border-color: var(--border-color);">
            <h2 class="text-xl font-bold" style="color: var(--text-primary);">Admin Users</h2>
            <button onclick="document.getElementById('addAdminModal').style.display='flex'" class="px-4 py-2 rounded-lg text-sm" style="background: var(--blue); color: white;">
                <i class="fas fa-plus"></i> Add New Admin
            </button>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b" style="border-color: var(--border-color);">
                            <th class="pb-3 text-sm font-semibold" style="color: var(--text-primary);">Admin</th>
                            <th class="pb-3 text-sm font-semibold" style="color: var(--text-primary);">Email</th>
                            <th class="pb-3 text-sm font-semibold" style="color: var(--text-primary);">Role</th>
                            <th class="pb-3 text-sm font-semibold" style="color: var(--text-primary);">Status</th>
                            <th class="pb-3 text-sm font-semibold" style="color: var(--text-primary);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins ?? [] as $admin)
                            <tr class="border-b" style="border-color: var(--border-color);">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=3498db&color=fff&bold=true&size=32" class="w-8 h-8 rounded-full">
                                        <span class="font-medium" style="color: var(--text-primary);">{{ $admin->name }}</span>
                                    </div>
                                </td>
                                <td style="color: var(--text-primary);">{{ $admin->email }}</td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded-lg" style="background: var(--light-blue); color: var(--blue);">{{ $admin->role ?? 'Admin' }}</span>
                                </td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded-lg {{ $admin->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button class="p-1 rounded-lg hover:bg-gray-100" style="color: var(--blue);">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="p-1 rounded-lg hover:bg-gray-100" style="color: var(--red);">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center" style="color: var(--text-secondary);">No admin users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div id="addAdminModal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div class="bg-white rounded-lg p-6 max-w-md w-full" style="background: var(--card-bg);">
        <h3 class="text-xl font-bold mb-4" style="color: var(--text-primary);">Add New Admin</h3>
        <form action="{{ route('admin.settings.admins.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg" style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('addAdminModal').style.display='none'" class="px-4 py-2 rounded-lg text-sm" style="background: var(--light-gray); color: var(--text-primary);">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-sm" style="background: var(--blue); color: white;">Add Admin</button>
            </div>
        </form>
    </div>
</div>
@endsection