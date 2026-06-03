<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Tithandizane Women Hub</title>
    
    <!-- Tailwind + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js CDN for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        /* Light Mode Variables (Default) */
        :root {
            --bg-primary: #f0f2f5;
            --bg-secondary: #ffffff;
            --text-primary: #1a2a3a;
            --text-secondary: #7f8c8d;
            --sidebar-bg: #1a2a3a;
            --sidebar-text: #E2E8F0;
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
            --teal-green: #2ecc71;
            --green: #27ae60;
            --light-teal: #d5f5e3;
            --orange: #f39c12;
            --light-orange: #fdebd0;
            --red: #e74c3c;
            --light-red: #fadbd8;
            --purple: #9b59b6;
            --light-purple: #e8daef;
            --blue: #3498db;
            --light-blue: #d6eaf8;
            --dark-blue: #2c3e50;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --gray-bg: #f0f2f5;
        }
        
        /* Dark Mode Variables */
        body.dark-mode {
            --bg-primary: #1a1a2e;
            --bg-secondary: #16213e;
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --sidebar-bg: #0f0f1a;
            --sidebar-text: #cbd5e0;
            --card-bg: #1e293b;
            --border-color: #2d3748;
            --light-teal: #1a3a2a;
            --light-orange: #3a2a1a;
            --light-red: #3a1a1a;
            --light-purple: #2a1a3a;
            --light-blue: #1a2a3a;
            --light-gray: #1a202c;
            --gray-bg: #0f172a;
        }
        
        body {
            background: var(--bg-primary);
            font-family: system-ui, 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            transition: background 0.3s ease, color 0.3s ease;
        }
        
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: var(--light-gray); border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: var(--dark-blue); border-radius: 10px; }
        
        .hover-scale { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-scale:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -12px rgba(0,0,0,0.15); }
        
        .card-shadow { box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 6px rgba(0,0,0,0.05); }
        
        .nav-item {
            transition: all 0.2s ease;
        }
        
        /* ACTIVE NAVIGATION STYLES - ADD THIS */
        .active-nav {
            background: var(--blue) !important;
            color: #FFFFFF !important;
        }
        
        .active-nav i,
        .active-nav span {
            color: #FFFFFF !important;
        }
        
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        
        input:focus, button:focus { outline: none; box-shadow: 0 0 0 2px var(--blue); }
        
        .stat-card { background: var(--card-bg); transition: all 0.2s ease; }
        
        .empower-card { background: linear-gradient(135deg, var(--text-primary) 0%, var(--dark-blue) 100%); }
        
        /* Card borders */
        .card-teal-border { border-left-color: var(--teal-green); }
        .card-blue-border { border-left-color: var(--blue); }
        .card-purple-border { border-left-color: var(--purple); }
        .card-orange-border { border-left-color: var(--orange); }
        
        /* Progress bar colors */
        .progress-bar-green { background: var(--teal-green); }
        .progress-bar-blue { background: var(--blue); }
        .progress-bar-orange { background: var(--orange); }
        .progress-bar-red { background: var(--red); }
        
        /* Badge colors */
        .badge-success { background: var(--light-teal); color: var(--teal-green); }
        .badge-warning { background: var(--light-orange); color: var(--orange); }
        .badge-danger { background: var(--light-red); color: var(--red); }
        .badge-info { background: var(--light-blue); color: var(--blue); }
        .badge-purple { background: var(--light-purple); color: var(--purple); }
        
        /* KPI Text Colors */
        .text-success { color: var(--teal-green); }
        .text-warning { color: var(--orange); }
        .text-danger { color: var(--red); }
        .text-info { color: var(--blue); }
        .text-purple { color: var(--purple); }
        
        /* Dark mode toggle button */
        .theme-toggle {
            background: var(--light-gray);
            border-radius: 50px;
            padding: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            transform: scale(1.05);
        }
        
        /* Hover effects for mentor management boxes */
        .mentor-box {
            transition: all 0.3s ease;
            border-color: var(--border-color);
        }
        
        .mentor-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        
        .mentor-box:hover .mentor-icon {
            transform: scale(1.1);
        }
        
        .mentor-box.view-all:hover {
            border-color: var(--purple);
            background: var(--light-purple);
        }
        
        .mentor-box.add-new:hover {
            border-color: var(--teal-green);
            background: var(--light-teal);
        }
        
        .mentor-box.pending:hover {
            border-color: var(--orange);
            background: var(--light-orange);
        }
        
        .mentor-icon {
            transition: transform 0.3s ease;
        }
        
        /* Additional styles for user management */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-active {
            background: var(--light-teal);
            color: var(--teal-green);
        }
        
        .status-inactive {
            background: var(--light-orange);
            color: var(--orange);
        }
        
        .status-banned {
            background: var(--light-red);
            color: var(--red);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 0;
            max-width: 500px;
            width: 90%;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .role-badge {
            background: var(--light-blue);
            color: var(--blue);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .animate-slide-in {
            animation: slideInRight 0.3s ease;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
        }
        
        .pagination .page-item {
            list-style: none;
        }
        
        .pagination .page-link {
            padding: 8px 12px;
            border-radius: 8px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .pagination .page-item.active .page-link {
            background: var(--blue);
            color: white;
        }
        
        .pagination .page-link:hover {
            background: var(--light-gray);
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <div class="w-64 flex flex-col shadow-xl" style="background: var(--sidebar-bg); border-right: 1px solid #2c3e50;">
        <div class="p-6 border-b" style="border-color: #2c3e50;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo2.png') }}" alt="Tithandizane Logo" class="w-12 h-12 rounded-full object-cover shadow-md border-2 border-white/30">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">Tithandizane</h1>
                    <p class="text-xs mt-1 opacity-90 text-white">Women Hub</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 mt-6 space-y-1 px-3" id="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}" data-page="dashboard" style="color: {{ request()->routeIs('admin.dashboard') ? '#FFFFFF' : 'var(--sidebar-text)' }};">
                <i class="fas fa-home w-5"></i>
                <span class="ml-3 font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.mentors.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.mentors.*') ? 'active-nav' : '' }}" data-page="mentors" style="color: var(--sidebar-text);">
                <i class="fas fa-chalkboard-user w-5"></i>
                <span class="ml-3">Mentors</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.reports.*') ? 'active-nav' : '' }}" data-page="reports" style="color: var(--sidebar-text);">
                <i class="fas fa-flag w-5"></i>
                <span class="ml-3">Harassment Reports</span>
                <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full" style="background: var(--red); color: white;" id="pendingReportsBadge">0</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="guidance" style="color: var(--sidebar-text);">
                <i class="fas fa-book-open w-5"></i>
                <span class="ml-3">Guidance Content</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'active-nav' : '' }}" data-page="users" style="color: var(--sidebar-text);">
                <i class="fas fa-user-circle w-5"></i>
                <span class="ml-3">Users</span>
                <span class="ml-auto text-xs px-2 py-0.5 rounded-full" style="background: var(--dark-blue); color: white;" id="totalUsersBadge">0</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.settings') ? 'active-nav' : '' }}" data-page="settings" style="color: var(--sidebar-text);">
                <i class="fas fa-cog w-5"></i>
                <span class="ml-3">Settings</span>
            </a>
        </nav>

        <!-- Admin user card - stays at bottom -->
        <div class="p-5 m-3 rounded-xl mb-6" style="background: var(--dark-blue); border: 1px solid #34495e;">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name ?? 'Admin User') }}&background=3498db&color=fff&bold=true&size=40" class="w-10 h-10 rounded-full border-2 border-white" id="adminAvatarImg">
                <div class="ml-3">
                    <p class="text-sm font-semibold text-white" id="adminNameDisplay">{{ Auth::guard('admin')->user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-white/80" id="adminEmailDisplay">{{ Auth::guard('admin')->user()->email ?? 'admin@tithandizane.org' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto custom-scroll" style="background: var(--bg-primary);">

        <!-- Top welcome bar with logout and dark mode toggle -->
        <div class="sticky top-0 z-10 backdrop-blur-sm shadow-sm border-b" style="background: var(--bg-secondary); border-color: var(--border-color);">
            <div class="flex justify-between items-center px-8 py-4 flex-wrap gap-3">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">@yield('page-title', 'Welcome back, Admin')</h2>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">@yield('page-subtitle', 'Empowering women through mentorship & safety — Here\'s your live snapshot')</p>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Dark/Light Mode Toggle Button -->
                    <button id="themeToggle" class="theme-toggle flex items-center gap-2 px-4 py-2 rounded-lg transition" style="background: var(--light-gray); color: var(--text-primary);">
                        <i id="themeIcon" class="fas fa-moon"></i>
                        <span id="themeText" class="text-sm font-medium">Dark Mode</span>
                    </button>
                    
                    <div class="relative">
                        <i class="fas fa-bell text-xl cursor-pointer transition" style="color: var(--text-primary);"></i>
                        <span class="absolute -top-1 -right-2 text-white text-[10px] rounded-full px-1.5" style="background: var(--red);" id="notificationBadge">0</span>
                    </div>
                    <i class="fas fa-envelope text-xl cursor-pointer transition" style="color: var(--text-primary);"></i>
                    
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-lg transition" style="background: var(--light-red); color: var(--red);">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-8">
            @if(session('success'))
                <div class="alert-success mb-4 p-4 rounded-lg flex items-center gap-3" style="background: var(--light-teal); color: var(--teal-green); border-left: 4px solid var(--teal-green);">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="ml-auto" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer;">
                        <i class="fas fa-times" style="color: var(--teal-green);"></i>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert-danger mb-4 p-4 rounded-lg flex items-center gap-3" style="background: var(--light-red); color: var(--red); border-left: 4px solid var(--red);">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="ml-auto" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer;">
                        <i class="fas fa-times" style="color: var(--red);"></i>
                    </button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert-danger mb-4 p-4 rounded-lg" style="background: var(--light-red); color: var(--red); border-left: 4px solid var(--red);">
                    <ul class="mb-0 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
</div>

<script>
    // Dark/Light Mode Toggle with localStorage persistence
    function initTheme() {
        const savedTheme = localStorage.getItem('theme');
        const body = document.body;
        
        if (savedTheme === 'dark') {
            body.classList.add('dark-mode');
            updateThemeUI(true);
        } else {
            body.classList.remove('dark-mode');
            updateThemeUI(false);
        }
    }
    
    function updateThemeUI(isDark) {
        const themeIcon = document.getElementById('themeIcon');
        const themeText = document.getElementById('themeText');
        
        if (themeIcon && themeText) {
            if (isDark) {
                themeIcon.className = 'fas fa-sun';
                themeText.textContent = 'Light Mode';
            } else {
                themeIcon.className = 'fas fa-moon';
                themeText.textContent = 'Dark Mode';
            }
        }
    }
    
    function toggleTheme() {
        const body = document.body;
        const isDark = body.classList.contains('dark-mode');
        
        if (isDark) {
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
            updateThemeUI(false);
        } else {
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
            updateThemeUI(true);
        }
    }
    
    // Load pending reports count for badge
    function loadPendingReportsCount() {
        const url = '{{ route("admin.reports.index") }}';
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.reports && Array.isArray(data.reports)) {
                const pendingReports = data.reports.filter(r => r.status === 'new' || r.status === 'in_review').length;
                const badge = document.getElementById('pendingReportsBadge');
                if (badge) badge.innerText = pendingReports;
            }
        })
        .catch(error => console.error('Error loading reports:', error));
    }
    
    // Load total users count
    function loadTotalUsersCount() {
        const url = '{{ route("admin.users.index") }}';
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const totalUsersBadge = document.getElementById('totalUsersBadge');
            if (totalUsersBadge && data.totalUsers !== undefined) {
                totalUsersBadge.innerText = data.totalUsers;
            }
        })
        .catch(error => console.error('Error loading users count:', error));
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        initTheme();
        loadPendingReportsCount();
        loadTotalUsersCount();
        
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', toggleTheme);
        }
    });
</script>

@stack('scripts')
</body>
</html>