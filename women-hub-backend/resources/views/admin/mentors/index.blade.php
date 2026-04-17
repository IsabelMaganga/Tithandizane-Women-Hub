{{-- resources/views/admin/mentors/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tithandizane Women Hub | Manage Mentors</title>
    <!-- Tailwind + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom smooth transitions & custom scrollbar */
        body {
            background: #F8FAFE;
            font-family: system-ui, 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #E2E8F0;
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #874179;
            border-radius: 10px;
        }
        .hover-scale {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-scale:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.15);
        }
        .card-shadow {
            box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 6px rgba(0,0,0,0.05);
        }
        .nav-item {
            transition: all 0.2s ease;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        input:focus, button:focus {
            outline: none;
            box-shadow: 0 0 0 2px #F3E6F1;
        }
        
        /* Toast notification */
        .toast {
            visibility: hidden;
            min-width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 16px;
            position: fixed;
            z-index: 1000;
            left: 50%;
            bottom: 30px;
            font-size: 14px;
            transform: translateX(-50%);
        }
        .toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }
        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }
        
        /* Status badge animations */
        .badge {
            transition: all 0.2s ease;
        }
        
        /* Delete modal */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
        }

        /* Stats Card Animations */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
        }
        .stat-card:active {
            transform: translateY(0);
        }
        
        /* Pulse animation for numbers when they change */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .number-update {
            animation: pulse 0.3s ease-in-out;
        }

        /* ===== Enhanced Progress Circle ===== */
        .progress-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
        }

        .progress-circle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(
                var(--color) 0%,
                var(--color) calc(var(--value) * 1%),
                #E5E7EB calc(var(--value) * 1%)
            );
            transition: background 0.8s ease-in-out;
        }

        .progress-inner {
            position: absolute;
            inset: 10px;
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 4px 10px rgba(0,0,0,0.08);
        }

        .progress-icon {
            font-size: 18px;
            margin-bottom: 2px;
            opacity: 0.7;
        }
        
        /* Button styles */
        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>
