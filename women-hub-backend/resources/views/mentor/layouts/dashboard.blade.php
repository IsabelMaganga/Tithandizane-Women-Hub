<!DOCTYPE html>
<html lang="en" style="visibility:hidden">
<head>
    <script>
        (function(){
            var stored      = localStorage.getItem('theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var dark        = stored ? stored === 'dark' : prefersDark;
            if (dark) {
                document.documentElement.setAttribute('data-theme','dark');
                var s = document.createElement('style');
                s.id = 'theme-override';
                s.textContent = ':root{--bg-primary:#0f0e1a;--bg-secondary:#18172b;--text-primary:#f1f0ff;--text-secondary:#9ca3af;--card-bg:#1c1b2e;--border-color:#2d2b45;--sidebar-bg:#1e1b38;--sidebar-hover:rgba(167,139,250,0.10);--sidebar-active:rgba(167,139,250,0.15);--sidebar-text:#9ca3af;--sidebar-text-bold:#f1f0ff;--sidebar-border:#2d2b45;--sidebar-accent:#a78bfa;--light-teal:#064e3b;--light-orange:#451a03;--light-red:#450a0a;--light-purple:#2e1065;--light-blue:#1e3a5f;--light-gray:#1c1b2e;--gray-bg:#0f0e1a;}';
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
    <title>@yield('title', 'Mentor Dashboard') - Tithandizane Women Hub</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* ── CSS Variables ─────────────────────────────────────────── */
        :root {
            --sidebar-bg:        #ffffff;
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
            --text-secondary:#9ca3af; --sidebar-bg:#1e1b38; --sidebar-hover:rgba(167,139,250,0.10);
            --sidebar-active:rgba(167,139,250,0.15); --sidebar-text:#9ca3af;
            --sidebar-text-bold:#f1f0ff; --sidebar-border:#2d2b45; --sidebar-accent:#a78bfa;
            --card-bg:#1c1b2e; --border-color:#2d2b45; --light-teal:#064e3b;
            --light-orange:#451a03; --light-red:#450a0a; --light-purple:#2e1065;
            --light-blue:#1e3a5f; --light-gray:#1c1b2e;
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

        .nav-submenu {
            margin: 2px 0 4px 18px;
            padding-left: 10px;
            border-left: 1px solid var(--sidebar-border);
        }
        .nav-submenu .nav-item { padding: 7px 10px; font-size: 13px; }

        .chevron { transition: transform .2s ease; margin-left: auto; font-size: 10px; flex-shrink: 0; }
        .chevron.open { transform: rotate(180deg); }

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

        /* Hide mentor name on xs screens */
        .mentor-name-label { font-size: 13px; font-weight: 500; color: var(--text-primary); }
        @media (max-width: 639px) { .mentor-name-label { display: none; } }

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
        /* Panel is placed OUTSIDE all overflow containers — appended to body */
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

        /* ── Misc ──────────────────────────────────────────────────── */
        .card { background: var(--card-bg); border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
        .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
        .badge-success { background: var(--light-teal);   color: var(--teal-green); }
        .badge-warning { background: var(--light-orange); color: var(--orange); }
        .badge-danger  { background: var(--light-red);    color: var(--red); }
        .badge-info    { background: var(--light-blue);   color: var(--blue); }
        .badge-purple  { background: var(--light-purple); color: var(--purple); }
        .line-clamp-2  { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>

    @stack('styles')
</head>
<body>

@php
    $isGeneralOpen  = request()->routeIs('mentor.appointment') || request()->routeIs('mentor.calender');
    $isChatOpen     = request()->routeIs('mentor.chat') || request()->routeIs('mentor.group') || request()->routeIs('mentor.groups');
    $isGuidanceOpen = request()->routeIs('mentor.hygiene') || request()->routeIs('mentor.general') || request()->routeIs('mentor.emergency');
    $isSettingsOpen = request()->routeIs('mentor.profile') || request()->routeIs('mentor.notifications') || request()->routeIs('mentor.settings');
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
                <a href="{{ route('mentor.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('images/logo2.png') }}" alt="Logo"
                         class="w-10 h-10 rounded-xl object-cover flex-shrink-0"
                         style="border: 2px solid var(--sidebar-border);">
                    <div class="min-w-0">
                        <p class="text-sm font-bold leading-tight truncate" style="color: var(--sidebar-text-bold);">Tithandizane</p>
                        <p class="text-[11px] truncate" style="color: var(--sidebar-text);">Women Hub · Mentor</p>
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
            <nav>
                <span class="nav-section-label">Main</span>

                <a href="{{ route('mentor.dashboard') }}"
                   class="nav-item {{ request()->routeIs('mentor.dashboard') ? 'active-nav' : '' }}">
                    <i class="fas fa-home"></i><span>Dashboard</span>
                </a>

                <!-- General -->
                <button type="button" class="nav-item {{ $isGeneralOpen ? 'active-nav' : '' }}" data-submenu="general-sub">
                    <i class="fas fa-calendar-days"></i><span>General</span>
                    <i class="fas fa-chevron-down chevron {{ $isGeneralOpen ? 'open' : '' }}"></i>
                </button>
                <div id="general-sub" class="nav-submenu {{ $isGeneralOpen ? '' : 'hidden' }}">
                    <a href="{{ route('mentor.appointment') }}" class="nav-item {{ request()->routeIs('mentor.appointment') ? 'active-nav' : '' }}">
                        <i class="fas fa-calendar"></i><span>Appointments</span>
                    </a>
                    <a href="{{ route('mentor.calender') }}" class="nav-item {{ request()->routeIs('mentor.calender') ? 'active-nav' : '' }}">
                        <i class="far fa-calendar"></i><span>Calendar</span>
                    </a>
                </div>

                <!-- Chats -->
                <button type="button" class="nav-item {{ $isChatOpen ? 'active-nav' : '' }}" data-submenu="chat-sub">
                    <i class="far fa-comment"></i><span>Chats</span>
                    <i class="fas fa-chevron-down chevron {{ $isChatOpen ? 'open' : '' }}"></i>
                </button>
                <div id="chat-sub" class="nav-submenu {{ $isChatOpen ? '' : 'hidden' }}">
                    <a href="{{ route('mentor.chat') }}" class="nav-item {{ request()->routeIs('mentor.chat') ? 'active-nav' : '' }}">
                        <i class="far fa-comment"></i><span>Mentorship Sessions</span>
                    </a>
                    <a href="{{ route('mentor.group') }}" class="nav-item {{ request()->routeIs('mentor.group') ? 'active-nav' : '' }}">
                        <i class="fas fa-circle-plus"></i><span>Create Group</span>
                    </a>
                    <a href="{{ route('mentor.groups') }}" class="nav-item {{ request()->routeIs('mentor.groups') ? 'active-nav' : '' }}">
                        <i class="fas fa-users"></i><span>Groups</span>
                    </a>
                </div>

                <a href="{{ route('mentor.reports') }}" class="nav-item {{ request()->routeIs('mentor.reports') ? 'active-nav' : '' }}">
                    <i class="fas fa-flag"></i><span>Report System</span>
                </a>

                <a href="{{ route('mentor.harassment.index') }}" class="nav-item {{ request()->routeIs('mentor.harassment.index') ? 'active-nav' : '' }}">
                    <i class="fas fa-shield-halved"></i><span>Assigned Cases</span>
                </a>

                <a href="{{ route('mentor.harassment.analytics') }}" class="nav-item {{ request()->routeIs('mentor.harassment.analytics') ? 'active-nav' : '' }}">
                    <i class="fas fa-chart-pie"></i><span>Analytics</span>
                </a>

                <span class="nav-section-label">Content & Settings</span>

                <!-- Guidance Content -->
                <button type="button" class="nav-item {{ $isGuidanceOpen ? 'active-nav' : '' }}" data-submenu="guidance-sub">
                    <i class="fas fa-circle-info"></i><span>Guidance Content</span>
                    <i class="fas fa-chevron-down chevron {{ $isGuidanceOpen ? 'open' : '' }}"></i>
                </button>
                <div id="guidance-sub" class="nav-submenu {{ $isGuidanceOpen ? '' : 'hidden' }}">
                    <a href="{{ route('mentor.hygiene') }}" class="nav-item {{ request()->routeIs('mentor.hygiene') ? 'active-nav' : '' }}">
                        <i class="fas fa-pump-medical"></i><span>Menstrual Hygiene</span>
                    </a>
                    <a href="{{ route('mentor.general') }}" class="nav-item {{ request()->routeIs('mentor.general') ? 'active-nav' : '' }}">
                        <i class="fas fa-house-medical"></i><span>General Issues</span>
                    </a>
                    <a href="{{ route('mentor.emergency') }}" class="nav-item {{ request()->routeIs('mentor.emergency') ? 'active-nav' : '' }}">
                        <i class="fas fa-user-injured"></i><span>Emergency</span>
                    </a>
                </div>

                <!-- Settings -->
                <button type="button" class="nav-item {{ $isSettingsOpen ? 'active-nav' : '' }}" data-submenu="settings-sub">
                    <i class="fas fa-cog"></i><span>Settings</span>
                    <i class="fas fa-chevron-down chevron {{ $isSettingsOpen ? 'open' : '' }}"></i>
                </button>
                <div id="settings-sub" class="nav-submenu {{ $isSettingsOpen ? '' : 'hidden' }}">
                    <a href="{{ route('mentor.profile') }}" class="nav-item {{ request()->routeIs('mentor.profile') ? 'active-nav' : '' }}">
                        <i class="far fa-circle-user"></i><span>Profile</span>
                    </a>
                    <a href="{{ route('mentor.availability') }}" class="nav-item {{ request()->routeIs('mentor.availability') ? 'active-nav' : '' }}">
                        <i class="far fa-calendar-check"></i>
                        <span>Availability</span>
                    </a>
                    <a href="{{ route('mentor.notifications') }}" class="nav-item {{ request()->routeIs('mentor.notifications') ? 'active-nav' : '' }}">
                        <i class="far fa-bell"></i><span>Notifications</span>
                    </a>
                    <a href="{{ route('mentor.settings') }}" class="nav-item {{ request()->routeIs('mentor.settings') ? 'active-nav' : '' }}">
                        <i class="fas fa-sliders"></i><span>Main Settings</span>
                    </a>
                </div>

                <div style="height:16px;"></div>
            </nav>
        </div>

        <!-- Mentor card -->
        <div class="sidebar-admin-card">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName ?? 'Mentor User') }}&background=7c3aed&color=fff&bold=true&size=40"
                     class="w-9 h-9 rounded-full flex-shrink-0"
                     style="border: 2px solid var(--sidebar-border);"
                     alt="{{ $mentorName ?? 'Mentor' }}">
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate" style="color: var(--text-primary);">{{ $mentorName ?? 'Mentor User' }}</p>
                    <p class="text-[11px] truncate" style="color: var(--text-secondary);">{{ $mentorEmail ?? 'mentor@tithandizane.org' }}</p>
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
                    <p class="topbar-title">@yield('page-title', 'Welcome back, Mentor')</p>
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
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span style="position:absolute;top:-4px;right:-4px;background:var(--red);color:#fff;font-size:10px;font-weight:700;border-radius:999px;min-width:17px;height:17px;display:flex;align-items:center;justify-content:center;padding:0 3px;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <!-- Divider -->
                <div style="width:1px;height:24px;background:var(--border-color);flex-shrink:0;"></div>

                <!-- Mentor avatar + name -->
                <div class="flex items-center gap-2">
                    <span class="mentor-name-label">{{ $mentorName ?? 'Mentor' }}</span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName ?? 'Mentor') }}&background=7c3aed&color=fff&size=128"
                         class="w-8 h-8 rounded-full flex-shrink-0"
                         style="border:2px solid var(--sidebar-border);"
                         alt="{{ $mentorName ?? 'Mentor' }}">
                </div>

                <!-- Logout -->
                <button class="topbar-btn-danger"
                        onclick="document.getElementById('logout-form').submit();"
                        aria-label="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="logout-label">Logout</span>
                </button>
                <form id="logout-form" action="{{ route('mentor.logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </header>

        <!-- Page content -->
        <main class="page-content">
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
        <button id="notifClose"
                style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:var(--light-red);color:var(--red);border:none;cursor:pointer;"
                aria-label="Close notifications">
            <i class="fas fa-times" style="font-size:13px;"></i>
        </button>
    </div>

    <!-- List -->
    <div style="flex:1;overflow-y:auto;padding:12px;">
        @if(isset($unreadNotifications) && $unreadNotifications->isEmpty())
            <div style="text-align:center;padding:48px 0;">
                <i class="fas fa-bell-slash" style="font-size:32px;color:var(--text-secondary);opacity:.35;"></i>
                <p style="font-size:13px;color:var(--text-secondary);margin-top:10px;">No notifications</p>
            </div>
        @elseif(isset($unreadNotifications))
            @foreach($unreadNotifications as $notification)
                <div style="padding:12px;border-radius:12px;background:var(--light-purple);border:1px solid var(--border-color);margin-bottom:8px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:4px;">
                        <p style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ $notification->data['title'] ?? 'Notification' }}</p>
                        <form action="{{ route('mentor.notification.read', $notification->id) }}" method="POST" style="flex-shrink:0;">
                            @csrf
                            <button type="submit" style="font-size:11px;font-weight:500;padding:3px 10px;border-radius:999px;background:var(--purple);color:#fff;border:none;cursor:pointer;">
                                Mark read
                            </button>
                        </form>
                    </div>
                    <p style="font-size:12px;color:var(--text-secondary);">{{ $notification->data['message'] ?? '' }}</p>
                    <p style="font-size:11px;color:var(--text-secondary);opacity:.65;margin-top:6px;">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Footer -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border-color);flex-shrink:0;">
        @if(isset($unreadNotifications) && $unreadNotifications->isNotEmpty())
            <form action="{{ route('mentor.notification.read-all') }}" method="POST">
                @csrf
                <button type="submit" style="font-size:12px;font-weight:500;padding:7px 14px;border-radius:8px;background:var(--purple);color:#fff;border:none;cursor:pointer;">
                    Mark all as read
                </button>
            </form>
        @endif
        <p style="font-size:11px;color:var(--text-secondary);margin-left:auto;">info@Tithandizane.com</p>
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
        el.textContent = ':root{--bg-primary:#0f0e1a;--bg-secondary:#18172b;--text-primary:#f1f0ff;--text-secondary:#9ca3af;--card-bg:#1c1b2e;--border-color:#2d2b45;--sidebar-bg:#1e1b38;--sidebar-hover:rgba(167,139,250,0.10);--sidebar-active:rgba(167,139,250,0.15);--sidebar-text:#9ca3af;--sidebar-text-bold:#f1f0ff;--sidebar-border:#2d2b45;--sidebar-accent:#a78bfa;--light-teal:#064e3b;--light-orange:#451a03;--light-red:#450a0a;--light-purple:#2e1065;--light-blue:#1e3a5f;--light-gray:#1c1b2e;}';
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

    // Close on Escape
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
function initNotifications() {
    var panel    = document.getElementById('notifPanel');
    var backdrop = document.getElementById('notifBackdrop');
    var bellBtn  = document.getElementById('bellBtn');
    var closeBtn = document.getElementById('notifClose');
    var isOpen   = false;

    function openPanel() {
        if (!panel) return;
        panel.classList.add('open');
        backdrop.classList.add('open');
        isOpen = true;
    }
    function closePanel() {
        if (!panel) return;
        panel.classList.remove('open');
        backdrop.classList.remove('open');
        isOpen = false;
    }
    function toggle() { isOpen ? closePanel() : openPanel(); }

    if (bellBtn)  bellBtn.addEventListener('click',  function(e){ e.stopPropagation(); toggle(); });
    if (closeBtn) closeBtn.addEventListener('click',  function(e){ e.stopPropagation(); closePanel(); });
    if (backdrop) backdrop.addEventListener('click',  closePanel);
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && isOpen) closePanel(); });
}

// ── Boot ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initSidebar();
    initAccordions();
    initNotifications();
    var tt = document.getElementById('themeToggle');
    if (tt) tt.addEventListener('click', toggleTheme);
});
</script>

@stack('scripts')
</body>
</html>