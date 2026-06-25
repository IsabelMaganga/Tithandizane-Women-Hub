<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Tithandizane Women Hub</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
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
        
        .sidebar-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: hidden;
        }
        
        .sidebar-nav-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }
        
        .sidebar-nav-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-nav-scroll::-webkit-scrollbar-track { background: #2c3e50; border-radius: 10px; }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb { background: var(--blue); border-radius: 10px; }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb:hover { background: var(--teal-green); }
        
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: var(--light-gray); border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: var(--dark-blue); border-radius: 10px; }
        
        .hover-scale { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-scale:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -12px rgba(0,0,0,0.15); }
        .card-shadow { box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 6px rgba(0,0,0,0.05); }
        
        .nav-item { transition: all 0.2s ease; }
        .active-nav { background: var(--blue) !important; color: #FFFFFF !important; }
        .active-nav i, .active-nav span { color: #FFFFFF !important; }

        .settings-submenu {
            margin-left: 1.5rem;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
            border-left: 2px solid rgba(255,255,255,0.1);
            padding-left: 0.5rem;
        }
        .settings-submenu .nav-item { padding: 0.5rem 0.75rem; font-size: 0.875rem; border-radius: 0.5rem; }
        .settings-submenu .nav-item.active-nav { background: var(--blue) !important; color: #FFFFFF !important; }
        .settings-submenu .nav-item.active-nav i,
        .settings-submenu .nav-item.active-nav span { color: #FFFFFF !important; }
        .settings-submenu .nav-item:not(.active-nav):hover { background: rgba(255,255,255,0.08); }
        
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        input:focus, button:focus { outline: none; box-shadow: 0 0 0 2px var(--blue); }
        .stat-card { background: var(--card-bg); transition: all 0.2s ease; }
        .empower-card { background: linear-gradient(135deg, var(--text-primary) 0%, var(--dark-blue) 100%); }
        .card-teal-border { border-left-color: var(--teal-green); }
        .card-blue-border { border-left-color: var(--blue); }
        .card-purple-border { border-left-color: var(--purple); }
        .card-orange-border { border-left-color: var(--orange); }
        .progress-bar-green { background: var(--teal-green); }
        .progress-bar-blue { background: var(--blue); }
        .progress-bar-orange { background: var(--orange); }
        .progress-bar-red { background: var(--red); }
        .badge-success { background: var(--light-teal); color: var(--teal-green); }
        .badge-warning { background: var(--light-orange); color: var(--orange); }
        .badge-danger { background: var(--light-red); color: var(--red); }
        .badge-info { background: var(--light-blue); color: var(--blue); }
        .badge-purple { background: var(--light-purple); color: var(--purple); }
        .text-success { color: var(--teal-green); }
        .text-warning { color: var(--orange); }
        .text-danger { color: var(--red); }
        .text-info { color: var(--blue); }
        .text-purple { color: var(--purple); }
        
        .theme-toggle {
            background: var(--light-gray);
            border-radius: 50px;
            padding: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .theme-toggle:hover { transform: scale(1.05); }
        
        .mentor-box { transition: all 0.3s ease; border-color: var(--border-color); }
        .mentor-box:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .mentor-box:hover .mentor-icon { transform: scale(1.1); }
        .mentor-box.view-all:hover { border-color: var(--purple); background: var(--light-purple); }
        .mentor-box.add-new:hover { border-color: var(--teal-green); background: var(--light-teal); }
        .mentor-box.pending:hover { border-color: var(--orange); background: var(--light-orange); }
        .mentor-icon { transition: transform 0.3s ease; }
        
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; }
        .status-active { background: var(--light-teal); color: var(--teal-green); }
        .status-inactive { background: var(--light-orange); color: var(--orange); }
        .status-banned { background: var(--light-red); color: var(--red); }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: var(--card-bg); border-radius: 12px; padding: 0; max-width: 500px; width: 90%; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .role-badge { background: var(--light-blue); color: var(--blue); padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; }
        .animate-slide-in { animation: slideInRight 0.3s ease; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        
        .pagination { display: flex; justify-content: center; gap: 8px; }
        .pagination .page-item { list-style: none; }
        .pagination .page-link { padding: 8px 12px; border-radius: 8px; background: var(--bg-secondary); color: var(--text-primary); text-decoration: none; transition: all 0.2s; }
        .pagination .page-item.active .page-link { background: var(--blue); color: white; }
        .pagination .page-link:hover { background: var(--light-gray); }
        .settings-card { transition: all 0.3s ease; }
        .settings-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15); }

        /* ── Notification Dropdown ─────────────────────────────────── */
        #notifDropdown {
            display: none;
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 360px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            z-index: 9999;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }
        #notifDropdown.open { display: block; animation: dropdownFadeIn 0.2s ease; }
        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .notif-item { transition: background 0.15s; }
        .notif-item:hover { background: var(--light-gray); }
        .notif-item.unread { border-left: 3px solid var(--blue); background: var(--light-blue); }
        .notif-item.unread:hover { background: #c3dcf0; }
        .notif-scroll { max-height: 360px; overflow-y: auto; }
        .notif-scroll::-webkit-scrollbar { width: 4px; }
        .notif-scroll::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <div class="w-64 flex flex-col shadow-xl sidebar-wrapper" style="background: var(--sidebar-bg); border-right: 1px solid #2c3e50;">
        
        <!-- Sidebar Header -->
        <div class="p-6 border-b flex-shrink-0" style="border-color: #2c3e50;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo2.png') }}" alt="Tithandizane Logo" class="w-12 h-12 rounded-full object-cover shadow-md border-2 border-white/30">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">Tithandizane</h1>
                    <p class="text-xs mt-1 opacity-90 text-white">Women Hub</p>
                </div>
            </div>
        </div>

        <!-- Scrollable Nav -->
        <div class="sidebar-nav-scroll">
            <nav class="mt-6 space-y-1 px-3 pb-4" id="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}" style="color: {{ request()->routeIs('admin.dashboard') ? '#FFFFFF' : 'var(--sidebar-text)' }};">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3 font-medium">Dashboard</span>
                </a>
                <a href="{{ route('admin.mentors.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.mentors.*') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                    <i class="fas fa-chalkboard-user w-5"></i>
                    <span class="ml-3">Mentors</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.reports.*') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                    <i class="fas fa-flag w-5"></i>
                    <span class="ml-3">Harassment Reports</span>
                    <span class="ml-auto text-xs font-bold px-2 py-0.5 text-white" id="pendingReportsBadge"></span>
                </a>
                <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" style="color: var(--sidebar-text);">
                    <i class="fas fa-book-open w-5"></i>
                    <span class="ml-3">Guidance Content</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                    <i class="fas fa-user-circle w-5"></i>
                    <span class="ml-3">Users</span>
                    <span class="ml-auto text-xs px-2 py-0.5 text-white" id="totalUsersBadge"></span>
                </a>
                
                @php
                    $settingsRoutes = ['admin.settings', 'admin.settings.general', 'admin.settings.admins', 'admin.settings.email', 'admin.settings.security', 'admin.settings.backup'];
                    $isSettingsActive = request()->routeIs(...$settingsRoutes);
                @endphp
                
                <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ $isSettingsActive ? 'active-nav' : '' }}" style="color: var(--sidebar-text);" onclick="toggleSettingsMenu(event)">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Settings</span>
                    <i class="fas fa-chevron-down ml-auto text-xs" id="settingsChevron"></i>
                </a>
                
                <div id="settingsSubmenu" class="settings-submenu {{ $isSettingsActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.settings.general') }}" class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.settings.general') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                        <i class="fas fa-sliders-h w-4 text-sm"></i><span class="ml-3 text-sm">General</span>
                    </a>
                    <a href="{{ route('admin.settings.admins') }}" class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.settings.admins') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                        <i class="fas fa-user-shield w-4 text-sm"></i><span class="ml-3 text-sm">Admin Users</span>
                    </a>
                    <a href="{{ route('admin.settings.email') }}" class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.settings.email') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                        <i class="fas fa-envelope w-4 text-sm"></i><span class="ml-3 text-sm">Email Templates</span>
                    </a>
                    <a href="{{ route('admin.settings.security') }}" class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.settings.security') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                        <i class="fas fa-lock w-4 text-sm"></i><span class="ml-3 text-sm">Security</span>
                    </a>
                    <a href="{{ route('admin.settings.backup') }}" class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.settings.backup') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                        <i class="fas fa-database w-4 text-sm"></i><span class="ml-3 text-sm">Backup</span>
                    </a>
                </div>
                
                <div class="mt-4 pt-2 border-t border-gray-700"></div>
                <a href="{{ route('admin.analytics.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.analytics.*') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                    <i class="fas fa-chart-line w-5"></i>
                    <span class="ml-3">Analytics Reports</span>
                </a>
                <a href="{{ route('admin.events.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.events.*') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                    <i class="fas fa-calendar-alt w-5"></i>
                    <span class="ml-3">Events Calendar</span>
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('admin.notifications.*') ? 'active-nav' : '' }}" style="color: var(--sidebar-text);">
                    <div class="relative">
                        <i class="fas fa-bell w-5"></i>
                        <span id="sidebar-notif-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"></span>
                    </div>
                    <span class="ml-3">Notifications</span>
                </a>
            </nav>
        </div>

        <!-- Admin card -->
        <div class="flex-shrink-0 p-5 m-3 rounded-xl mb-6" style="background: var(--dark-blue); border: 1px solid #34495e;">
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

        <!-- Top bar -->
        <div class="sticky top-0 z-10 backdrop-blur-sm shadow-sm border-b" style="background: var(--bg-secondary); border-color: var(--border-color);">
            <div class="flex justify-between items-center px-8 py-4 flex-wrap gap-3">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">@yield('page-title', 'Welcome back, Admin')</h2>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">@yield('page-subtitle', 'Empowering women through mentorship & safety')</p>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Dark mode toggle -->
                    <button id="themeToggle" class="theme-toggle flex items-center gap-2 px-4 py-2 rounded-lg transition" style="background: var(--light-gray); color: var(--text-primary);">
                        <i id="themeIcon" class="fas fa-moon"></i>
                        <span id="themeText" class="text-sm font-medium">Dark Mode</span>
                    </button>
                    
                    <!-- ── Bell icon with dropdown ── -->
                    <div class="relative" id="notifWrapper">
                        <button id="notifBell"
                                class="relative p-2 rounded-lg transition hover:opacity-80 focus:outline-none"
                                style="color: var(--text-primary);"
                                aria-label="Notifications">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notifBadge"
                                  class="hidden absolute -top-1 -right-1 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
                                  style="background: var(--red);"></span>
                        </button>

                        <!-- Dropdown -->
                        <div id="notifDropdown">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color: var(--border-color);">
                                <h3 class="font-bold text-sm" style="color: var(--text-primary);">Notifications</h3>
                                <div class="flex items-center gap-3">
                                    <button id="markAllReadBtn" class="text-xs font-medium transition hover:opacity-70" style="color: var(--blue);">
                                        Mark all read
                                    </button>
                                    <a href="{{ route('admin.notifications.index') }}" class="text-xs font-medium transition hover:opacity-70" style="color: var(--purple);">
                                        View all
                                    </a>
                                </div>
                            </div>

                            <!-- Notification list -->
                            <div class="notif-scroll" id="notifList">
                                <div class="text-center py-8">
                                    <i class="fas fa-spinner fa-spin text-xl" style="color: var(--purple);"></i>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-4 py-3 border-t text-center" style="border-color: var(--border-color);">
                                <a href="{{ route('admin.notifications.index') }}"
                                   class="text-sm font-semibold transition hover:opacity-70"
                                   style="color: var(--purple);">
                                    See all notifications <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <i class="fas fa-envelope text-xl cursor-pointer transition" style="color: var(--text-primary);"></i>
                    
                    <!-- Logout -->
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
                <div class="mb-4 p-4 rounded-lg flex items-center gap-3" style="background: var(--light-teal); color: var(--teal-green); border-left: 4px solid var(--teal-green);">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="ml-auto" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer;">
                        <i class="fas fa-times" style="color: var(--teal-green);"></i>
                    </button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg flex items-center gap-3" style="background: var(--light-red); color: var(--red); border-left: 4px solid var(--red);">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="ml-auto" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer;">
                        <i class="fas fa-times" style="color: var(--red);"></i>
                    </button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-4 p-4 rounded-lg" style="background: var(--light-red); color: var(--red); border-left: 4px solid var(--red);">
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
// ── Settings menu ──────────────────────────────────────────────────────────────
function toggleSettingsMenu(event) {
    event.preventDefault();
    const submenu = document.getElementById('settingsSubmenu');
    const chevron = document.getElementById('settingsChevron');
    if (submenu) {
        submenu.classList.toggle('hidden');
        if (chevron) chevron.className = submenu.classList.contains('hidden')
            ? 'fas fa-chevron-down ml-auto text-xs'
            : 'fas fa-chevron-up ml-auto text-xs';
    }
}

