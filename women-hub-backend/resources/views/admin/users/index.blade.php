@extends('admin.layouts.admin')
@section('title', 'Manage Users')
@section('page-title', 'User Management')
@section('page-subtitle', 'View and manage all registered users')

@push('styles')
<style>
    /* Modern Card Styles */
    .stat-card-modern {
        background: var(--card-bg);
        border-radius: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--teal-green), var(--blue));
    }
    
    .stat-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
    }
    
    /* Gradient Border for Filters */
    .filter-section-modern {
        background: var(--card-bg);
        border-radius: 1rem;
        position: relative;
        padding: 1.5rem;
    }
    
    /* Modern Table Styles */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .modern-table th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: var(--bg-secondary);
        border-bottom: 2px solid var(--border-color);
    }
    
    .modern-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        transition: background 0.2s ease;
    }
    
    .modern-table tbody tr:hover {
        background: var(--light-gray);
        cursor: pointer;
    }
    
    /* Action Buttons */
    .action-btn {
        padding: 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: transparent;
        cursor: pointer;
    }
    
    .action-btn-view:hover {
        background: var(--light-blue);
        color: var(--blue);
        transform: scale(1.1);
    }
    
    .action-btn-edit:hover {
        background: var(--light-orange);
        color: var(--orange);
        transform: scale(1.1);
    }
    
    .action-btn-delete:hover {
        background: var(--light-red);
        color: var(--red);
        transform: scale(1.1);
    }
    
    /* Avatar Animation */
    .user-avatar-modern {
        transition: transform 0.2s ease;
    }
    
    .user-avatar-modern:hover {
        transform: scale(1.1);
    }
    
    /* Search Input Focus Effect */
    .search-input:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    /* Custom Checkbox */
    .checkbox-wrapper {
        position: relative;
        display: inline-block;
    }
    
    .checkbox-wrapper input {
        width: 1.2rem;
        height: 1.2rem;
        cursor: pointer;
        accent-color: var(--blue);
    }
    
    /* Export Button */
    .export-btn {
        background: linear-gradient(135deg, var(--teal-green), var(--green));
        transition: all 0.3s ease;
    }
    
    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(46, 204, 113, 0.3);
    }
    
    /* Bulk Action Bar */
    .bulk-action-bar {
        background: var(--blue);
        color: white;
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        display: none;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    /* Advanced Filter Panel */
    .advanced-filters {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    
    .advanced-filters.show {
        max-height: 500px;
    }
    
    /* Loading Skeleton */
    .skeleton {
        background: linear-gradient(90deg, var(--border-color) 25%, var(--light-gray) 50%, var(--border-color) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Modern Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="stat-card-modern p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-xl bg-blue/10">
                    <i class="fas fa-users text-2xl text-blue"></i>
                </div>
                <span class="text-3xl font-bold text-primary-color">{{ $totalUsers }}</span>
            </div>
            <div>
                <p class="text-secondary-color text-sm font-medium">Total Users</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-xs text-success">
                        <i class="fas fa-arrow-up"></i> +12%
                    </span>
                    <span class="text-xs text-secondary-color">from last month</span>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="stat-card-modern p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-xl bg-success/10">
                    <i class="fas fa-user-check text-2xl text-success"></i>
                </div>
                <span class="text-3xl font-bold text-primary-color">{{ $activeUsers }}</span>
            </div>
            <div>
                <p class="text-secondary-color text-sm font-medium">Active Users</p>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-success rounded-full h-2" style="width: {{ $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0 }}%"></div>
                    </div>
                    <p class="text-xs text-secondary-color mt-1">{{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0 }}% of total</p>
                </div>
            </div>
        </div>

        <!-- Inactive Users -->
        <div class="stat-card-modern p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-xl bg-warning/10">
                    <i class="fas fa-user-clock text-2xl text-warning"></i>
                </div>
                <span class="text-3xl font-bold text-primary-color">{{ $inactiveUsers }}</span>
            </div>
            <div>
                <p class="text-secondary-color text-sm font-medium">Inactive Users</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-xs text-warning">
                        <i class="fas fa-clock"></i> Needs attention
                    </span>
                </div>
            </div>
        </div>

        <!-- Banned Users -->
        <div class="stat-card-modern p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-xl bg-danger/10">
                    <i class="fas fa-ban text-2xl text-danger"></i>
                </div>
                <span class="text-3xl font-bold text-primary-color">{{ $bannedUsers }}</span>
            </div>
            <div>
                <p class="text-secondary-color text-sm font-medium">Banned Users</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-xs text-danger">
                        <i class="fas fa-exclamation-triangle"></i> Restricted access
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Distribution & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="filter-section-modern">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-chart-pie text-xl text-purple"></i>
                <h3 class="font-semibold text-primary-color">Role Distribution</h3>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach($roleStats as $stat)
                <div class="px-4 py-2 rounded-full" style="background: var(--light-purple);">
                    <span class="text-sm font-medium" style="color: var(--purple);">
                        {{ ucfirst($stat->role) }}: {{ $stat->count }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="filter-section-modern">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-chart-line text-xl text-info"></i>
                <h3 class="font-semibold text-primary-color">Quick Stats</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-secondary-color">Active Rate</p>
                    <p class="text-2xl font-bold text-primary-color">{{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0 }}%</p>
                </div>
                <div>
                    <p class="text-xs text-secondary-color">Banned Rate</p>
                    <p class="text-2xl font-bold text-primary-color">{{ $totalUsers > 0 ? round(($bannedUsers / $totalUsers) * 100) : 0 }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <div class="filter-section-modern">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-filter text-lg text-purple"></i>
                <h3 class="font-semibold text-primary-color">Filter Users</h3>
            </div>
            <button type="button" onclick="toggleAdvancedFilters()" class="text-sm text-blue hover:underline">
                <i class="fas fa-sliders-h mr-1"></i> Advanced Filters
            </button>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">
                        <i class="fas fa-search mr-1"></i> Search
                    </label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Name, email or phone..."
                           class="search-input w-full px-4 py-2.5 rounded-lg border border-color bg-transparent text-primary-color focus:outline-none focus:ring-2 focus:ring-blue transition">
                </div>
               
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">
                        <i class="fas fa-tag mr-1"></i> Role
                    </label>
                    <select name="role" class="w-full px-4 py-2.5 rounded-lg border border-color bg-transparent text-primary-color focus:outline-none focus:ring-2 focus:ring-blue">
                        <option value="">All Roles</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>👤 User</option>
                        <option value="mentor" {{ request('role') == 'mentor' ? 'selected' : '' }}>🎓 Mentor</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>⚙️ Admin</option>
                    </select>
                </div>
               
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">
                        <i class="fas fa-circle mr-1"></i> Status
                    </label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-lg border border-color bg-transparent text-primary-color focus:outline-none focus:ring-2 focus:ring-blue">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>⏸️ Inactive</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>🚫 Banned</option>
                    </select>
                </div>
               
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-6 py-2.5 bg-blue text-white rounded-lg hover:opacity-90 transition transform hover:scale-105">
                        <i class="fas fa-search mr-2"></i>Apply
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 border border-color rounded-lg text-secondary-color hover:bg-gray-100 transition">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>

            <!-- Advanced Filters Panel -->
            <div id="advancedFilters" class="advanced-filters mt-4 pt-4 border-t border-color">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary-color mb-2">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 rounded-lg border border-color bg-transparent text-primary-color">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary-color mb-2">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 rounded-lg border border-color bg-transparent text-primary-color">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary-color mb-2">Email Verified</label>
                        <select name="email_verified" class="w-full px-4 py-2 rounded-lg border border-color bg-transparent text-primary-color">
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
    <div id="bulkActionBar" class="bulk-action-bar">
        <div class="flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span id="selectedCount">0</span> users selected
        </div>
        <div class="flex gap-3">
            <button onclick="bulkDelete()" class="px-4 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
            <button onclick="bulkStatusChange('active')" class="px-4 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-check mr-1"></i> Activate
            </button>
            <button onclick="bulkStatusChange('inactive')" class="px-4 py-1 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                <i class="fas fa-pause mr-1"></i> Deactivate
            </button>
            <button onclick="clearSelection()" class="px-4 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-times mr-1"></i> Clear
            </button>
        </div>
    </div>

    <!-- Export Button -->
    <div class="flex justify-end">
        <button onclick="exportUsers()" class="export-btn px-6 py-2.5 rounded-lg text-white font-medium flex items-center gap-2">
            <i class="fas fa-download"></i> Export Users
        </button>
    </div>

    <!-- Users Table -->
    <div class="card-bg rounded-xl shadow-sm overflow-hidden" style="background: var(--card-bg);">
        <div class="overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="w-12">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                            </div>
                        </th>
                        <th>
                            <a href="{{ route('admin.users.index', array_merge(request()->all(), ['sort_by' => 'name', 'sort_order' => request('sort_by') == 'name' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-primary-color flex items-center gap-1">
                                User
                                @if(request('sort_by') == 'name')
                                    <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Contact</th>
                        <th>
                            <a href="{{ route('admin.users.index', array_merge(request()->all(), ['sort_by' => 'role', 'sort_order' => request('sort_by') == 'role' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-primary-color flex items-center gap-1">
                                Role
                                @if(request('sort_by') == 'role')
                                    <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.users.index', array_merge(request()->all(), ['sort_by' => 'status', 'sort_order' => request('sort_by') == 'status' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-primary-color flex items-center gap-1">
                                Status
                                @if(request('sort_by') == 'status')
                                    <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.users.index', array_merge(request()->all(), ['sort_by' => 'created_at', 'sort_order' => request('sort_by') == 'created_at' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-primary-color flex items-center gap-1">
                                Joined
                                @if(request('sort_by') == 'created_at')
                                    <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" class="user-checkbox" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" onclick="updateSelectedCount()">
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=3498db&color=fff&bold=true&size=40&length=2"
                                     class="user-avatar-modern w-10 h-10 rounded-full object-cover ring-2 ring-offset-2 ring-blue"
                                     alt="{{ $user->name }}">
                                <div>
                                    <p class="font-semibold text-primary-color">{{ $user->name }}</p>
                                    <p class="text-xs text-secondary-color">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <div class="text-primary-color flex items-center gap-1">
                                    <i class="fas fa-envelope text-xs text-secondary-color"></i>
                                    <span>{{ $user->email }}</span>
                                </div>
                                @if($user->phone)
                                <div class="text-xs text-secondary-color mt-1 flex items-center gap-1">
                                    <i class="fas fa-phone text-xs"></i>
                                    <span>{{ $user->phone }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $roleColors = [
                                    'admin' => 'bg-purple-100 text-purple-700',
                                    'mentor' => 'bg-blue-100 text-blue-700',
                                    'user' => 'bg-green-100 text-green-700'
                                ];
                                $roleColor = $roleColors[$user->role ?? 'user'] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $roleColor }}">
                                <i class="fas fa-{{ $user->role == 'admin' ? 'crown' : ($user->role == 'mentor' ? 'chalkboard-user' : 'user') }} mr-1"></i>
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-700',
                                    'inactive' => 'bg-orange-100 text-orange-700',
                                    'banned' => 'bg-red-100 text-red-700'
                                ];
                                $statusColor = $statusColors[$user->status ?? 'active'] ?? 'bg-gray-100 text-gray-700';
                                $statusIcons = [
                                    'active' => 'fa-check-circle',
                                    'inactive' => 'fa-pause-circle',
                                    'banned' => 'fa-ban'
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                <i class="fas {{ $statusIcons[$user->status ?? 'active'] }} mr-1"></i>
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td class="text-sm text-secondary-color">
                            <div class="flex flex-col">
                                <span>{{ $user->created_at->format('M d, Y') }}</span>
                                <span class="text-xs">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="flex gap-1 justify-center">
                                <button onclick="viewUser({{ $user->id }})"
                                        class="action-btn action-btn-view p-2 rounded-lg"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                               
                                <button onclick="changeUserStatus({{ $user->id }}, '{{ $user->status ?? 'active' }}')"
                                        class="action-btn action-btn-edit p-2 rounded-lg"
                                        title="Change Status">
                                    <i class="fas fa-user-edit"></i>
                                </button>
                               
                                <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')"
                                        class="action-btn action-btn-delete p-2 rounded-lg"
                                        title="Delete User">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-users-slash text-6xl text-secondary-color mb-4"></i>
                                <p class="text-secondary-color text-lg">No users found</p>
                                <p class="text-sm text-secondary-color mt-2">Try adjusting your search or filter criteria</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
       
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-color">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- User Details Modal -->
<div id="userModal" class="modal">
    <div class="modal-content max-w-2xl">
        <div class="p-6 border-b border-color flex justify-between items-center" style="background: linear-gradient(135deg, var(--blue) 0%, var(--purple) 100%);">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-user-circle mr-2"></i>User Profile
            </h3>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="userDetails">
            <!-- User details will be loaded here -->
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="p-6 border-b border-color" style="background: linear-gradient(135deg, var(--orange) 0%, var(--red) 100%);">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-exchange-alt mr-2"></i>Change User Status
            </h3>
        </div>
        <div class="p-6">
            <p class="mb-4 text-primary-color">Select new status for <strong id="statusUserName" class="text-blue"></strong></p>
            <select id="newStatus" class="w-full px-4 py-2.5 rounded-lg border border-color bg-transparent text-primary-color focus:outline-none focus:ring-2 focus:ring-blue mb-4">
                <option value="active">✅ Active</option>
                <option value="inactive">⏸️ Inactive</option>
                <option value="banned">🚫 Banned</option>
            </select>
            <div class="flex gap-3 justify-end">
                <button onclick="closeStatusModal()" class="px-4 py-2 border border-color rounded-lg text-secondary-color hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button onclick="confirmStatusChange()" class="px-4 py-2 bg-blue text-white rounded-lg hover:opacity-90 transition transform hover:scale-105">
                    <i class="fas fa-save mr-1"></i> Update Status
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentUserId = null;
    let currentUserName = null;
    let currentStatus = null;
    let selectedUsers = [];

    // Bulk Actions
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        selectedUsers = Array.from(checkboxes).map(cb => ({
            id: cb.dataset.userId,
            name: cb.dataset.userName
        }));
        
        const count = selectedUsers.length;
        document.getElementById('selectedCount').innerText = count;
        document.getElementById('bulkActionBar').style.display = count > 0 ? 'flex' : 'none';
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
        document.getElementById('selectAll').checked = false;
        updateSelectedCount();
    }

    function bulkDelete() {
        if (selectedUsers.length === 0) return;
        
        if (confirm(`Are you sure you want to delete ${selectedUsers.length} user(s)? This action cannot be undone.`)) {
            selectedUsers.forEach(user => {
                fetch(`/admin/users/${user.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
            });
            showNotification(`${selectedUsers.length} user(s) deleted successfully`, 'success');
            setTimeout(() => location.reload(), 1500);
        }
    }

    function bulkStatusChange(status) {
        if (selectedUsers.length === 0) return;
        
        if (confirm(`Change status to ${status} for ${selectedUsers.length} user(s)?`)) {
            selectedUsers.forEach(user => {
                fetch(`/admin/users/${user.id}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                });
            });
            showNotification(`${selectedUsers.length} user(s) status updated to ${status}`, 'success');
            setTimeout(() => location.reload(), 1500);
        }
    }

    function exportUsers() {
        window.location.href = '{{ route("admin.users.index") }}?export=true&' + new URLSearchParams(window.location.search).toString();
    }

    function toggleAdvancedFilters() {
        const panel = document.getElementById('advancedFilters');
        panel.classList.toggle('show');
    }

    // View user details
    function viewUser(userId) {
        fetch(`/admin/users/${userId}/json`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                document.getElementById('userDetails').innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center gap-6 pb-6 border-b border-color">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=3498db&color=fff&bold=true&size=100&length=2"
                                 class="w-24 h-24 rounded-full ring-4 ring-blue">
                            <div>
                                <h4 class="text-2xl font-bold text-primary-color">${escapeHtml(user.name)}</h4>
                                <p class="text-secondary-color">${escapeHtml(user.email)}</p>
                                <div class="flex gap-2 mt-2">
                                    <span class="px-2 py-1 rounded-full text-xs ${user.role === 'admin' ? 'bg-purple-100 text-purple-700' : (user.role === 'mentor' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700')}">
                                        ${user.role || 'User'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="p-4 rounded-lg" style="background: var(--light-gray);">
                                <p class="text-xs text-secondary-color uppercase tracking-wide">Phone Number</p>
                                <p class="text-primary-color font-medium mt-1">${user.phone || 'Not provided'}</p>
                            </div>
                            <div class="p-4 rounded-lg" style="background: var(--light-gray);">
                                <p class="text-xs text-secondary-color uppercase tracking-wide">Status</p>
                                <p class="text-primary-color font-medium mt-1 capitalize">${user.status || 'Active'}</p>
                            </div>
                            <div class="p-4 rounded-lg" style="background: var(--light-gray);">
                                <p class="text-xs text-secondary-color uppercase tracking-wide">Joined Date</p>
                                <p class="text-primary-color font-medium mt-1">${new Date(user.created_at).toLocaleDateString()}</p>
                            </div>
                            <div class="p-4 rounded-lg" style="background: var(--light-gray);">
                                <p class="text-xs text-secondary-color uppercase tracking-wide">Last Updated</p>
                                <p class="text-primary-color font-medium mt-1">${new Date(user.updated_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('userModal').style.display = 'flex';
            }
        })
        .catch(error => console.error('Error:', error));
    }
   
    // Change user status
    function changeUserStatus(userId, currentStatusValue) {
        currentUserId = userId;
        currentStatus = currentStatusValue;
       
        fetch(`/admin/users/${userId}/json`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentUserName = data.user.name;
                document.getElementById('statusUserName').textContent = currentUserName;
                document.getElementById('newStatus').value = currentStatus;
                document.getElementById('statusModal').style.display = 'flex';
            }
        });
    }
   
    function confirmStatusChange() {
        const newStatus = document.getElementById('newStatus').value;
       
        fetch(`/admin/users/${currentUserId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error updating user status', 'error');
        });
       
        closeStatusModal();
    }
   
    // Delete user
    function deleteUser(userId, userName) {
        if (confirm(`⚠️ Are you sure you want to delete "${userName}"?\n\nThis action cannot be undone and will permanently remove the user from the system.`)) {
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('Error deleting user', 'error');
            });
        }
    }
   
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
   
    // Close modals
    function closeModal() {
        document.getElementById('userModal').style.display = 'none';
    }
   
    function closeStatusModal() {
        document.getElementById('statusModal').style.display = 'none';
    }
   
    // Show notification
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-20 right-4 p-4 rounded-lg shadow-lg z-50 animate-slide-in ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} text-xl"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
   
    // Close modals when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('userModal');
        const statusModal = document.getElementById('statusModal');
        if (event.target === modal) closeModal();
        if (event.target === statusModal) closeStatusModal();
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            document.getElementById('selectAll').checked = true;
            toggleSelectAll();
        }
        if (e.key === 'Escape') {
            closeModal();
            closeStatusModal();
        }
    });
</script>
@endpush