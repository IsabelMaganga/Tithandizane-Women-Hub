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

    <div class="flex h-screen">
        <!-- Left Sidebar - Dark Navigation -->
        <div class="w-[250px] bg-gray-900 text-white h-full flex flex-col">
            <a href=" {{ route('mentor.dashboard')}}" class="p-4 select-none">
                <h1 class="text-xl font-bold">Tithandizane</h1>
                <p class="text-sm text-gray-400">Women Hub</p>
            </a>

            <nav class="relative flex-1 h-full mx-2 mt-0 overflow-auto text-sm" id="sidebar-nav">

                <a href="{{ route('mentor.dashboard') }}"
                   class="sticky top-0 z-10 flex items-center justify-between gap-4 px-6 py-3 transition-all rounded-3xl {{ request()->routeIs('mentor.dashboard') ? $activeClasses : $inactiveClasses }}">
                    <span>Dashboard</span>
                    <i class="w-5 fas fa-home"></i>
                </a>

                {{-- General tab --}}
                <div class="mt-3 border-t border-white/30">
                    <button type="button" class="flex items-center justify-between w-full px-2 py-3 text-left transition-colors {{ $isGeneralOpen ? 'text-white bg-gray-800' : 'text-gray-300 hover:bg-gray-800' }}" data-toggle="general-sub-list" data-icon="showGeneralIcon">
                        <span class="flex items-center">
                            <i class="w-5 fa-solid fa-calendar-days"></i>
                            <span class="ml-0 capitalize">General</span>
                        </span>
                        <i id="showGeneralIcon" class="mr-2 transition-transform fas fa-chevron-down {{ $isGeneralOpen ? 'arrow-rotate' : '' }}"></i>
                    </button>

                    <div id="general-sub-list" class="sub-list text-[12px] {{ $isGeneralOpen ? 'show' : '' }}">
                        <a href="{{ route('mentor.appointment') }}" class="flex items-center px-6 py-3 mt-1 transition-colors {{ request()->routeIs('mentor.appointment') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 text-yellow-500 text-md fa-solid fa-calendar"></i>
                            <span class="ml-3">Appointments</span>
                        </a>
                        <a href="{{ route('mentor.calender') }}" class="flex items-center px-6 py-3 transition-colors {{ request()->routeIs('mentor.calender') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-regular fa-calendar"></i>
                            <span class="ml-3">Calendar</span>
                        </a>
                    </div>
                </div>

                {{-- chat tab --}}
                <div class="border-t border-white/30">
                    <button type="button" class="flex items-center justify-between w-full px-2 py-3 text-left transition-colors {{ $isChatOpen ? 'text-white bg-gray-800' : 'text-gray-300 hover:bg-gray-800' }}" data-toggle="chat-sub-list" data-icon="showChatIcon">
                        <span class="flex items-center">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-0">Chats</span>
                        </span>
                        <i id="showChatIcon" class="mr-2 transition-transform fas fa-chevron-down {{ $isChatOpen ? 'arrow-rotate' : '' }}"></i>
                    </button>

                    <div id="chat-sub-list" class="sub-list text-[12px] {{ $isChatOpen ? 'show' : '' }}">
                        <a href="{{ route('mentor.chat') }}" class="flex items-center px-6 py-3 mt-1 transition-colors {{ request()->routeIs('mentor.chat') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-3">Mentorship Sessions</span>
                        </a>
                        <a href="{{ route('mentor.group') }}" class="flex items-center px-6 py-3 transition-colors {{ request()->routeIs('mentor.group') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-solid fa-circle-plus"></i>
                            <span class="ml-3">Create group</span>
                        </a>
                        <a href="{{ route('mentor.groups') }}" class="flex items-center px-6 py-3 transition-colors {{ request()->routeIs('mentor.groups') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-solid fa-users"></i>
                            <span class="ml-3">Groups</span>
                        </a>
                    </div>
                </div>

                {{-- report --}}
                <div class="border-t border-white/30">
                    <a href="{{ route('mentor.reports') }}" class="flex items-center px-2 py-3 transition-colors {{ request()->routeIs('mentor.reports') ? $activeClasses : $inactiveClasses }}">
                        <i class="w-5 fa-solid fa-flag"></i>
                        <span class="ml-0 capitalize">Report system</span>
                    </a>
                </div>

                {{-- Guidance tab --}}
                <div class="border-t border-white/30">
                    <button type="button" class="flex items-center justify-between w-full px-2 py-3 text-left transition-colors {{ $isGuidanceOpen ? 'text-white bg-gray-800' : 'text-gray-300 hover:bg-gray-800' }}" data-toggle="guidance-sub-list" data-icon="showGuidanceIcon">
                        <span class="flex items-center">
                            <i class="w-5 fa-solid fa-circle-info"></i>
                            <span class="ml-0 capitalize">Guidance content</span>
                        </span>
                        <i id="showGuidanceIcon" class="mr-2 transition-transform fas fa-chevron-down {{ $isGuidanceOpen ? 'arrow-rotate' : '' }}"></i>
                    </button>

                    <div id="guidance-sub-list" class="sub-list text-[12px] {{ $isGuidanceOpen ? 'show' : '' }}">
                        <a href="{{ route('mentor.hygiene') }}" class="flex items-center px-6 py-3 mt-1 transition-colors {{ request()->routeIs('mentor.hygiene') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-solid fa-pump-medical"></i>
                            <span class="ml-3">Hygiene</span>
                        </a>
                        <a href="{{ route('mentor.general') }}" class="flex items-center px-6 py-3 transition-colors {{ request()->routeIs('mentor.general') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-solid fa-house-medical"></i>
                            <span class="ml-3 capitalize">General</span>
                        </a>
                        <a href="{{ route('mentor.emergency') }}" class="flex items-center px-6 py-3 transition-colors {{ request()->routeIs('mentor.emergency') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-solid fa-user-injured"></i>
                            <span class="ml-3">Emergency</span>
                        </a>
                    </div>
                </div>

                {{-- settings tab --}}
                <div class="border-t border-white/30">
                    <button type="button" class="flex items-center justify-between w-full px-2 py-3 text-left transition-colors {{ $isSettingsOpen ? 'text-white bg-gray-800' : 'text-gray-300 hover:bg-gray-800' }}" data-toggle="settings-sub-list" data-icon="showSettings">
                        <span class="flex items-center">
                            <i class="w-5 fas fa-cog"></i>
                            <span class="ml-0 capitalize">Settings</span>
                        </span>
                        <i id="showSettings" class="mr-2 transition-transform fas fa-chevron-down {{ $isSettingsOpen ? 'arrow-rotate' : '' }}"></i>
                    </button>

                    <div id="settings-sub-list" class="sub-list text-[12px] {{ $isSettingsOpen ? 'show' : '' }}">
                        <a href="{{ route('mentor.profile') }}" class="flex items-center px-6 py-3 mt-1 transition-colors {{ request()->routeIs('mentor.profile') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-regular fa-circle-user"></i>
                            <span class="ml-3">Profile</span>
                        </a>
                        <a href="{{ route('mentor.notifications') }}" class="flex items-center px-6 py-3 transition-colors {{ request()->routeIs('mentor.notifications') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fa-regular fa-bell"></i>
                            <span class="ml-3">Notifications</span>
                        </a>
                        <a href="{{ route('mentor.settings') }}" class="flex items-center px-6 py-3 transition-colors {{ request()->routeIs('mentor.settings') ? $activeClasses : $inactiveClasses }}">
                            <i class="w-5 fas fa-sliders"></i>
                            <span class="ml-3">Main settings</span>
                        </a>
                    </div>
                </div>
            </nav>

            {{-- notification bar --}}
            @if(isset($unreadCount) && $unreadCount > 0)
                <a href="{{ route('mentor.notifications') }}" class="block w-full pt-6 mt-auto text-sm">
                    <div class="relative flex items-center gap-3 px-6 py-3 text-gray-300 transition-colors bg-[#090d14] hover:bg-black hover:text-white">
                        <span class="relative">
                            <i class="text-xl text-yellow-500 fas fa-bell"></i>
                            <span class="absolute w-2 h-2 bg-yellow-500 rounded-full -right-1 -top-1 animate-pulse"></span>
                        </span>
                        <span>Notifications</span>
                        <span class="ml-auto rounded-full bg-yellow-500/20 px-2 py-0.5 text-xs font-semibold text-yellow-400">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    </div>
                </a>
            @endif

            <!-- Logout Button -->
            <div class="pt-6 mt-auto text-sm">
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 transition-colors bg-red-600 hover:bg-red-700 hover:text-white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="w-5 fas fa-sign-out-alt"></i>
                    <span class="ml-3">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('mentor.logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top Header -->
            <div class="sticky top-0 z-30 flex items-center justify-between px-8 py-4 bg-gradient-to-r from-gray-900 via-[#312e81] to-gray-900 shadow-sm">
                <h2 class="text-lg font-semibold text-white capitalize">{{ trim($__env->yieldContent('title', 'Dashboard')) }}</h2>

                <div class="flex items-center space-x-5">
                    {{-- notifications --}}
                    <div class="relative px-1">
                        <i class="text-xl text-yellow-500 cursor-pointer fas fa-bell hover:text-yellow-400" id="bellIcon"></i>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span id="notifCount" class="absolute flex items-center justify-center px-1 text-[10px] font-semibold text-white bg-red-500 rounded-full -right-2 -top-2 h-5 min-w-[1.25rem]">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </div>

                    <div class="w-px h-8 bg-white/10"></div>

                    {{-- name section --}}
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-100">{{ $mentorName ?? 'Mentor' }}</p>
                            <p class="text-xs text-gray-400">{{ $mentorEmail ?? 'mentor@example.com' }}</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName ?? 'Mentor') }}&background=0D8F81&color=fff&size=128" class="w-10 h-10 rounded-full ring-2 ring-white/10" alt="{{ $mentorName ?? 'Mentor' }}">
                    </div>
                </div>
            </div>

            <div class="relative p-1">
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
        document.addEventListener('DOMContentLoaded', function () {

            // Collapsible nav sections
            document.querySelectorAll('[data-toggle]').forEach(function (button) {
                const subList = document.getElementById(button.dataset.toggle);
                const icon = document.getElementById(button.dataset.icon);

                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    subList?.classList.toggle('show');
                    icon?.classList.toggle('arrow-rotate');
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

        // Chat / notification broadcast channel (needs Echo setup)
        {{--  const userId = {{ $userId ?? (auth()->id() ?? 0) }};  --}}

        // Uncomment once Laravel Echo + broadcasting are configured:
        /*
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Echo !== 'undefined' && window.Echo) {
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        const countEl = document.getElementById('notifCount');
                        if (countEl) {
                            const current = parseInt(countEl.innerText) || 0;
                            const next = current + 1;
                            countEl.innerText = next > 9 ? '9+' : next;
                        }

                        const container = document.getElementById('notificationPopUp');
                        if (container) {
                            const div = document.createElement('div');
                            div.className = 'bg-white shadow p-3 rounded border-l-4 border-blue-500 mb-2';
                            div.innerHTML = `
                                <p class="font-bold">${notification.name || 'Notification'}</p>
                                <p class="text-sm text-gray-500">${notification.message || ''}</p>
                            `;
                            container.appendChild(div);
                            setTimeout(() => div.remove(), 5000);
                        }
                    });
            }
        });
        */
    </script>

    @stack('scripts')
</body>
</html>