// ── Theme ──────────────────────────────────────────────────────────────────────
function initTheme() {
    const isDark = localStorage.getItem('theme') === 'dark';
    document.body.classList.toggle('dark-mode', isDark);
    updateThemeUI(isDark);
}
function updateThemeUI(isDark) {
    const icon = document.getElementById('themeIcon');
    const text = document.getElementById('themeText');
    if (icon) icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
    if (text) text.textContent = isDark ? 'Light Mode' : 'Dark Mode';
}
function toggleTheme() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    updateThemeUI(isDark);
}

// ── Sidebar badge loaders ──────────────────────────────────────────────────────
function loadPendingReportsCount() {
    fetch('{{ route("admin.reports.index") }}?status=pending', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(data => {
        const badge = document.getElementById('pendingReportsBadge');
        if (badge) badge.innerText = (data.stats?.pending ?? 0) > 0 ? data.stats.pending : '';
    }).catch(() => {});
}

function loadTotalUsersCount() {
    fetch('{{ route("admin.users.index") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(data => {
        const badge = document.getElementById('totalUsersBadge');
        if (badge && data.totalUsers !== undefined) badge.innerText = data.totalUsers;
    }).catch(() => {});
}

// ── Notification dropdown ──────────────────────────────────────────────────────
let notifOpen = false;

