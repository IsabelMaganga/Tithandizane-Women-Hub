<!DOCTYPE html>
<html lang="en" style="visibility:hidden">
<head>
    <!--
        Anti-flash strategy:
        1. <html> starts invisible — no layout shift, no white flash between pages
        2. Inline script injects dark CSS vars synchronously BEFORE any stylesheet loads
        3. DOMContentLoaded restores visibility — theme vars already applied, zero blink
        4. body has NO background transition so theme never animates on page load
    -->
    <script>
        (function(){
            var stored      = localStorage.getItem('theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var dark        = stored ? stored === 'dark' : prefersDark;
            if (dark) {
                document.documentElement.setAttribute('data-theme','dark');
                var s = document.createElement('style');
                s.id = 'theme-override';
                s.textContent = ':root{--bg-primary:#0f0e1a;--bg-secondary:#18172b;--text-primary:#f1f0ff;--text-secondary:#9ca3af;--card-bg:#1c1b2e;--border-color:#2d2b45;--sidebar-bg:#1e1b38;--sidebar-mid:#2d2b45;--sidebar-hover:rgba(167,139,250,0.10);--sidebar-active:rgba(167,139,250,0.15);--sidebar-text:#9ca3af;--sidebar-text-bold:#f1f0ff;--sidebar-border:#2d2b45;--sidebar-accent:#a78bfa;--light-teal:#064e3b;--light-orange:#451a03;--light-red:#450a0a;--light-purple:#2e1065;--light-blue:#1e3a5f;--light-gray:#1c1b2e;--gray-bg:#0f0e1a;}';
                document.head.appendChild(s);
            } else {
                document.documentElement.setAttribute('data-theme','light');
                var e = document.getElementById('theme-override');
                if (e) e.remove();
            }
            document.addEventListener('DOMContentLoaded', function(){ document.documentElement.style.visibility = ''; });
        })();
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/Ellipse 3.png') }}">
    <title>@yield('title', 'Admin Dashboard') - Tithandizane Women Hub</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ── CSS Variables ─────────────────────────────────────────── */
        :root {
            --sidebar-bg:        #ffffff;
            --sidebar-mid:       #f9f8ff;
            --sidebar-hover:     rgba(124,58,237,0.07);
            --sidebar-active:    rgba(124,58,237,0.12);
            --sidebar-text:      #6b7280;
            --sidebar-text-bold: #1e1b38;
            --sidebar-border:    #e5e2f0;
            --sidebar-accent:    #7c3aed;
            --bg-primary:        #f5f4f9;
            --bg-secondary:      #ffffff;
            --card-bg:           #ffffff;
            --border-color:      #e5e2f0;
            --text-primary:      #1e1b38;
            --text-secondary:    #6b7280;
            --teal-green:  #10b981;
            --green:       #059669;
            --light-teal:  #d1fae5;
            --orange:      #f59e0b;
            --light-orange:#fef3c7;
            --red:         #ef4444;
            --light-red:   #fee2e2;
            --purple:      #7c3aed;
            --light-purple:#ede9fe;
            --blue:        #3b82f6;
            --light-blue:  #dbeafe;
            --light-gray:  #f9fafb;
            --gray-bg:     #f5f4f9;
        }

        /* ── Dark mode overrides ───────────────────────────────────── */
        [data-theme="dark"] .nav-item.active-nav {
            color: var(--sidebar-accent) !important;
            box-shadow: inset 3px 0 0 var(--sidebar-accent) !important;
        }
        [data-theme="dark"] .nav-item.active-nav i { color: var(--sidebar-accent) !important; }
        [data-theme="dark"] .theme-toggle { background: var(--purple) !important; color: #fff !important; }
        [data-theme="dark"] .theme-toggle:hover { background: #6d28d9 !important; }
        [data-theme="dark"] .sidebar-admin-card { background: rgba(255,255,255,0.06) !important; }
        [data-theme="dark"] body, [data-theme="dark"] {
            --bg-primary:#0f0e1a; --bg-secondary:#18172b; --text-primary:#f1f0ff;
            --text-secondary:#9ca3af; --sidebar-bg:#1e1b38; --sidebar-mid:#2d2b45;
            --sidebar-hover:rgba(167,139,250,0.10); --sidebar-active:rgba(167,139,250,0.15);
            --sidebar-text:#9ca3af; --sidebar-text-bold:#f1f0ff; --sidebar-border:#2d2b45;
            --sidebar-accent:#a78bfa; --card-bg:#1c1b2e; --border-color:#2d2b45;
            --light-teal:#064e3b; --light-orange:#451a03; --light-red:#450a0a;
            --light-purple:#2e1065; --light-blue:#1e3a5f; --light-gray:#1c1b2e; --gray-bg:#0f0e1a;
        }

        /* ── Base ──────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        body { background: var(--bg-primary); font-family: 'Inter', system-ui, sans-serif; color: var(--text-primary); margin: 0; }

        /* ── Layout shell ──────────────────────────────────────────── */
        .app-shell { display: flex; height: 100vh; overflow: hidden; }

        /* ── Sidebar ───────────────────────────────────────────────── */
        .sidebar {
            width: 256px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            transition: transform 0.28s cubic-bezier(.4,0,.2,1);
            z-index: 200;
        }

        /* Mobile: sidebar slides in as overlay */
        @media (max-width: 1023px) {
            .sidebar {
                position: fixed;
                top: 0; left: 0; bottom: 0;
                transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0,0,0,0.12);
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-backdrop {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 199;
                backdrop-filter: blur(2px);
            }
            .sidebar-backdrop.open { display: block; }
        }

        .sidebar-logo {
            padding: 20px 16px 16px;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }
        .sidebar-nav-scroll {
            flex: 1; overflow-y: auto; overflow-x: hidden;
            padding: 8px 12px 0;
        }
        .sidebar-nav-scroll::-webkit-scrollbar { width: 3px; }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb { background: var(--purple); border-radius: 10px; }

        .nav-section-label {
            font-size: 10px; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; color: var(--sidebar-accent);
            padding: 14px 12px 5px; display: block;
        }

        .nav-item {
            display: flex; align-items: center; width: 100%;
            padding: 9px 12px; border-radius: 10px;
            color: var(--sidebar-text); font-size: 14px; font-weight: 500;
            gap: 11px; text-decoration: none; cursor: pointer;
            border: none; background: none; text-align: left;
            margin-bottom: 2px;
            transition: background .15s ease, color .15s ease;
        }
        .nav-item:hover { background: var(--sidebar-hover); color: var(--sidebar-text-bold); }
        .nav-item.active-nav {
            background: var(--sidebar-active);
            color: var(--purple) !important;
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--purple);
        }
        .nav-item.active-nav i { color: var(--purple) !important; }
        .nav-item i { width: 17px; text-align: center; flex-shrink: 0; font-size: 14px; }

        .settings-submenu {
            margin: 2px 0 4px 18px;
            padding-left: 10px;
            border-left: 1px solid var(--sidebar-border);
        }
        .settings-submenu .nav-item { padding: 7px 10px; font-size: 13px; }

        .chevron { transition: transform .2s ease; margin-left: auto; font-size: 10px; flex-shrink: 0; }
        .chevron.open { transform: rotate(180deg); }

        /* Nav count pills */
        .nav-pill {
            margin-left: auto;
            font-size: 10px; font-weight: 700;
            padding: 2px 7px; border-radius: 20px;
            background: rgba(255,255,255,0.18);
            color: var(--sidebar-text-bold);
            line-height: 1.4;
        }
        .nav-pill-alert { background: var(--red); color: white; }

        .sidebar-admin-card {
            flex-shrink: 0; margin: 10px 12px 16px;
            padding: 12px 14px; border-radius: 14px;
            background: var(--light-purple);
            border: 1px solid var(--sidebar-border);
        }

        /* ── Main area ─────────────────────────────────────────────── */
        .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

        /* ── Topbar ────────────────────────────────────────────────── */
        .topbar {
            flex-shrink: 0;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; gap: 12px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .topbar-title { font-size: 18px; font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .topbar-sub   { font-size: 11px; color: var(--text-secondary); margin-top: 1px; display: none; }
        @media (min-width: 640px) { .topbar-sub { display: block; } }

        /* Hamburger — only visible on mobile */
        .hamburger {
            display: none;
            width: 38px; height: 38px; border-radius: 10px;
            align-items: center; justify-content: center;
            color: var(--text-secondary); background: var(--light-gray);
            border: none; cursor: pointer; flex-shrink: 0;
            transition: background .15s;
        }
        .hamburger:hover { background: var(--border-color); color: var(--text-primary); }
        @media (max-width: 1023px) { .hamburger { display: flex; } }

        .topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

        .topbar-icon-btn {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary); background: var(--light-gray);
            border: none; cursor: pointer; position: relative;
            transition: background .15s, color .15s; flex-shrink: 0;
        }
        .topbar-icon-btn:hover { background: var(--border-color); color: var(--text-primary); }

        .theme-toggle {
            display: flex; align-items: center; gap: 7px;
            padding: 6px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 500;
            background: var(--light-gray); color: var(--text-primary);
            border: none; cursor: pointer; transition: background .18s;
            white-space: nowrap;
        }
        .theme-toggle:hover { background: var(--border-color); }

        /* Hide theme text on small screens */
        @media (max-width: 479px) { .theme-label { display: none; } }

        .topbar-btn-danger {
            display: flex; align-items: center; gap: 6px;
            padding: 7px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 500;
            color: var(--red); background: var(--light-red);
            border: none; cursor: pointer; transition: background .15s; white-space: nowrap;
        }
        .topbar-btn-danger:hover { background: #fca5a5; }
        /* Hide logout text on xs */
        @media (max-width: 479px) { .logout-label { display: none; } }

        /* ── Page content ──────────────────────────────────────────── */
        .page-content { flex: 1; overflow-y: auto; background: var(--bg-primary); }
        .page-content::-webkit-scrollbar { width: 6px; }
        .page-content::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }

        .page-inner { padding: 24px; }
        @media (min-width: 768px) { .page-inner { padding: 32px; } }

        /* ── Notification slide panel ──────────────────────────────── */
        #notifPanel {
            position: fixed;
            top: 0; right: 0; bottom: 0;
            width: 100%; max-width: 380px;
            display: flex; flex-direction: column;
            background: var(--card-bg);
            border-left: 1px solid var(--border-color);
            box-shadow: -4px 0 32px rgba(0,0,0,0.12);
            z-index: 999;
            transform: translateX(100%);
            transition: transform 0.28s cubic-bezier(.4,0,.2,1);
        }
        #notifPanel.open { transform: translateX(0); }

        #notifBackdrop {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.35);
            z-index: 998;
            backdrop-filter: blur(2px);
        }
        #notifBackdrop.open { display: block; }

        /* ── Flash messages ────────────────────────────────────────── */
        .flash { padding: 14px 16px; border-radius: 12px; display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .flash-success { background: var(--light-teal);   color: var(--teal-green); border-left: 4px solid var(--teal-green); }
        .flash-error   { background: var(--light-red);    color: var(--red);        border-left: 4px solid var(--red); }

        /* ── Misc cards & badges ───────────────────────────────────── */
        .card { background: var(--card-bg); border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03); }
        .hover-lift { transition: transform .2s ease, box-shadow .2s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.10); }

        .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
        .badge-success { background: var(--light-teal);   color: var(--teal-green); }
        .badge-warning { background: var(--light-orange); color: var(--orange); }
        .badge-danger  { background: var(--light-red);    color: var(--red); }
        .badge-info    { background: var(--light-blue);   color: var(--blue); }
        .badge-purple  { background: var(--light-purple); color: var(--purple); }

        .status-badge    { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; }
        .status-active   { background: var(--light-teal);   color: var(--teal-green); }
        .status-inactive { background: var(--light-orange); color: var(--orange); }
        .status-banned   { background: var(--light-red);    color: var(--red); }
        .role-badge      { background: var(--light-blue); color: var(--blue); padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; }

        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
        input:focus, button:focus { outline: none; }

        .stat-card-teal   { border-left: 4px solid var(--teal-green); }
        .stat-card-blue   { border-left: 4px solid var(--blue); }
        .stat-card-purple { border-left: 4px solid var(--purple); }
        .stat-card-orange { border-left: 4px solid var(--orange); }

        .settings-card { transition: all .3s ease; }
        .settings-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.12); }

        .animate-slide-in { animation: slideInRight .3s ease; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        .mentor-box { transition: all .3s ease; border-color: var(--border-color); }
        .mentor-box:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .mentor-box:hover .mentor-icon { transform: scale(1.1); }
        .mentor-box.view-all:hover  { border-color: var(--purple);     background: var(--light-purple); }
        .mentor-box.add-new:hover   { border-color: var(--teal-green); background: var(--light-teal); }
        .mentor-box.pending:hover   { border-color: var(--orange);     background: var(--light-orange); }
        .mentor-icon { transition: transform .3s ease; }

        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: 8px; }
        .pagination .page-item { list-style: none; }
        .pagination .page-link { padding: 8px 12px; border-radius: 8px; background: var(--bg-secondary); color: var(--text-primary); text-decoration: none; transition: all .2s; }
        .pagination .page-item.active .page-link { background: var(--purple); color: white; }
        .pagination .page-link:hover { background: var(--light-purple); }

        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-content { background: var(--card-bg); border-radius: 16px; padding: 0; max-width: 500px; width: 90%; animation: slideIn .3s ease; }
        @keyframes slideIn { from { transform: translateY(-40px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>

    @stack('styles')
</head>
<body>

@php
    $settingsRoutes  = ['admin.settings','admin.settings.general','admin.settings.admins','admin.settings.email','admin.settings.security','admin.settings.backup'];
    $isSettingsActive = request()->routeIs(...$settingsRoutes);
@endphp

<!-- ── Sidebar backdrop (mobile) ── -->
<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<!-- ── App shell ── -->
<div class="app-shell">

    <!-- ════════════ SIDEBAR ════════════ -->
    <aside class="sidebar" id="sidebar">

        <!-- Logo -->
        <div class="sidebar-logo">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('images/logo2.png') }}" alt="Logo"
                         class="w-10 h-10 rounded-xl object-cover flex-shrink-0"
                         style="border: 2px solid var(--sidebar-border);">
                    <div class="min-w-0">
                        <p class="text-sm font-bold leading-tight truncate" style="color: var(--sidebar-text-bold);">Tithandizane</p>
                        <p class="text-[11px] truncate" style="color: var(--sidebar-text);">Women Hub · Admin</p>
                    </div>
                </a>
                <!-- Close button — mobile only -->
                <button id="sidebarClose"
                        class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 ml-2"
                        style="background: var(--light-red); color: var(--red);"
                        aria-label="Close menu">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Nav -->
        <div class="sidebar-nav-scroll">
            <nav id="sidebar-nav">

                <span class="nav-section-label">Main</span>

                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active-nav' : '' }}">
                    <i class="fas fa-home"></i><span>Dashboard</span>
                </a>

                <a href="{{ route('admin.mentors.index') }}"
                   class="nav-item {{ request()->routeIs('admin.mentors.*') ? 'active-nav' : '' }}">
                    <i class="fas fa-chalkboard-user"></i><span>Mentors</span>
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active-nav' : '' }}">
                    <i class="fas fa-flag"></i><span>Harassment Reports</span>
                    <span class="nav-pill nav-pill-alert hidden" id="pendingReportsBadge"></span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-book-open"></i><span>Guidance Content</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="nav-item {{ request()->routeIs('admin.users.*') ? 'active-nav' : '' }}">
                    <i class="fas fa-user-circle"></i><span>Users</span>
                    <span class="nav-pill hidden" id="totalUsersBadge"></span>
                </a>

                <span class="nav-section-label">System</span>

                <!-- Settings accordion -->
                <button type="button"
                        class="nav-item {{ $isSettingsActive ? 'active-nav' : '' }}"
                        data-submenu="settings-sub">
                    <i class="fas fa-cog"></i><span>Settings</span>
                    <i class="fas fa-chevron-down chevron {{ $isSettingsActive ? 'open' : '' }}"></i>
                </button>
                <div id="settings-sub" class="settings-submenu {{ $isSettingsActive ? '' : 'hidden' }}">
                    <a href="{{ route('admin.settings.general') }}"
                       class="nav-item {{ request()->routeIs('admin.settings.general') ? 'active-nav' : '' }}">
                        <i class="fas fa-sliders-h"></i><span>General</span>
                    </a>
                    <a href="{{ route('admin.settings.admins') }}"
                       class="nav-item {{ request()->routeIs('admin.settings.admins') ? 'active-nav' : '' }}">
                        <i class="fas fa-user-shield"></i><span>Admin Users</span>
                    </a>
                    <a href="{{ route('admin.settings.email') }}"
                       class="nav-item {{ request()->routeIs('admin.settings.email') ? 'active-nav' : '' }}">
                        <i class="fas fa-envelope"></i><span>Email Templates</span>
                    </a>
                    <a href="{{ route('admin.settings.security') }}"
                       class="nav-item {{ request()->routeIs('admin.settings.security') ? 'active-nav' : '' }}">
                        <i class="fas fa-lock"></i><span>Security</span>
                    </a>
                    <a href="{{ route('admin.settings.backup') }}"
                       class="nav-item {{ request()->routeIs('admin.settings.backup') ? 'active-nav' : '' }}">
                        <i class="fas fa-database"></i><span>Backup</span>
                    </a>
                </div>

                <a href="{{ route('admin.analytics.index') }}"
                   class="nav-item {{ request()->routeIs('admin.analytics.*') ? 'active-nav' : '' }}">
                    <i class="fas fa-chart-line"></i><span>Analytics Reports</span>
                </a>

                <a href="{{ route('admin.events.index') }}"
                   class="nav-item {{ request()->routeIs('admin.events.*') ? 'active-nav' : '' }}">
                    <i class="fas fa-calendar-alt"></i><span>Events Calendar</span>
                </a>

                <a href="{{ route('admin.notifications.index') }}"
                   class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active-nav' : '' }}">
                    <div class="relative flex-shrink-0" style="width:17px;height:17px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-bell" style="font-size:14px;"></i>
                        <span id="sidebar-notif-badge"
                              class="hidden absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center"></span>
                    </div>
                    <span>Notifications</span>
                </a>

                <div style="height:16px;"></div>
            </nav>
        </div>

        <!-- Admin card -->
        <div class="sidebar-admin-card">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name ?? 'Admin User') }}&background=7c3aed&color=fff&bold=true&size=40"
                     class="w-9 h-9 rounded-full flex-shrink-0"
                     style="border: 2px solid var(--sidebar-border);"
                     id="adminAvatarImg"
                     alt="{{ Auth::guard('admin')->user()->name ?? 'Admin' }}">
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate" style="color: var(--text-primary);" id="adminNameDisplay">{{ Auth::guard('admin')->user()->name ?? 'Admin User' }}</p>
                    <p class="text-[11px] truncate" style="color: var(--text-secondary);" id="adminEmailDisplay">{{ Auth::guard('admin')->user()->email ?? 'admin@tithandizane.org' }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- ════════════ MAIN AREA ════════════ -->
    <div class="main-area">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <!-- Hamburger (mobile only) -->
                <button class="hamburger" id="hamburgerBtn" aria-label="Open menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="min-w-0">
                    <p class="topbar-title">@yield('page-title', 'Welcome back, Admin')</p>
                    <p class="topbar-sub">@yield('page-subtitle', 'Empowering women through mentorship &amp; safety')</p>
                </div>
            </div>

            <div class="topbar-right">
                <!-- Theme toggle -->
                <button id="themeToggle" class="theme-toggle" aria-label="Toggle theme">
                    <i id="themeIcon" class="fas fa-moon text-sm"></i>
                    <span id="themeText" class="theme-label">Dark Mode</span>
                </button>

                <!-- Notification bell -->
                <button id="bellBtn" class="topbar-icon-btn" aria-label="Notifications">
                    <i class="fas fa-bell" style="font-size:15px;"></i>
                    <span id="notifBadge"
                          class="hidden"
                          style="position:absolute;top:-4px;right:-4px;background:var(--red);color:#fff;font-size:10px;font-weight:700;border-radius:999px;min-width:17px;height:17px;display:none;align-items:center;justify-content:center;padding:0 3px;"></span>
                </button>

                <!-- Mail icon -->
                <button class="topbar-icon-btn" aria-label="Messages">
                    <i class="fas fa-envelope" style="font-size:15px;"></i>
                </button>

                <!-- Divider -->
                <div style="width:1px;height:24px;background:var(--border-color);flex-shrink:0;"></div>

                <!-- Admin name -->
                <span class="text-sm font-medium" style="color: var(--text-primary); display:none;" class="admin-name-label"
                      id="topbarAdminName">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>

                <!-- Logout -->
                <form method="POST" action="{{ route('admin.logout') }}" class="inline" id="admin-logout-form">
                    @csrf
                    <button type="submit" class="topbar-btn-danger" aria-label="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="logout-label">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page content -->
        <main class="page-content custom-scroll">
            <div class="page-inner">

                @if(session('success'))
                    <div class="flash flash-success">
                        <i class="fas fa-check-circle" style="font-size:18px;flex-shrink:0;"></i>
                        <span style="font-size:14px;font-weight:500;flex:1;">{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;opacity:.6;" aria-label="Dismiss">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flash flash-error">
                        <i class="fas fa-exclamation-triangle" style="font-size:18px;flex-shrink:0;"></i>
                        <span style="font-size:14px;font-weight:500;flex:1;">{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;opacity:.6;" aria-label="Dismiss">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="flash flash-error" style="display:block;">
                        <ul style="list-style:disc;padding-left:18px;margin:0;">
                            @foreach($errors->all() as $error)
                                <li style="font-size:13px;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>

<!-- ════════════ NOTIFICATION PANEL (appended to body, outside all overflow) ════════════ -->
<div id="notifBackdrop"></div>

<div id="notifPanel" role="dialog" aria-label="Notifications" aria-modal="true">
    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px;border-bottom:1px solid var(--border-color);flex-shrink:0;">
        <p style="font-size:14px;font-weight:700;color:var(--text-primary);">Notifications</p>
        <div style="display:flex;align-items:center;gap:12px;">
            <button id="markAllReadBtn"
                    style="font-size:12px;font-weight:500;color:var(--blue);background:none;border:none;cursor:pointer;padding:0;">
                Mark all read
            </button>
            <a href="{{ route('admin.notifications.index') }}"
               style="font-size:12px;font-weight:500;color:var(--purple);">View all</a>
            <button id="notifClose"
                    style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:var(--light-red);color:var(--red);border:none;cursor:pointer;"
                    aria-label="Close notifications">
                <i class="fas fa-times" style="font-size:13px;"></i>
            </button>
        </div>
    </div>

    <!-- List -->
    <div style="flex:1;overflow-y:auto;padding:12px;" id="notifList">
        <div style="text-align:center;padding:48px 0;">
            <i class="fas fa-spinner fa-spin" style="font-size:24px;color:var(--purple);"></i>
        </div>
    </div>

    <!-- Footer -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border-color);flex-shrink:0;">
        <a href="{{ route('admin.notifications.index') }}"
           style="font-size:12px;font-weight:600;color:var(--purple);">
            See all notifications <i class="fas fa-arrow-right" style="font-size:10px;margin-left:4px;"></i>
        </a>
        <p style="font-size:11px;color:var(--text-secondary);">info@Tithandizane.com</p>
    </div>
</div>

<script>
// ── Theme ─────────────────────────────────────────────────────────────────────
function updateThemeUI(isDark) {
    var icon = document.getElementById('themeIcon');
    var text = document.getElementById('themeText');
    if (icon) icon.className = isDark ? 'fas fa-sun text-sm' : 'fas fa-moon text-sm';
    if (text) text.textContent = isDark ? 'Light Mode' : 'Dark Mode';
}
function applyTheme(dark) {
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    var el = document.getElementById('theme-override');
    if (dark) {
        if (!el) { el = document.createElement('style'); el.id = 'theme-override'; document.head.appendChild(el); }
        el.textContent = ':root{--bg-primary:#0f0e1a;--bg-secondary:#18172b;--text-primary:#f1f0ff;--text-secondary:#9ca3af;--card-bg:#1c1b2e;--border-color:#2d2b45;--sidebar-bg:#1e1b38;--sidebar-mid:#2d2b45;--sidebar-hover:rgba(167,139,250,0.10);--sidebar-active:rgba(167,139,250,0.15);--sidebar-text:#9ca3af;--sidebar-text-bold:#f1f0ff;--sidebar-border:#2d2b45;--sidebar-accent:#a78bfa;--light-teal:#064e3b;--light-orange:#451a03;--light-red:#450a0a;--light-purple:#2e1065;--light-blue:#1e3a5f;--light-gray:#1c1b2e;--gray-bg:#0f0e1a;}';
    } else {
        if (el) el.textContent = '';
    }
    updateThemeUI(dark);
}
function toggleTheme() {
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    localStorage.setItem('theme', isDark ? 'light' : 'dark');
    document.body.style.transition = 'background .25s ease';
    applyTheme(!isDark);
    setTimeout(function(){ document.body.style.transition = ''; }, 300);
}
function initTheme() {
    updateThemeUI(document.documentElement.getAttribute('data-theme') === 'dark');
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e){
            if (!localStorage.getItem('theme')) applyTheme(e.matches);
        });
    }
}