<body class="font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- ================= LEFT SIDEBAR ================= -->
    <div class="w-64 flex flex-col shadow-xl" style="background: #874179; border-right: 1px solid #6d3661;">
        <div class="p-6 border-b" style="border-color: #6d3661;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo2.png') }}" alt="Tithandizane Logo" class="w-12 h-12 rounded-full object-cover shadow-md border-2 border-white/30">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">Tithandizane</h1>
                    <p class="text-xs mt-1 opacity-90 text-white">Women Hub</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 mt-6 space-y-1 px-3" id="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" style="color: #E2E8F0;">
                <i class="fas fa-home w-5 text-white"></i>
                <span class="ml-3 font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.mentors.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="mentors" style="background: #6d3661; color: #FFFFFF;">
                <i class="fas fa-chalkboard-user w-5" style="color: #8BC34A;"></i>
                <span class="ml-3 font-medium">Mentors</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="reports" style="color: #E2E8F0;">
                <i class="fas fa-flag w-5" style="color: #9C27B0;"></i>
                <span class="ml-3">Harassment Reports</span>
                <span class="ml-auto bg-rose-600 text-white text-xs font-bold px-2 py-0.5 rounded-full" id="pendingReportsBadge">0</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="guidance" style="color: #E2E8F0;">
                <i class="fas fa-book-open w-5" style="color: #4CAF50;"></i>
                <span class="ml-3">Guidance Content</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="users" style="color: #E2E8F0;">
                <i class="fas fa-user-circle w-5" style="color: #5CB8E4;"></i>
                <span class="ml-3">Users</span>
                <span class="ml-auto bg-gray-600 text-xs px-2 py-0.5 rounded-full" id="totalUsersBadge">0</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="settings" style="color: #E2E8F0;">
                <i class="fas fa-cog w-5" style="color: #8BC34A;"></i>
                <span class="ml-3">Settings</span>
            </a>
        </nav>

        <div class="pt-8 mt-auto px-3 pb-6">
            <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg transition hover:bg-rose-800/50 text-stone-200 hover:text-white">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span class="ml-3">Logout</span>
                </button>
            </form>
        </div>

        <!-- Admin user card -->
        <div class="p-5 m-3 rounded-xl mb-6" style="background: #6d3661; border: 1px solid #af5c9c;">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name ?? 'Admin User') }}&background=5CB8E4&color=fff&bold=true&size=40" class="w-10 h-10 rounded-full border-2 border-white" id="adminAvatarImg">
                <div class="ml-3">
                    <p class="text-sm font-semibold text-white" id="adminNameDisplay">{{ Auth::guard('admin')->user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-white/80" id="adminEmailDisplay">{{ Auth::guard('admin')->user()->email ?? 'admin@tithandizane.org' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="flex-1 overflow-y-auto custom-scroll" style="background: #F8FAFE;">
        
        <!-- Top welcome bar -->
        <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm shadow-sm border-b" style="border-color: #E2E8F0;">
            <div class="flex justify-between items-center px-8 py-5 flex-wrap gap-3">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900">Manage Mentors</h2>
                    <p class="text-sm mt-1 text-gray-700">View, edit, and manage all mentor profiles</p>
                </div>
                <div class="flex items-center gap-5">
                    <div class="relative">
                        <i class="fas fa-bell text-2xl cursor-pointer transition text-gray-600 hover:text-#874179"></i>
                        <span class="absolute -top-1 -right-2 bg-rose-500 text-white text-[10px] rounded-full px-1.5" id="notificationBadge">0</span>
                    </div>
                    <i class="fas fa-envelope text-2xl cursor-pointer text-gray-600 hover:text-#874179"></i>
                    <div class="h-8 w-px bg-gray-300"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800" id="topAdminName">{{ Auth::guard('admin')->user()->name ?? 'Admin User' }}</p>
                            <p class="text-xs text-gray-600">Lead Administrator</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name ?? 'Admin User') }}&background=874179&color=fff&size=48" class="w-11 h-11 rounded-full border-2 border-[#874179]" id="topAdminAvatar">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Stats Cards Row with Enhanced Progress Circles -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Total Mentors -->
                <div class="stat-card bg-white rounded-2xl p-6 shadow-lg text-center">
                    <p class="text-sm font-medium uppercase text-gray-500 mb-4">Total Mentors</p>

                    <div class="progress-wrapper">
                        <div class="progress-circle" id="circleTotal" style="--value: 0; --color: #22c55e;"></div>
                        
                        <div class="progress-inner">
                            <i class="fas fa-users progress-icon text-green-500"></i>
                            <span class="text-lg font-bold text-gray-800" id="statTotalMentors">0%</span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-3">All mentors</p>
                </div>

                <!-- Active Mentors -->
                <div class="stat-card bg-white rounded-2xl p-6 shadow-lg text-center">
                    <p class="text-sm font-medium uppercase text-gray-500 mb-4">Active</p>

                    <div class="progress-wrapper">
                        <div class="progress-circle" id="circleActive" style="--value: 0; --color: #3b82f6;"></div>
                        
                        <div class="progress-inner">
                            <i class="fas fa-chart-line progress-icon text-blue-500"></i>
                            <span class="text-lg font-bold text-gray-800" id="statActiveMentors">0%</span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-3">Currently available</p>
                </div>

                <!-- Pending Approval -->
                <div class="stat-card bg-white rounded-2xl p-6 shadow-lg text-center">
                    <p class="text-sm font-medium uppercase text-gray-500 mb-4">Pending</p>

                    <div class="progress-wrapper">
                        <div class="progress-circle" id="circlePending" style="--value: 0; --color: #a855f7;"></div>
                        
                        <div class="progress-inner">
                            <i class="fas fa-hourglass-half progress-icon text-purple-500"></i>
                            <span class="text-lg font-bold text-gray-800" id="statPendingMentors">0%</span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-3">Awaiting review</p>
                </div>

                <!-- Inactive Mentors -->
                <div class="stat-card bg-white rounded-2xl p-6 shadow-lg text-center">
                    <p class="text-sm font-medium uppercase text-gray-500 mb-4">Inactive</p>

                    <div class="progress-wrapper">
                        <div class="progress-circle" id="circleInactive" style="--value: 0; --color: #ec4899;"></div>
                        
                        <div class="progress-inner">
                            <i class="fas fa-ban progress-icon text-pink-500"></i>
                            <span class="text-lg font-bold text-gray-800" id="statInactiveMentors">0%</span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-3">Temporarily unavailable</p>
                </div>

            </div>

            <!-- Main Mentor List Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Header with Add Button and Search -->
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="background: linear-gradient(90deg, #F9F0F7 0%, #FFFFFF 100%);">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-hands-helping mr-2" style="color: #874179;"></i>Mentor Directory
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Manage and oversee all registered mentors</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-3 text-gray-400"></i>
                            <input type="text" id="searchInput" placeholder="Search by name, email, or expertise..." 
                                   class="pl-11 pr-4 py-2.5 w-full sm:w-80 rounded-xl border border-gray-200 focus:border-[#874179] focus:ring-2 focus:ring-[#F3E6F1] bg-white transition-all">
                        </div>
                        <a href="{{ route('admin.mentors.create') }}" class="px-5 py-2.5 rounded-xl text-white flex items-center gap-2 transition shadow-md hover:shadow-lg" style="background: #874179;">
                            <i class="fas fa-plus-circle"></i> Add New Mentor
                        </a>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-wrap gap-3 items-center">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600"><i class="fas fa-filter mr-1"></i> Filter:</span>
                        <button class="status-filter px-3 py-1.5 text-sm rounded-full transition-all active" data-status="all" style="background: #874179; color: white;">All</button>
                        <button class="status-filter px-3 py-1.5 text-sm rounded-full transition-all" data-status="active" style="background: #E2E8F0; color: #4B5563;">Active</button>
                        <button class="status-filter px-3 py-1.5 text-sm rounded-full transition-all" data-status="pending" style="background: #E2E8F0; color: #4B5563;">Pending</button>
                        <button class="status-filter px-3 py-1.5 text-sm rounded-full transition-all" data-status="inactive" style="background: #E2E8F0; color: #4B5563;">Inactive</button>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Show:</span>
                        <select id="perPageSelect" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:border-[#874179]">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Mentors Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Mentor</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Expertise</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Availability</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="mentorsTableBody" class="divide-y divide-gray-100">
                            <tr>
                                <td colspan="7" class="text-center py-12 text-gray-500">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-spinner fa-spin text-3xl" style="color: #874179;"></i>
                                        <p>Loading mentors...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-600" id="paginationInfo">
                        Showing 0 to 0 of 0 results
                    </div>
                    <div class="flex gap-2" id="paginationButtons">
                        <!-- Pagination buttons will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="modal-overlay absolute inset-0 bg-black/50" id="modalOverlay"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6 text-center">
            <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-trash-alt text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Mentor</h3>
            <p class="text-gray-600 mb-6" id="deleteModalMessage">Are you sure you want to delete this mentor? This action cannot be undone.</p>
            <div class="flex gap-3 justify-center">
                <button id="cancelDeleteBtn" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">Cancel</button>
                <button id="confirmDeleteBtn" class="px-5 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Toast notification helper
    function showToast(message, type = 'success') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.toast');
        existingToasts.forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        toast.style.backgroundColor = type === 'error' ? '#dc2626' : '#10b981';
        document.body.appendChild(toast);
        
        // Force reflow
        toast.offsetHeight;
        
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Animate number function with percentage support
    function animateNumber(element, start, end, duration = 500, isPercent = false) {
        if (!element) return;

        let current = start;
        const increment = (end - start) / (duration / 16);

        const timer = setInterval(() => {
            current += increment;

            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                clearInterval(timer);
                element.textContent = isPercent ? end + '%' : end;
                element.classList.add('number-update');
                setTimeout(() => element.classList.remove('number-update'), 300);
            } else {
                element.textContent = isPercent ? Math.round(current) + '%' : Math.round(current);
            }
        }, 16);
    }

    // State variables
    let currentPage = 1;
    let currentStatus = 'all';
    let currentSearch = '';
    let currentPerPage = 10;
    let deleteMentorId = null;
    
    // Fetch mentors with filters
    async function fetchMentors() {
        const tbody = document.getElementById('mentorsTableBody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12 text-gray-500">
                <div class="flex flex-col items-center gap-3">
                    <i class="fas fa-spinner fa-spin text-3xl" style="color: #874179;"></i>
                    <p>Loading mentors...</p>
                </div>
            </td></tr>`;
        }
        
        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: currentPerPage,
                status: currentStatus,
                search: currentSearch
            });
            
            const response = await fetch(`{{ route('admin.mentors.index') }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success !== false) {
                // Update stats with enhanced circles
                if (data.stats) {
                    const total = data.stats.total || 0;
                    const active = data.stats.active || 0;
                    const pending = data.stats.pending || 0;
                    const inactive = data.stats.inactive || 0;

                    // Convert to percentages
                    const activePercent = total ? Math.round((active / total) * 100) : 0;
                    const pendingPercent = total ? Math.round((pending / total) * 100) : 0;
                    const inactivePercent = total ? Math.round((inactive / total) * 100) : 0;

                    // Animate numbers (percent style)
                    animateNumber(document.getElementById('statTotalMentors'), 0, 100, 600, true);
                    animateNumber(document.getElementById('statActiveMentors'), 0, activePercent, 600, true);
                    animateNumber(document.getElementById('statPendingMentors'), 0, pendingPercent, 600, true);
                    animateNumber(document.getElementById('statInactiveMentors'), 0, inactivePercent, 600, true);

                    // Animate circles with delay for smooth effect
                    setTimeout(() => {
                        const circleTotal = document.getElementById('circleTotal');
                        const circleActive = document.getElementById('circleActive');
                        const circlePending = document.getElementById('circlePending');
                        const circleInactive = document.getElementById('circleInactive');
                        
                        if (circleTotal) circleTotal.style.setProperty('--value', 100);
                        if (circleActive) circleActive.style.setProperty('--value', activePercent);
                        if (circlePending) circlePending.style.setProperty('--value', pendingPercent);
                        if (circleInactive) circleInactive.style.setProperty('--value', inactivePercent);
                    }, 100);
                }
                
                renderMentorsTable(data.mentors || []);
                renderPagination(data);
            } else {
                throw new Error(data.message || 'Failed to load mentors');
            }
        } catch (error) {
            console.error('Error fetching mentors:', error);
            const tbody = document.getElementById('mentorsTableBody');
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12 text-red-500">
                    <i class="fas fa-exclamation-circle text-3xl mb-2 block"></i>
                    Error loading mentors. Please refresh the page.
                    <br>
                    <small class="text-gray-400">${error.message}</small>
                </td></tr>`;
            }
        }
    }
    
    function renderMentorsTable(mentors) {
        const tbody = document.getElementById('mentorsTableBody');
        if (!tbody) return;
        
        if (!mentors || mentors.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12 text-gray-500">
                <i class="fas fa-users-slash text-3xl mb-2 block" style="color: #874179;"></i>
                No mentors found matching your criteria.
            </td></tr>`;
            return;
        }
        
        tbody.innerHTML = mentors.map(mentor => {
            // Parse expertise
            let expertiseArray = [];
            if (mentor.expertise) {
                try {
                    expertiseArray = typeof mentor.expertise === 'string' ? JSON.parse(mentor.expertise) : mentor.expertise;
                } catch(e) {
                    expertiseArray = [];
                }
            }
            
            const photoUrl = mentor.photo ? `/storage/${mentor.photo}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=874179&color=fff&size=40`;
            
            let statusBadge = '';
            if (mentor.status === 'active') {
                statusBadge = '<span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Active</span>';
            } else if (mentor.status === 'pending') {
                statusBadge = '<span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-full font-medium">Pending</span>';
            } else {
                statusBadge = '<span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">Inactive</span>';
            }
            
            const joinDate = new Date(mentor.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            
            return `
                <tr class="hover:bg-gray-50 transition group" data-mentor-id="${mentor.id}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="${photoUrl}" class="w-10 h-10 rounded-full object-cover border border-gray-200" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=874179&color=fff&size=40'">
                            <div>
                                <p class="font-semibold text-gray-800">${escapeHtml(mentor.name)}</p>
                                <p class="text-xs text-gray-500">ID: ${mentor.id}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            ${expertiseArray.slice(0, 2).map(e => `<span class="inline-block text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-700">${escapeHtml(e)}</span>`).join('')}
                            ${expertiseArray.length > 2 ? `<span class="inline-block text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600">+${expertiseArray.length - 2}</span>` : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700">${escapeHtml(mentor.email)}</div>
                        <div class="text-xs text-gray-500">${escapeHtml(mentor.phone || 'No phone')}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 max-w-[150px]">
                        <div class="truncate" title="${escapeHtml(mentor.availability || 'Not specified')}">
                            <i class="fas fa-calendar-alt text-xs mr-1 text-gray-400"></i>
                            ${escapeHtml(mentor.availability ? (mentor.availability.length > 20 ? mentor.availability.substring(0, 20) + '...' : mentor.availability) : 'Not specified')}
                        </div>
                    </td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">${joinDate}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/admin/mentors/${mentor.id}" class="view-mentor-btn p-2 rounded-lg text-blue-600 hover:bg-blue-50 transition" data-id="${mentor.id}" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/mentors/${mentor.id}/edit" class="edit-mentor-btn p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition" data-id="${mentor.id}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="delete-mentor-btn p-2 rounded-lg text-red-600 hover:bg-red-50 transition" data-id="${mentor.id}" data-name="${escapeHtml(mentor.name)}" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        
        // Attach view button handlers (optional - they already have href)
        document.querySelectorAll('.view-mentor-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Allow default anchor behavior
                const mentorId = btn.dataset.id;
                if (mentorId) {
                    window.location.href = `/admin/mentors/${mentorId}`;
                }
            });
        });
        
        // Attach edit button handlers (optional - they already have href)
        document.querySelectorAll('.edit-mentor-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Allow default anchor behavior
                const mentorId = btn.dataset.id;
                if (mentorId) {
                    window.location.href = `/admin/mentors/${mentorId}/edit`;
                }
            });
        });
        
        // Attach delete button handlers
        document.querySelectorAll('.delete-mentor-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const mentorId = btn.dataset.id;
                const mentorName = btn.dataset.name;
                if (mentorId) {
                    deleteMentorId = mentorId;
                    const modalMessage = document.getElementById('deleteModalMessage');
                    if (modalMessage) {
                        modalMessage.innerHTML = `Are you sure you want to delete <strong>${escapeHtml(mentorName)}</strong>? This action cannot be undone.`;
                    }
                    const deleteModal = document.getElementById('deleteModal');
                    if (deleteModal) {
                        deleteModal.classList.remove('hidden');
                        deleteModal.classList.add('flex');
                    }
                }
            });
        });
    }
    
    function renderPagination(data) {
        const { current_page, last_page, total, from, to } = data;
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationContainer = document.getElementById('paginationButtons');
        
        if (paginationInfo) {
            paginationInfo.innerHTML = `Showing ${from || 0} to ${to || 0} of ${total || 0} results`;
        }
        
        if (!paginationContainer) return;
        
        if (last_page <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let buttons = '';
        
        // Previous button
        buttons += `<button class="pagination-btn px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition ${current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''}" 
                           data-page="${current_page - 1}" ${current_page === 1 ? 'disabled' : ''}>
                        <i class="fas fa-chevron-left"></i>
                    </button>`;
        
        // Page numbers
        let startPage = Math.max(1, current_page - 2);
        let endPage = Math.min(last_page, current_page + 2);
        
        if (startPage > 1) {
            buttons += `<button class="pagination-btn px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-100 transition" data-page="1">1</button>`;
            if (startPage > 2) buttons += `<span class="px-2 text-gray-500">...</span>`;
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === current_page;
            buttons += `<button class="pagination-btn px-3 py-1.5 rounded-lg transition ${isActive ? 'text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-100'}" 
                               data-page="${i}" 
                               style="${isActive ? 'background: #874179;' : ''}">
                            ${i}
                        </button>`;
        }
        
        if (endPage < last_page) {
            if (endPage < last_page - 1) buttons += `<span class="px-2 text-gray-500">...</span>`;
            buttons += `<button class="pagination-btn px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-100 transition" data-page="${last_page}">${last_page}</button>`;
        }
        
        // Next button
        buttons += `<button class="pagination-btn px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition ${current_page === last_page ? 'opacity-50 cursor-not-allowed' : ''}" 
                           data-page="${current_page + 1}" ${current_page === last_page ? 'disabled' : ''}>
                        <i class="fas fa-chevron-right"></i>
                    </button>`;
        
        paginationContainer.innerHTML = buttons;
        
        // Attach pagination event listeners
        document.querySelectorAll('.pagination-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const page = parseInt(btn.dataset.page);
                if (page && !isNaN(page) && page !== current_page && page >= 1 && page <= last_page) {
                    currentPage = page;
                    fetchMentors();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
            return c;
        });
    }
    
    // Delete mentor function
    async function deleteMentor(id) {
        if (!id) {
            showToast('Invalid mentor ID', 'error');
            return;
        }
        
        // Show loading state on delete button
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const originalText = confirmBtn?.innerHTML || 'Delete';
        if (confirmBtn) {
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Deleting...';
            confirmBtn.disabled = true;
        }
        
        try {
            const response = await fetch(`/admin/mentors/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showToast('Mentor deleted successfully!', 'success');
                // Refresh the list
                await fetchMentors();
            } else {
                showToast(data.message || 'Failed to delete mentor', 'error');
            }
        } catch (error) {
            console.error('Error deleting mentor:', error);
            showToast('An error occurred while deleting the mentor', 'error');
        } finally {
            if (confirmBtn) {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        }
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', () => {
        fetchMentors();
        
        // Search input debounce
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentSearch = e.target.value;
                    currentPage = 1;
                    fetchMentors();
                }, 300);
            });
        }
        
        // Status filters
        const filterBtns = document.querySelectorAll('.status-filter');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => {
                    b.style.background = '#E2E8F0';
                    b.style.color = '#4B5563';
                });
                btn.style.background = '#874179';
                btn.style.color = 'white';
                currentStatus = btn.dataset.status;
                currentPage = 1;
                fetchMentors();
            });
        });
        
        // Per page select
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', (e) => {
                currentPerPage = parseInt(e.target.value);
                currentPage = 1;
                fetchMentors();
            });
        }
        
        // Modal handlers
        const deleteModal = document.getElementById('deleteModal');
        const modalOverlay = document.getElementById('modalOverlay');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        function closeModal() {
            if (deleteModal) {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
            }
            deleteMentorId = null;
        }
        
        if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
        if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', closeModal);
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', async () => {
                if (deleteMentorId) {
                    await deleteMentor(deleteMentorId);
                    closeModal();
                }
            });
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && deleteModal && !deleteModal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
</body>
</html>