function getCSRF() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

function setNotifBadge(count) {
    const badge       = document.getElementById('notifBadge');
    const sidebarBadge = document.getElementById('sidebar-notif-badge');
    [badge, sidebarBadge].forEach(el => {
        if (!el) return;
        if (count > 0) {
            el.textContent = count > 99 ? '99+' : count;
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
}

async function loadNotificationCount() {
    try {
        const res  = await fetch('/admin/notifications/unread-count', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
        });
        const data = await res.json();
        setNotifBadge(data.count ?? 0);
    } catch (e) {}
}

async function loadNotifications() {
    const list = document.getElementById('notifList');
    if (!list) return;

    try {
        const res  = await fetch('/admin/notifications?dropdown=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
        });
        const data = await res.json();
        const notifications = data.notifications ?? data.data ?? [];

        if (!notifications.length) {
            list.innerHTML = `
                <div class="text-center py-10">
                    <i class="fas fa-bell-slash text-4xl mb-3" style="color: var(--text-secondary);"></i>
                    <p class="text-sm font-medium" style="color: var(--text-secondary);">No notifications yet</p>
                </div>`;
            return;
        }

        list.innerHTML = notifications.slice(0, 8).map(n => {
            const iconMap = {
                info:    { icon: 'fa-info-circle',       color: 'var(--blue)',       bg: 'var(--light-blue)'   },
                success: { icon: 'fa-check-circle',      color: 'var(--teal-green)', bg: 'var(--light-teal)'   },
                warning: { icon: 'fa-exclamation-circle',color: 'var(--orange)',     bg: 'var(--light-orange)' },
                danger:  { icon: 'fa-times-circle',      color: 'var(--red)',        bg: 'var(--light-red)'    },
                event:   { icon: 'fa-calendar-alt',      color: 'var(--purple)',     bg: 'var(--light-purple)' },
            };
            const style  = iconMap[n.type] ?? iconMap.info;
            const timeAgo = n.created_at_human ?? n.created_at ?? '';
            const unread  = !n.is_read;

            return `
                <div class="notif-item ${unread ? 'unread' : ''} flex items-start gap-3 px-4 py-3 cursor-pointer"
                     onclick="handleNotifClick(${n.id}, '${escapeAttr(n.data?.link ?? '')}')">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center mt-0.5"
                         style="background: ${style.bg};">
                        <i class="fas ${style.icon} text-sm" style="color: ${style.color};"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate" style="color: var(--text-primary);">${escapeHtml(n.title ?? '')}</p>
                        <p class="text-xs mt-0.5 line-clamp-2" style="color: var(--text-secondary);">${escapeHtml(n.message ?? '')}</p>
                        <p class="text-xs mt-1" style="color: var(--text-secondary); opacity: 0.7;">${escapeHtml(timeAgo)}</p>
                    </div>
                    ${unread ? `<span class="flex-shrink-0 w-2 h-2 rounded-full mt-2" style="background: var(--blue);"></span>` : ''}
                </div>`;
        }).join('');

    } catch (e) {
        const list = document.getElementById('notifList');
        if (list) list.innerHTML = `<div class="text-center py-8 text-sm" style="color:var(--text-secondary);">Failed to load notifications.</div>`;
    }
}

async function handleNotifClick(id, link) {
    // Mark as read
    try {
        await fetch(`/admin/notifications/${id}/mark-read`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
        });
    } catch (e) {}

    closeNotifDropdown();

    if (link && link !== 'undefined' && link !== '') {
        window.location.href = link;
    } else {
        window.location.href = '{{ route("admin.notifications.index") }}';
    }
}