// ── Sidebar (hamburger / mobile) ──────────────────────────────────────────────
function initSidebar() {
    var sidebar  = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    var openBtn  = document.getElementById('hamburgerBtn');
    var closeBtn = document.getElementById('sidebarClose');

    function openSidebar()  { sidebar.classList.add('open'); backdrop.classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('open'); backdrop.classList.remove('open'); document.body.style.overflow = ''; }

    if (openBtn)  openBtn.addEventListener('click',  openSidebar);
    if (closeBtn) closeBtn.addEventListener('click',  closeSidebar);
    if (backdrop) backdrop.addEventListener('click',  closeSidebar);
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeSidebar(); });
}

// ── Accordion submenus ────────────────────────────────────────────────────────
function initAccordions() {
    document.querySelectorAll('[data-submenu]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var target  = document.getElementById(btn.dataset.submenu);
            var chevron = btn.querySelector('.chevron');
            if (!target) return;
            var hidden = target.classList.toggle('hidden');
            if (chevron) chevron.classList.toggle('open', !hidden);
        });
    });
}

// ── Notification panel ────────────────────────────────────────────────────────
var notifIsOpen = false;

function setNotifBadge(count) {
    var badge        = document.getElementById('notifBadge');
    var sidebarBadge = document.getElementById('sidebar-notif-badge');
    [badge, sidebarBadge].forEach(function(el) {
        if (!el) return;
        if (count > 0) {
            el.textContent = count > 99 ? '99+' : count;
            el.classList.remove('hidden');
            el.style.display = 'flex';
        } else {
            el.classList.add('hidden');
            el.style.display = 'none';
        }
    });
}

