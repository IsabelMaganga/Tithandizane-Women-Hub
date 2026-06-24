<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Tithandizane Women Hub</title>
    <link rel="icon" href="{{ asset('images/Ellipse 3.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- Comment out Vite to fix the manifest error --}}
    {{-- @vite(['resources/js/app.js']) --}}

    <style>
        /* Custom scrollbar styles */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        body {
            font-family: "Poppins", Arial, Helvetica, sans-serif;
        }

        #notificationSideBar {
            transition: right 0.25s ease-in-out;
        }

        .notificationShow { right: 0; }
        .notificationHide { right: -1200px; }

        .arrow-rotate {
            transform: rotate(180deg);
        }

        .sub-list {
            display: none;
        }

        .sub-list.show {
            display: block;
        }

        /* Sidebar styles matching admin */
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

        .sidebar-nav-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav-scroll::-webkit-scrollbar-track {
            background: #2c3e50;
            border-radius: 10px;
        }

        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background: #3498db;
            border-radius: 10px;
        }

        .sidebar-nav-scroll::-webkit-scrollbar-thumb:hover {
            background: #2ecc71;
        }

        .nav-item {
            transition: all 0.2s ease;
        }

        .active-nav {
            background: #3498db !important;
            color: #FFFFFF !important;
        }

        .active-nav i,
        .active-nav span {
            color: #FFFFFF !important;
        }

        /* Settings Submenu Styles - matching admin */
        .settings-submenu {
            margin-left: 1.5rem;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
            border-left: 2px solid rgba(255,255,255,0.1);
            padding-left: 0.5rem;
        }

        .settings-submenu .nav-item {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
        }

        .settings-submenu .nav-item.active-nav {
            background: #3498db !important;
            color: #FFFFFF !important;
        }

        .settings-submenu .nav-item.active-nav i,
        .settings-submenu .nav-item.active-nav span {
            color: #FFFFFF !important;
        }

        .settings-submenu .nav-item:not(.active-nav):hover {
            background: rgba(255,255,255,0.08);
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background: #1a1a2e;
        }

        body.dark-mode .sidebar-wrapper {
            background: #0f0f1a !important;
            border-right-color: #2d3748 !important;
        }

        body.dark-mode .sidebar-wrapper [style*="border-color: #2c3e50;"] {
            border-color: #2d3748 !important;
        }

        body.dark-mode .sidebar-wrapper [style*="background: #2c3e50;"] {
            background: #1e293b !important;
            border-color: #2d3748 !important;
        }

        body.dark-mode .top-header {
            background: #1e293b !important;
            border-bottom-color: #2d3748 !important;
        }

        body.dark-mode .top-header h2 {
            color: #ffffff !important;
        }

        body.dark-mode .top-header .text-gray-600 {
            color: #cbd5e0 !important;
        }

        body.dark-mode .top-header .text-gray-700 {
            color: #e2e8f0 !important;
        }

        body.dark-mode .top-header .bg-gray-300 {
            background: #4a5568 !important;
        }

        body.dark-mode .top-header .ring-gray-300 {
            --tw-ring-color: #4a5568 !important;
        }

        body.dark-mode .bg-gray-100 {
            background: #1a1a2e !important;
        }

        body.dark-mode .bg-white {
            background: #16213e !important;
        }

        body.dark-mode .text-gray-800 {
            color: #e2e8f0 !important;
        }

        body.dark-mode .border-gray-200 {
            border-color: #2d3748 !important;
        }

        body.dark-mode .border-gray-100 {
            border-color: #2d3748 !important;
        }

        body.dark-mode .bg-gray-50 {
            background: #1e293b !important;
        }

        body.dark-mode .text-gray-500 {
            color: #94a3b8 !important;
        }

        body.dark-mode .text-gray-400 {
            color: #94a3b8 !important;
        }

        body.dark-mode .bg-slate-800 {
            background: #334155 !important;
        }

        body.dark-mode #notificationSideBar {
            background: #16213e !important;
        }

        body.dark-mode .shadow-sm {
            --tw-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3) !important;
        }

        body.dark-mode .shadow-xl {
            --tw-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.3) !important;
        }

        body.dark-mode .hover\:bg-gray-100:hover {
            background: #2d3748 !important;
        }

        .theme-toggle {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .theme-toggle:hover {
            transform: scale(1.05);
        }

        /* Logout button hover effect */
        .logout-btn:hover {
            background: #dc2626 !important;
            color: white !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100">

    @php
        $isGeneralOpen  = request()->routeIs('mentor.appointment') || request()->routeIs('mentor.calender');
        $isChatOpen     = request()->routeIs('mentor.chat') || request()->routeIs('mentor.group') || request()->routeIs('mentor.groups');
        $isGuidanceOpen = request()->routeIs('mentor.hygiene') || request()->routeIs('mentor.general') || request()->routeIs('mentor.emergency');
        $isSettingsOpen = request()->routeIs('mentor.profile') || request()->routeIs('mentor.notifications') || request()->routeIs('mentor.settings');

        $activeClasses   = 'bg-white text-gray-800';
        $inactiveClasses = 'text-gray-300 hover:bg-gray-800 hover:text-white';
    @endphp

    <div class="flex h-screen overflow-hidden">
        <!-- Left Sidebar - Dark Navigation - Matches admin structure -->
        <div class="w-64 flex flex-col shadow-xl sidebar-wrapper" style="background: #1a2a3a; border-right: 1px solid #2c3e50;">
            
            <!-- Sidebar Header with Logo -->
            <div class="p-6 border-b flex-shrink-0" style="border-color: #2c3e50;">
                <div class="flex items-center gap-3">
                    <a href="{{ route('mentor.dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo2.png') }}" alt="Tithandizane Logo" class="w-12 h-12 rounded-full object-cover shadow-md border-2 border-white/30">
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-white">Tithandizane</h1>
                            <p class="text-xs mt-1 opacity-90 text-white">Women Hub</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- SCROLLABLE NAVIGATION AREA - Matches admin structure -->
            <div class="sidebar-nav-scroll">
                <nav class="mt-6 space-y-1 px-3 pb-4" id="sidebar-nav">
                    <!-- Dashboard -->
                    <a href="{{ route('mentor.dashboard') }}" 
                       class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.dashboard') ? 'active-nav' : '' }}" 
                       data-page="dashboard" 
                       style="color: {{ request()->routeIs('mentor.dashboard') ? '#FFFFFF' : '#E2E8F0' }};">
                        <i class="fas fa-home w-5"></i>
                        <span class="ml-3 font-medium">Dashboard</span>
                    </a>

                    <!-- General -->
                    <div>
                        <button type="button" 
                                class="nav-item flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all duration-200 group {{ $isGeneralOpen ? 'active-nav' : '' }}" 
                                data-toggle="general-sub-list" 
                                data-icon="showGeneralIcon"
                                style="color: {{ $isGeneralOpen ? '#FFFFFF' : '#E2E8F0' }};">
                            <span class="flex items-center">
                                <i class="fa-solid fa-calendar-days w-5"></i>
                                <span class="ml-3">General</span>
                            </span>
                            <i id="showGeneralIcon" class="fas fa-chevron-down text-xs transition-transform {{ $isGeneralOpen ? 'arrow-rotate' : '' }}"></i>
                        </button>

                        <div id="general-sub-list" class="settings-submenu {{ $isGeneralOpen ? '' : 'hidden' }}">
                            <a href="{{ route('mentor.appointment') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.appointment') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.appointment') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-solid fa-calendar w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Appointments</span>
                            </a>
                            <a href="{{ route('mentor.calender') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.calender') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.calender') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-regular fa-calendar w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Calendar</span>
                            </a>
                        </div>
                    </div>

                    <!-- Chats -->
                    <div>
                        <button type="button" 
                                class="nav-item flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all duration-200 group {{ $isChatOpen ? 'active-nav' : '' }}" 
                                data-toggle="chat-sub-list" 
                                data-icon="showChatIcon"
                                style="color: {{ $isChatOpen ? '#FFFFFF' : '#E2E8F0' }};">
                            <span class="flex items-center">
                                <i class="fa-regular fa-comment w-5"></i>
                                <span class="ml-3">Chats</span>
                            </span>
                            <i id="showChatIcon" class="fas fa-chevron-down text-xs transition-transform {{ $isChatOpen ? 'arrow-rotate' : '' }}"></i>
                        </button>

                        <div id="chat-sub-list" class="settings-submenu {{ $isChatOpen ? '' : 'hidden' }}">
                            <a href="{{ route('mentor.chat') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.chat') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.chat') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-regular fa-comment w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Mentorship Sessions</span>
                            </a>
                            <a href="{{ route('mentor.group') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.group') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.group') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-solid fa-circle-plus w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Create group</span>
                            </a>
                            <a href="{{ route('mentor.groups') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.groups') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.groups') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-solid fa-users w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Groups</span>
                            </a>
                        </div>
                    </div>

                    <!-- Report System -->
                    <a href="{{ route('mentor.reports') }}" 
                       class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.reports') ? 'active-nav' : '' }}" 
                       data-page="reports" 
                       style="color: {{ request()->routeIs('mentor.reports') ? '#FFFFFF' : '#E2E8F0' }};">
                        <i class="fa-solid fa-flag w-5"></i>
                        <span class="ml-3">Report System</span>
                    </a>

                    <!-- Assigned Cases -->
                    <a href="{{ route('mentor.harassment.index') }}" 
                       class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.harassment.index') ? 'active-nav' : '' }}" 
                       data-page="assigned-cases" 
                       style="color: {{ request()->routeIs('mentor.harassment.index') ? '#FFFFFF' : '#E2E8F0' }};">
                        <i class="fa-solid fa-shield-halved w-5"></i>
                        <span class="ml-3">Assigned Cases</span>
                    </a>

                    <!-- Analytics -->
                    <a href="{{ route('mentor.harassment.analytics') }}" 
                       class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.harassment.analytics') ? 'active-nav' : '' }}" 
                       data-page="analytics" 
                       style="color: {{ request()->routeIs('mentor.harassment.analytics') ? '#FFFFFF' : '#E2E8F0' }};">
                        <i class="fa-solid fa-chart-pie w-5"></i>
                        <span class="ml-3">Analytics</span>
                    </a>

                    <!-- Guidance Content -->
                    <div>
                        <button type="button" 
                                class="nav-item flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all duration-200 group {{ $isGuidanceOpen ? 'active-nav' : '' }}" 
                                data-toggle="guidance-sub-list" 
                                data-icon="showGuidanceIcon"
                                style="color: {{ $isGuidanceOpen ? '#FFFFFF' : '#E2E8F0' }};">
                            <span class="flex items-center">
                                <i class="fa-solid fa-circle-info w-5"></i>
                                <span class="ml-3">Guidance Content</span>
                            </span>
                            <i id="showGuidanceIcon" class="fas fa-chevron-down text-xs transition-transform {{ $isGuidanceOpen ? 'arrow-rotate' : '' }}"></i>
                        </button>

                        <div id="guidance-sub-list" class="settings-submenu {{ $isGuidanceOpen ? '' : 'hidden' }}">
                            <a href="{{ route('mentor.hygiene') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.hygiene') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.hygiene') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-solid fa-pump-medical w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Menstrual Hygiene</span>
                            </a>
                            <a href="{{ route('mentor.general') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.general') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.general') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-solid fa-house-medical w-4 text-sm"></i>
                                <span class="ml-3 text-sm">General Issues</span>
                            </a>
                            <a href="{{ route('mentor.emergency') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.emergency') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.emergency') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-solid fa-user-injured w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Emergency</span>
                            </a>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div>
                        <button type="button" 
                                class="nav-item flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all duration-200 group {{ $isSettingsOpen ? 'active-nav' : '' }}" 
                                data-toggle="settings-sub-list" 
                                data-icon="showSettings"
                                style="color: {{ $isSettingsOpen ? '#FFFFFF' : '#E2E8F0' }};">
                            <span class="flex items-center">
                                <i class="fas fa-cog w-5"></i>
                                <span class="ml-3">Settings</span>
                            </span>
                            <i id="showSettings" class="fas fa-chevron-down text-xs transition-transform {{ $isSettingsOpen ? 'arrow-rotate' : '' }}"></i>
                        </button>

                        <div id="settings-sub-list" class="settings-submenu {{ $isSettingsOpen ? '' : 'hidden' }}">
                            <a href="{{ route('mentor.profile') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.profile') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.profile') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-regular fa-circle-user w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Profile</span>
                            </a>
                            <a href="{{ route('mentor.notifications') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.notifications') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.notifications') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fa-regular fa-bell w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Notifications</span>
                            </a>
                            <a href="{{ route('mentor.settings') }}" 
                               class="nav-item flex items-center px-4 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('mentor.settings') ? 'active-nav' : '' }}" 
                               style="color: {{ request()->routeIs('mentor.settings') ? '#FFFFFF' : '#E2E8F0' }};">
                                <i class="fas fa-sliders w-4 text-sm"></i>
                                <span class="ml-3 text-sm">Main Settings</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Mentor user card with email - fixed at bottom (does not scroll) -->
            <div class="flex-shrink-0 p-4 mx-3 rounded-xl mb-4" style="background: #2c3e50; border: 1px solid #34495e;">
                <div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName ?? 'Mentor User') }}&background=3498db&color=fff&bold=true&size=40" 
                         class="w-10 h-10 rounded-full border-2 border-white" 
                         alt="{{ $mentorName ?? 'Mentor User' }}">
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-white">{{ $mentorName ?? 'Mentor User' }}</p>
                        <p class="text-xs text-white/80">{{ $mentorEmail ?? 'mentor@tithandizane.org' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top Header - White background with Logout and Dark Mode -->
            <div class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 bg-white shadow-sm border-b border-gray-200 top-header">
                <h2 class="text-lg font-semibold text-gray-800 capitalize">{{ trim($__env->yieldContent('title', 'Dashboard')) }}</h2>

                <div class="flex items-center space-x-4">
                    <!-- Dark/Light Mode Toggle Button -->
                    <button id="themeToggle" class="theme-toggle flex items-center gap-2 px-3 py-2 rounded-lg transition bg-gray-100 hover:bg-gray-200 text-gray-700">
                        <i id="themeIcon" class="fas fa-moon"></i>
                        <span id="themeText" class="text-sm font-medium">Dark</span>
                    </button>

                    {{-- notifications --}}
                    <div class="relative px-1">
                        <i class="text-xl text-gray-600 cursor-pointer fas fa-bell hover:text-gray-800" id="bellIcon"></i>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span id="notifCount" class="absolute flex items-center justify-center px-1 text-[10px] font-semibold text-white bg-red-500 rounded-full -right-2 -top-2 h-5 min-w-[1.25rem]">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </div>

                    <div class="w-px h-8 bg-gray-300"></div>

                    {{-- name section - Only name, no email --}}
                    <div class="flex items-center space-x-3">
                        <p class="text-sm font-medium text-gray-700">{{ $mentorName ?? 'Mentor' }}</p>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName ?? 'Mentor') }}&background=0D8F81&color=fff&size=128" 
                             class="w-10 h-10 rounded-full ring-2 ring-gray-300" 
                             alt="{{ $mentorName ?? 'Mentor' }}">
                    </div>

                    <!-- Logout Button -->
                    <a href="#" class="flex items-center gap-2 px-4 py-2 rounded-lg transition bg-red-600 hover:bg-red-700 text-white" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="text-sm font-medium">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('mentor.logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

            <div class="relative p-6">
                @yield('content')

                {{-- Notification side panel --}}
                <div id="notificationSideBar" class="fixed bottom-0 z-40 flex flex-col w-full h-[89%] max-w-[26rem] bg-white shadow-xl notificationHide">
                    <div class="flex items-center justify-between w-full p-4 border-b border-gray-100">
                        <p class="font-semibold text-gray-800">Notifications</p>
                        <button id="closeNotif" type="button" aria-label="Close notifications">
                            <i class="text-xl text-red-600 fa-regular fa-circle-xmark"></i>
                        </button>
                    </div>

                    <div class="flex-1 w-full p-3 overflow-y-auto">
                        <div class="flex flex-col w-full gap-2 text-sm">
                            @if(isset($unreadNotifications) && $unreadNotifications->isEmpty())
                                <p class="mt-4 text-center text-gray-500">No notifications</p>
                            @elseif(isset($unreadNotifications))
                                @foreach ($unreadNotifications as $notification)
                                    <div class="w-full p-3 text-sm rounded-lg bg-gray-50">
                                        <div class="flex items-center justify-between w-full mb-2">
                                            <h1 class="font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Notification' }}</h1>
                                            <form action="{{ route('mentor.notification.read', $notification->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="rounded-full bg-slate-800 px-3 py-1 text-[10px] text-gray-200 transition-colors hover:text-white">Mark as read</button>
                                            </form>
                                        </div>
                                        <p class="text-gray-500 break-words">{{ $notification->data['message'] ?? '' }}</p>
                                        <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between w-full p-3 border-t border-gray-100">
                        @if(isset($unreadNotifications) && $unreadNotifications->isNotEmpty())
                            <form action="{{ route('mentor.notification.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="rounded-full bg-slate-800 px-3 py-2 text-[10px] text-gray-200 transition-colors hover:text-white">Mark all as read</button>
                            </form>
                        @endif
                        <p class="text-sm text-gray-400">info@Tithandizane.com</p>
                    </div>
                </div>

                <div id="notificationPopUp" class="fixed z-50 space-y-2 text-xs bg-white select-none bottom-3 right-10 w-60"></div>
            </div>
        </div>
    </div>

    <script>
        // Dark/Light Mode Toggle with localStorage persistence
        function initTheme() {
            const savedTheme = localStorage.getItem('mentor_theme');
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
                    themeText.textContent = 'Light';
                } else {
                    themeIcon.className = 'fas fa-moon';
                    themeText.textContent = 'Dark';
                }
            }
        }
        
        function toggleTheme() {
            const body = document.body;
            const isDark = body.classList.contains('dark-mode');
            
            if (isDark) {
                body.classList.remove('dark-mode');
                localStorage.setItem('mentor_theme', 'light');
                updateThemeUI(false);
            } else {
                body.classList.add('dark-mode');
                localStorage.setItem('mentor_theme', 'dark');
                updateThemeUI(true);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize theme
            initTheme();

            // Theme toggle button
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', toggleTheme);
            }

            // Collapsible nav sections
            document.querySelectorAll('[data-toggle]').forEach(function (button) {
                const subList = document.getElementById(button.dataset.toggle);
                const icon = document.getElementById(button.dataset.icon);

                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (subList) {
                        subList.classList.toggle('hidden');
                        if (icon) {
                            icon.classList.toggle('arrow-rotate');
                        }
                    }
                });
            });

            // Notifications sidebar toggle
            const notificationSideBar = document.getElementById('notificationSideBar');
            const closeNotif = document.getElementById('closeNotif');
            const bellIcon = document.getElementById('bellIcon');

            if (bellIcon && notificationSideBar) {
                bellIcon.addEventListener('click', function (e) {
                    e.stopPropagation();
                    notificationSideBar.classList.remove('notificationHide');
                    notificationSideBar.classList.add('notificationShow');
                });
            }

            if (closeNotif && notificationSideBar) {
                closeNotif.addEventListener('click', function () {
                    notificationSideBar.classList.remove('notificationShow');
                    notificationSideBar.classList.add('notificationHide');
                });
            }

            // Click outside to close notification sidebar
            document.addEventListener('click', function (event) {
                if (notificationSideBar && notificationSideBar.classList.contains('notificationShow')) {
                    if (!notificationSideBar.contains(event.target) && !bellIcon.contains(event.target)) {
                        notificationSideBar.classList.remove('notificationShow');
                        notificationSideBar.classList.add('notificationHide');
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>