function openNotifDropdown() {
    document.getElementById('notifDropdown').classList.add('open');
    notifOpen = true;
    loadNotifications();
}

function closeNotifDropdown() {
    document.getElementById('notifDropdown').classList.remove('open');
    notifOpen = false;
}

function toggleNotifDropdown() {
    notifOpen ? closeNotifDropdown() : openNotifDropdown();
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}
function escapeAttr(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'");
}

// ── Init ───────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    loadPendingReportsCount();
    loadTotalUsersCount();
    loadNotificationCount();

    document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);
    document.getElementById('notifBell')?.addEventListener('click', e => { e.stopPropagation(); toggleNotifDropdown(); });

    // Mark all read button inside dropdown
    document.getElementById('markAllReadBtn')?.addEventListener('click', async e => {
        e.stopPropagation();
        try {
            await fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
            });
            setNotifBadge(0);
            loadNotifications();
        } catch (err) {}
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', e => {
        const wrapper = document.getElementById('notifWrapper');
        if (notifOpen && wrapper && !wrapper.contains(e.target)) closeNotifDropdown();
    });

    // Auto-expand settings menu on settings pages
    const isSettingsPage = {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }};
    if (isSettingsPage) {
        const submenu = document.getElementById('settingsSubmenu');
        const chevron = document.getElementById('settingsChevron');
        if (submenu) submenu.classList.remove('hidden');
        if (chevron) chevron.className = 'fas fa-chevron-up ml-auto text-xs';
    }

    // Refresh notification count every 60 seconds
    setInterval(loadNotificationCount, 60000);
});
</script>

@stack('scripts')
</body>
</html>