function getCSRF() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; });
}
function escapeAttr(str) { if (!str) return ''; return String(str).replace(/'/g, "\\'"); }

async function loadNotificationCount() {
    try {
        var res  = await fetch('/admin/notifications/unread-count', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
        });
        var data = await res.json();
        setNotifBadge(data.count ?? 0);
    } catch (e) {}
}

async function loadNotifications() {
    var list = document.getElementById('notifList');
    if (!list) return;
    list.innerHTML = '<div style="text-align:center;padding:48px 0;"><i class="fas fa-spinner fa-spin" style="font-size:24px;color:var(--purple);"></i></div>';
    try {
        var res  = await fetch('/admin/notifications?dropdown=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
        });
        var data = await res.json();
        var notifications = data.notifications ?? data.data ?? [];

        if (!notifications.length) {
            list.innerHTML = '<div style="text-align:center;padding:48px 0;"><i class="fas fa-bell-slash" style="font-size:32px;color:var(--text-secondary);opacity:.35;"></i><p style="font-size:13px;color:var(--text-secondary);margin-top:10px;">No notifications</p></div>';
            return;
        }

        var iconMap = {
            info:    { icon: 'fa-info-circle',        color: 'var(--blue)',       bg: 'var(--light-blue)'   },
            success: { icon: 'fa-check-circle',       color: 'var(--teal-green)', bg: 'var(--light-teal)'   },
            warning: { icon: 'fa-exclamation-circle', color: 'var(--orange)',     bg: 'var(--light-orange)' },
            danger:  { icon: 'fa-times-circle',       color: 'var(--red)',        bg: 'var(--light-red)'    },
            event:   { icon: 'fa-calendar-alt',       color: 'var(--purple)',     bg: 'var(--light-purple)' },
        };

        list.innerHTML = notifications.slice(0, 10).map(function(n) {
            var style  = iconMap[n.type] ?? iconMap.info;
            var unread = !n.is_read;
            var time   = n.created_at_human ?? n.created_at ?? '';
            var link   = escapeAttr(n.data?.link ?? '');
            return '<div style="padding:12px;border-radius:12px;background:' + (unread ? 'var(--light-purple)' : 'var(--card-bg)') + ';border:1px solid var(--border-color);margin-bottom:8px;cursor:pointer;' + (unread ? 'border-left:3px solid var(--purple);' : '') + '" onclick="handleNotifClick(' + n.id + ', \'' + link + '\')">'
                + '<div style="display:flex;align-items:flex-start;gap:10px;">'
                + '<div style="flex-shrink:0;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:' + style.bg + ';">'
                + '<i class="fas ' + style.icon + '" style="color:' + style.color + ';font-size:13px;"></i></div>'
                + '<div style="flex:1;min-width:0;">'
                + '<p style="font-size:13px;font-weight:600;color:var(--text-primary);margin:0 0 2px;">' + escapeHtml(n.title ?? '') + '</p>'
                + '<p style="font-size:12px;color:var(--text-secondary);margin:0 0 4px;">' + escapeHtml(n.message ?? '') + '</p>'
                + '<p style="font-size:11px;color:var(--text-secondary);opacity:.65;margin:0;">' + escapeHtml(time) + '</p>'
                + '</div>'
                + (unread ? '<span style="flex-shrink:0;width:8px;height:8px;border-radius:50%;background:var(--purple);margin-top:4px;"></span>' : '')
                + '</div></div>';
        }).join('');
    } catch (e) {
        if (list) list.innerHTML = '<div style="text-align:center;padding:32px;font-size:13px;color:var(--text-secondary);">Failed to load notifications.</div>';
    }
}

