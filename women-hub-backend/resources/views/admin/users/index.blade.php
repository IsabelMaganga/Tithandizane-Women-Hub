@extends('admin.layouts.admin')

@section('title', 'Manage Users')
@section('page-title', 'User Management')
@section('page-subtitle', 'View and manage all registered users across the platform')

@push('styles')
<style>
    /* Modern Card Styles */
    .stat-card-modern {
        background: var(--card-bg);
        border-radius: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }
    
    .stat-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--teal-green), var(--blue));
        opacity: 0.6;
    }
    
    .stat-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .stat-icon-blue { background: rgba(52, 152, 219, 0.12); color: var(--blue); }
    .stat-icon-green { background: rgba(46, 204, 113, 0.12); color: var(--teal-green); }
    .stat-icon-orange { background: rgba(243, 156, 18, 0.12); color: var(--orange); }
    .stat-icon-red { background: rgba(231, 76, 60, 0.12); color: var(--red); }
    .stat-icon-purple { background: rgba(155, 89, 182, 0.12); color: var(--purple); }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    
    .stat-trend {
        font-size: 0.75rem;
        padding: 2px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .stat-trend-up { background: rgba(46, 204, 113, 0.15); color: var(--teal-green); }
    .stat-trend-down { background: rgba(231, 76, 60, 0.15); color: var(--red); }
    .stat-trend-neutral { background: rgba(155, 89, 182, 0.15); color: var(--purple); }
    
    /* Progress Bar */
    .progress-bar {
        height: 4px;
        border-radius: 4px;
        background: var(--border-color);
        overflow: hidden;
        margin-top: 8px;
    }
    
    .progress-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
    }
    
    .progress-fill-green { background: var(--teal-green); }
    .progress-fill-blue { background: var(--blue); }
    .progress-fill-orange { background: var(--orange); }
    .progress-fill-red { background: var(--red); }
    
    /* Filter Section */
    .filter-section {
        background: var(--card-bg);
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
    }
    
    .filter-input {
        width: 100%;
        padding: 0.625rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }
    
    .filter-input:focus {
        outline: none;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .filter-select {
        width: 100%;
        padding: 0.625rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid var(--border-color);
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: all 0.2s ease;
        font-size: 0.875rem;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
    }
    
    .filter-select:focus {
        outline: none;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .filter-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.375rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Table Styles */
    .table-wrapper {
        background: var(--card-bg);
        border-radius: 1rem;
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    
    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .modern-table th {
        padding: 0.875rem 1.25rem;
        text-align: left;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }
    
    .modern-table td {
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
        font-size: 0.875rem;
        vertical-align: middle;
    }
    
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .modern-table tbody tr:hover {
        background: var(--bg-primary);
    }
    
    .modern-table tbody tr:active {
        transform: scale(0.99);
    }
    
    /* Avatar */
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-color);
        transition: all 0.2s ease;
    }
    
    .user-avatar:hover {
        transform: scale(1.05);
        border-color: var(--blue);
    }
    
    /* Badge */
    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    
    .badge-admin { background: rgba(155, 89, 182, 0.15); color: var(--purple); }
    .badge-mentor { background: rgba(52, 152, 219, 0.15); color: var(--blue); }
    .badge-user { background: rgba(46, 204, 113, 0.15); color: var(--teal-green); }
    
    .badge-active { background: rgba(46, 204, 113, 0.15); color: var(--teal-green); }
    .badge-inactive { background: rgba(243, 156, 18, 0.15); color: var(--orange); }
    .badge-banned { background: rgba(231, 76, 60, 0.15); color: var(--red); }
    
    /* Action Buttons */
    .action-btn {
        padding: 0.375rem 0.625rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: transparent;
        cursor: pointer;
        border: none;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    
    .action-btn:hover {
        transform: scale(1.1);
    }
    
    .action-btn-view:hover { background: rgba(52, 152, 219, 0.15); color: var(--blue); }
    .action-btn-edit:hover { background: rgba(243, 156, 18, 0.15); color: var(--orange); }
    .action-btn-delete:hover { background: rgba(231, 76, 60, 0.15); color: var(--red); }
    
    /* Bulk Action Bar */
    .bulk-bar {
        display: none;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.5rem;
        background: var(--blue);
        color: white;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
        animation: slideDown 0.3s ease;
    }
    
    .bulk-bar.show {
        display: flex;
    }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .bulk-btn {
        padding: 0.375rem 1rem;
        border-radius: 0.5rem;
        border: none;
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        color: white;
    }
    
    .bulk-btn:hover { opacity: 0.8; transform: scale(0.97); }
    .bulk-btn-danger { background: #dc2626; }
    .bulk-btn-success { background: #16a34a; }
    .bulk-btn-warning { background: #ea580c; }
    .bulk-btn-secondary { background: #6b7280; }
    
    /* Checkbox */
    .checkbox-custom {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 2px solid var(--border-color);
        cursor: pointer;
        accent-color: var(--blue);
        transition: all 0.2s ease;
    }
    
    .checkbox-custom:checked {
        border-color: var(--blue);
    }
    
    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .modal-overlay.show {
        display: flex;
    }
    
    .modal-container {
        background: var(--card-bg);
        border-radius: 1.25rem;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow: auto;
        animation: modalIn 0.3s ease;
    }
    
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    
    .modal-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, var(--blue), var(--purple));
        border-radius: 1.25rem 1.25rem 0 0;
    }
    
    .modal-header h3 {
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }
    
    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
        line-height: 1;
    }
    
    .modal-close:hover { transform: rotate(90deg); opacity: 0.8; }
    .modal-body { padding: 2rem; }
    .modal-footer { padding: 1.25rem 2rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.75rem; }
    
    /* Empty State */
    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: var(--text-secondary);
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    
    .empty-state-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .empty-state-subtitle {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stat-card-modern { padding: 1rem; }
        .stat-value { font-size: 1.5rem; }
        .stat-icon { width: 40px; height: 40px; font-size: 1.25rem; }
        .modal-container { max-width: 95%; margin: 1rem; }
        .modern-table th, .modern-table td { padding: 0.625rem 0.875rem; font-size: 0.8rem; }
        .bulk-bar { flex-wrap: wrap; gap: 0.5rem; }
        .bulk-bar .flex { flex-wrap: wrap; gap: 0.5rem; }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Users -->
        <div class="stat-card-modern p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Total Users</p>
                    <p class="stat-value mt-1">{{ $totalUsers ?? 0 }}</p>
                </div>
                <div class="stat-icon stat-icon-blue">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="stat-trend stat-trend-up">
                    <i class="fas fa-arrow-up"></i> 12%
                </span>
                <span class="text-xs text-secondary-color ml-2">from last month</span>
            </div>
        </div>

        <!-- Active Users -->
        <div class="stat-card-modern p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Active Users</p>
                    <p class="stat-value mt-1">{{ $activeUsers ?? 0 }}</p>
                </div>
                <div class="stat-icon stat-icon-green">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="progress-bar">
                    <div class="progress-bar-fill progress-fill-green" style="width: {{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0 }}%"></div>
                </div>
                <span class="text-xs text-secondary-color mt-1 block">{{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0 }}% of total</span>
            </div>
        </div>

        <!-- Inactive Users -->
        <div class="stat-card-modern p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Inactive Users</p>
                    <p class="stat-value mt-1">{{ $inactiveUsers ?? 0 }}</p>
                </div>
                <div class="stat-icon stat-icon-orange">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="stat-trend stat-trend-neutral">
                    <i class="fas fa-clock"></i> Needs review
                </span>
            </div>
        </div>

        <!-- Banned Users -->
        <div class="stat-card-modern p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-label">Banned Users</p>
                    <p class="stat-value mt-1">{{ $bannedUsers ?? 0 }}</p>
                </div>
                <div class="stat-icon stat-icon-red">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="stat-trend stat-trend-down">
                    <i class="fas fa-exclamation-triangle"></i> Restricted
                </span>
            </div>
        </div>
    </div>

    <!-- Role Distribution & Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="filter-section">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-chart-pie" style="color: var(--purple);"></i>
                <h3 class="font-semibold text-primary-color">Role Distribution</h3>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($roleStats ?? [] as $stat)
                <span class="badge badge-admin">
                    {{ ucfirst($stat->role) }} ({{ $stat->count }})
                </span>
                @endforeach
            </div>
        </div>

        <div class="filter-section">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-chart-line" style="color: var(--blue);"></i>
                <h3 class="font-semibold text-primary-color">Quick Stats</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-secondary-color uppercase tracking-wide">Active Rate</p>
                    <p class="text-2xl font-bold text-primary-color">{{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0 }}%</p>
                </div>
                <div>
                    <p class="text-xs text-secondary-color uppercase tracking-wide">Banned Rate</p>
                    <p class="text-2xl font-bold text-primary-color">{{ $totalUsers > 0 ? round(($bannedUsers / $totalUsers) * 100) : 0 }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="filter-section">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-sliders-h" style="color: var(--purple);"></i>
                <h3 class="font-semibold text-primary-color">Filter Users</h3>
            </div>
            <button onclick="toggleFilters()" class="text-sm" style="color: var(--blue);">
                <i class="fas fa-chevron-down mr-1"></i> Advanced
            </button>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="filter-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name, email or phone..." class="filter-input">
                </div>
                <div>
                    <label class="filter-label">Role</label>
                    <select name="role" class="filter-select">
                        <option value="">All Roles</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="mentor" {{ request('role') == 'mentor' ? 'selected' : '' }}>Mentor</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Status</label>
                    <select name="status" class="filter-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 rounded-lg font-medium transition hover:opacity-90" style="background: var(--blue); color: white;">
                        <i class="fas fa-search mr-2"></i>Apply
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg border transition hover:bg-gray-100" style="border-color: var(--border-color); color: var(--text-secondary);">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div id="advancedFilters" class="mt-4 pt-4 border-t" style="border-color: var(--border-color); display: none;">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="filter-label">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-input">
                    </div>
                    <div>
                        <label class="filter-label">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-input">
                    </div>
                    <div>
                        <label class="filter-label">Email Verified</label>
                        <select name="email_verified" class="filter-select">
                            <option value="">All</option>
                            <option value="yes" {{ request('email_verified') == 'yes' ? 'selected' : '' }}>Verified</option>
                            <option value="no" {{ request('email_verified') == 'no' ? 'selected' : '' }}>Not Verified</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkBar" class="bulk-bar">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span id="selectedCount">0</span> users selected
        </div>
        <div class="flex gap-2">
            <button onclick="bulkAction('delete')" class="bulk-btn bulk-btn-danger">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
            <button onclick="bulkAction('active')" class="bulk-btn bulk-btn-success">
                <i class="fas fa-check mr-1"></i> Activate
            </button>
            <button onclick="bulkAction('inactive')" class="bulk-btn bulk-btn-warning">
                <i class="fas fa-pause mr-1"></i> Deactivate
            </button>
            <button onclick="clearSelection()" class="bulk-btn bulk-btn-secondary">
                <i class="fas fa-times mr-1"></i> Clear
            </button>
        </div>
    </div>

    <!-- Export Button -->
    <div class="flex justify-end">
        <button onclick="exportUsers()" class="px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition hover:opacity-90" style="background: var(--teal-green); color: white;">
            <i class="fas fa-download"></i> Export
        </button>
    </div>

    <!-- Users Table -->
    <div class="table-wrapper">
        <div class="overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="w-8">
                            <input type="checkbox" id="selectAll" class="checkbox-custom" onclick="toggleAll()">
                        </th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox-custom user-checkbox" 
                                   data-id="{{ $user->id }}" onchange="updateBulkBar()">
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=3498db&color=fff&bold=true&size=40&length=2"
                                     class="user-avatar" alt="{{ $user->name }}">
                                <div>
                                    <p class="font-medium text-primary-color">{{ $user->name }}</p>
                                    <p class="text-xs text-secondary-color">ID: #{{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-sm text-primary-color">{{ $user->email }}</span>
                                @if($user->phone ?? false)
                                <span class="text-xs text-secondary-color">{{ $user->phone }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->role ?? 'user' }}">
                                <i class="fas fa-{{ $user->role == 'admin' ? 'crown' : ($user->role == 'mentor' ? 'chalkboard-user' : 'user') }}"></i>
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->status ?? 'active' }}">
                                <i class="fas fa-{{ $user->status == 'active' ? 'check-circle' : ($user->status == 'inactive' ? 'pause-circle' : 'ban') }}"></i>
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-sm text-primary-color">{{ $user->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-secondary-color">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="viewUser({{ $user->id }})" class="action-btn action-btn-view" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="openStatusModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->status ?? 'active' }}')" 
                                        class="action-btn action-btn-edit" title="Change Status">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                                <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" 
                                        class="action-btn action-btn-delete" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon"><i class="fas fa-users-slash"></i></div>
                                <p class="empty-state-title">No users found</p>
                                <p class="empty-state-subtitle">Try adjusting your search or filter criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($users) && $users->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--border-color);">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- View User Modal -->
<div id="viewModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-user-circle mr-2"></i>User Profile</h3>
            <button onclick="closeModal('viewModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="userDetails">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl" style="color: var(--blue);"></i>
                <p class="mt-2 text-secondary-color">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div id="statusModal" class="modal-overlay">
    <div class="modal-container max-w-md">
        <div class="modal-header" style="background: linear-gradient(135deg, var(--orange), var(--red));">
            <h3><i class="fas fa-exchange-alt mr-2"></i>Change Status</h3>
            <button onclick="closeModal('statusModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p class="text-primary-color mb-4">
                Update status for <strong id="statusUserName" class="text-blue"></strong>
            </p>
            <select id="newStatus" class="filter-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="banned">Banned</option>
            </select>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('statusModal')" class="px-4 py-2 rounded-lg border transition hover:bg-gray-100" style="border-color: var(--border-color); color: var(--text-secondary);">
                Cancel
            </button>
            <button onclick="confirmStatus()" class="px-4 py-2 rounded-lg transition hover:opacity-90" style="background: var(--blue); color: white;">
                <i class="fas fa-save mr-1"></i> Update
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal-container max-w-md">
        <div class="modal-header" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
            <h3><i class="fas fa-exclamation-triangle mr-2"></i>Confirm Deletion</h3>
            <button onclick="closeModal('deleteModal')" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-4">
                <i class="fas fa-trash-alt text-5xl" style="color: #dc2626;"></i>
            </div>
            <p class="text-primary-color mb-4 text-center">
                Are you sure you want to delete <strong id="deleteUserName" class="text-red"></strong>?
            </p>
            <p class="text-sm text-secondary-color text-center">
                This action cannot be undone. All associated data will be permanently removed.
            </p>
        </div>
        <div class="modal-footer justify-center">
            <button onclick="closeModal('deleteModal')" class="px-6 py-2.5 rounded-lg border transition hover:bg-gray-100" style="border-color: var(--border-color); color: var(--text-secondary);">
                <i class="fas fa-times mr-2"></i>Cancel
            </button>
            <button onclick="confirmDelete()" class="px-6 py-2.5 rounded-lg transition hover:opacity-90" style="background: #dc2626; color: white;">
                <i class="fas fa-trash-alt mr-2"></i>Delete User
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedUsers = [];
    let statusUserId = null;
    let statusUserName = '';
    let deleteUserId = null;
    let deleteUserName = '';

    // Toggle advanced filters
    function toggleFilters() {
        const panel = document.getElementById('advancedFilters');
        const icon = event.currentTarget.querySelector('i');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            icon.className = 'fas fa-chevron-up mr-1';
        } else {
            panel.style.display = 'none';
            icon.className = 'fas fa-chevron-down mr-1';
        }
    }

    // Select all
    function toggleAll() {
        const checked = document.getElementById('selectAll').checked;
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = checked);
        updateBulkBar();
    }

    // Update bulk bar
    function updateBulkBar() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        selectedUsers = Array.from(checkboxes).map(cb => cb.dataset.id);
        const count = selectedUsers.length;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('bulkBar').className = `bulk-bar ${count > 0 ? 'show' : ''}`;
    }

    // Clear selection
    function clearSelection() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        updateBulkBar();
    }

    // Bulk action
    function bulkAction(action) {
        if (selectedUsers.length === 0) return;
        
        const actions = {
            'delete': { confirm: `Delete ${selectedUsers.length} user(s)?`, method: 'DELETE', url: '/admin/users/' },
            'active': { confirm: `Activate ${selectedUsers.length} user(s)?`, method: 'PUT', url: '/admin/users/' },
            'inactive': { confirm: `Deactivate ${selectedUsers.length} user(s)?`, method: 'PUT', url: '/admin/users/' }
        };
        
        const config = actions[action];
        if (!config) return;
        
        if (!confirm(config.confirm + ' This action cannot be undone.')) return;
        
        selectedUsers.forEach(id => {
            fetch(`${config.url}${id}${action !== 'delete' ? '/status' : ''}`, {
                method: config.method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: action !== 'delete' ? JSON.stringify({ status: action }) : undefined
            });
        });
        
        showNotification(`${selectedUsers.length} user(s) ${action}d successfully`, 'success');
        setTimeout(() => location.reload(), 1500);
    }

    // Export users
    function exportUsers() {
        window.location.href = '{{ route("admin.users.index") }}?export=true&' + new URLSearchParams(window.location.search).toString();
    }

    // View user
    function viewUser(id) {
        const modal = document.getElementById('viewModal');
        const details = document.getElementById('userDetails');
        modal.classList.add('show');
        details.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl" style="color: var(--blue);"></i><p class="mt-2 text-secondary-color">Loading...</p></div>';
        
        fetch(`/admin/users/${id}/json`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const u = data.user;
                details.innerHTML = `
                    <div class="flex items-center gap-6 pb-6 border-b" style="border-color: var(--border-color);">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=3498db&color=fff&bold=true&size=80&length=2"
                             class="w-20 h-20 rounded-full border-4" style="border-color: var(--blue);">
                        <div>
                            <h4 class="text-xl font-bold text-primary-color">${escapeHtml(u.name)}</h4>
                            <p class="text-secondary-color">${escapeHtml(u.email)}</p>
                            <div class="flex gap-2 mt-2">
                                <span class="badge badge-${u.role || 'user'}">${u.role || 'User'}</span>
                                <span class="badge badge-${u.status || 'active'}">${u.status || 'Active'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div class="p-3 rounded-lg" style="background: var(--bg-primary);">
                            <p class="text-xs text-secondary-color uppercase tracking-wide">Phone</p>
                            <p class="font-medium text-primary-color mt-1">${u.phone || 'Not provided'}</p>
                        </div>
                        <div class="p-3 rounded-lg" style="background: var(--bg-primary);">
                            <p class="text-xs text-secondary-color uppercase tracking-wide">Joined</p>
                            <p class="font-medium text-primary-color mt-1">${new Date(u.created_at).toLocaleDateString()}</p>
                        </div>
                        <div class="p-3 rounded-lg" style="background: var(--bg-primary);">
                            <p class="text-xs text-secondary-color uppercase tracking-wide">Last Updated</p>
                            <p class="font-medium text-primary-color mt-1">${new Date(u.updated_at).toLocaleDateString()}</p>
                        </div>
                        <div class="p-3 rounded-lg" style="background: var(--bg-primary);">
                            <p class="text-xs text-secondary-color uppercase tracking-wide">User ID</p>
                            <p class="font-medium text-primary-color mt-1">#${u.id}</p>
                        </div>
                    </div>
                `;
            }
        });
    }

    // Open status modal
    function openStatusModal(id, name, status) {
        statusUserId = id;
        statusUserName = name;
        document.getElementById('statusUserName').textContent = name;
        document.getElementById('newStatus').value = status;
        document.getElementById('statusModal').classList.add('show');
    }

    // Confirm status change
    function confirmStatus() {
        const status = document.getElementById('newStatus').value;
        fetch(`/admin/users/${statusUserId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(err => {
            showNotification('Failed to update status', 'error');
        });
        closeModal('statusModal');
    }

    // Delete user
    function deleteUser(id, name) {
        deleteUserId = id;
        deleteUserName = name;
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteModal').classList.add('show');
    }

    // Confirm delete
    function confirmDelete() {
        fetch(`/admin/users/${deleteUserId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                closeModal('deleteModal');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(err => {
            showNotification('Failed to delete user', 'error');
        });
    }

    // Close modal
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    // Show notification
    function showNotification(message, type = 'success') {
        const colors = {
            success: { bg: '#22c55e', icon: 'fa-check-circle' },
            error: { bg: '#ef4444', icon: 'fa-exclamation-circle' }
        };
        const config = colors[type] || colors.success;
        
        const el = document.createElement('div');
        el.className = 'fixed top-20 right-4 p-4 rounded-lg shadow-lg z-50 text-white flex items-center gap-3';
        el.style.background = config.bg;
        el.style.animation = 'slideDown 0.3s ease';
        el.innerHTML = `<i class="fas ${config.icon} text-xl"></i><span>${message}</span>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    // Escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.show').forEach(el => el.classList.remove('show'));
        }
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            document.getElementById('selectAll').click();
        }
    });
</script>
@endpush