async function handleNotifClick(id, link) {
    try {
        await fetch('/admin/notifications/' + id + '/mark-read', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
        });
    } catch (e) {}
    closeNotifPanel();
    window.location.href = (link && link !== 'undefined' && link !== '') ? link : '{{ route("admin.notifications.index") }}';
}

function openNotifPanel()  {
    var panel    = document.getElementById('notifPanel');
    var backdrop = document.getElementById('notifBackdrop');
    if (!panel) return;
    panel.classList.add('open');
    backdrop.classList.add('open');
    notifIsOpen = true;
    loadNotifications();
}
function closeNotifPanel() {
    var panel    = document.getElementById('notifPanel');
    var backdrop = document.getElementById('notifBackdrop');
    if (!panel) return;
    panel.classList.remove('open');
    backdrop.classList.remove('open');
    notifIsOpen = false;
}

function initNotifications() {
    var bellBtn  = document.getElementById('bellBtn');
    var closeBtn = document.getElementById('notifClose');
    var backdrop = document.getElementById('notifBackdrop');

    if (bellBtn)  bellBtn.addEventListener('click',  function(e){ e.stopPropagation(); notifIsOpen ? closeNotifPanel() : openNotifPanel(); });
    if (closeBtn) closeBtn.addEventListener('click',  function(e){ e.stopPropagation(); closeNotifPanel(); });
    if (backdrop) backdrop.addEventListener('click',  closeNotifPanel);
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && notifIsOpen) closeNotifPanel(); });

    var markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', async function(e) {
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
    }
}

// ── Sidebar badge loaders ─────────────────────────────────────────────────────
function loadPendingReportsCount() {
    fetch('{{ route("admin.reports.index") }}?status=pending', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
    }).then(function(r){ return r.json(); }).then(function(data) {
        var badge = document.getElementById('pendingReportsBadge');
        if (!badge) return;
        var count = (data.stats && data.stats.pending) ? data.stats.pending : 0;
        if (count > 0) { badge.textContent = count; badge.classList.remove('hidden'); }
    }).catch(function(){});
}

function loadTotalUsersCount() {
    fetch('{{ route("admin.users.index") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCSRF() }
    }).then(function(r){ return r.json(); }).then(function(data) {
        var badge = document.getElementById('totalUsersBadge');
        if (badge && data.totalUsers !== undefined) {
            badge.textContent = data.totalUsers;
            badge.classList.remove('hidden');
        }
    }).catch(function(){});
}

// ── Show admin name on medium screens ─────────────────────────────────────────
function updateAdminNameVisibility() {
    var el = document.getElementById('topbarAdminName');
    if (!el) return;
    el.style.display = window.innerWidth >= 640 ? 'inline' : 'none';
}

// ── Boot ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initSidebar();
    initAccordions();
    initNotifications();
    loadPendingReportsCount();
    loadTotalUsersCount();
    loadNotificationCount();
    updateAdminNameVisibility();
    window.addEventListener('resize', updateAdminNameVisibility);

    var tt = document.getElementById('themeToggle');
    if (tt) tt.addEventListener('click', toggleTheme);

    // Refresh notification count every 60s
    setInterval(loadNotificationCount, 60000);
});
</script>

@stack('scripts')
</body>
</html>