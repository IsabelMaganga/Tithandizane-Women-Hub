<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Tithandizane Women Hub</title>
    <link rel="short icon" href="{{ asset('images/Ellipse 3.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playwrite+DK+Uloopet+Guides&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

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

        body{
            font-family: "poppins", Arial, Helvetica, sans-serif;

        }
        .notificationShow{
             right: 10px;
        }
        .notificationHide{
            right: -1200px;
            animation-delay: 10s;
        }
        .notificationChatShow{
             right: 10px;
        }
        .notificationChatHide{
            right: -1200px;
            animation-delay: 10s;
        }

        .arrow-rotate{
            transform: rotate(180deg);
        }
        #chat-sub-list,
        #guidance-sub-list,
        #settings-sub-list,
        #general-sub-list {
            display: none;
        }
        #chat-sub-list.show,
        #guidance-sub-list.show,
        #settings-sub-list.show,
        #general-sub-list.show {
            display: block;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Left Sidebar - Dark Navigation -->
        <div class="w-[250px] bg-gray-900 text-white h-full flex flex-col">
            <a href=" {{ route('mentor.dashboard')}}" class="p-4 select-none">
                <h1 class="text-xl font-bold">Tithandizane</h1>
                <p class="text-sm text-gray-400">Women Hub</p>
            </a>

            <nav class="relative flex-1 h-full mx-2 mt-0 overflow-auto text-sm" id="sidebar-nav">
                <a href="{{ route('mentor.dashboard')}}" class="sticky top-0 flex items-center justify-between gap-4 px-6 py-3 text-center text-gray-600 transition-all ease-in-out delay-75 bg-white rounded-3xl hover:bg-gray-50 nav-item" data-page="dashboard">
                    <span class="ml-0">Dashboard</span>
                    <i class="w-5 fas fa-home"></i>
                </a>

                 {{-- General tab --}}
                <div class="border-t div mt-3 border-white/30">
                    <div class="flex items-center justify-between w-full hover:bg-gray-800 div">
                        <div class="flex items-center w-full px-2 py-3 text-gray-300 cursor-pointer nav-item" data-page="guidance">
                            <i class="w-5 fa-solid fa-circle-info"></i>
                            <span class="ml-0 capitalize">General</span>
                        </div>

                        <i id="showGeneralIcon" class="mr-4 transition-all cursor-pointer fas fa-chevron-down arrow-rotate"></i>
                    </div>

                    <div id="general-sub-list" class="text-[12px]">

                       {{-- appointments --}}
                        <a href="{{ route('mentor.appointment')}}" class="flex items-center px-6 py-3 mt-3 text-gray-300  hover:text-gray-50 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-calendar"></i>
                            <span class="ml-3">Appointments</span>
                        </a>

                        {{-- calendar --}}
                        <a href="{{ route('mentor.calender')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item">
                            <i class="w-5 fa-regular fa-calendar"></i>
                            <span class="ml-3">Calendar</span>
                        </a>

                    </div>
                </div>

                {{-- chat tab --}}
                <div class="border-t div  border-white/30">
                    <div class="flex items-center justify-between w-full div">
                        <div class="flex items-center px-2 py-3 text-gray-300 cursor-pointer  nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-0">Chats</span>
                        </div>

                        <i id="showChatIcon" class="mr-4 transition-all cursor-pointer fas fa-chevron-down arrow-rotate"></i>
                    </div>

                    <div id="chat-sub-list" class="sub-list text-[12px] w-full">
                        <a href="{{ route('mentor.chat')}}" class="flex items-center px-6 py-3 hover:bg-gray-800 text-gray-300  nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-3">Mentorship Sessions</span>
                        </a>
                        <a href="{{ route('mentor.group')}}" class="flex items-center px-6 py-3 hover:bg-gray-800 text-gray-300  nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-3">Create group</span>
                        </a>
                        <a href="{{ route('mentor.groups')}}" class="flex items-center px-6 py-3 hover:bg-gray-800 text-gray-300  nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-3">Groups</span>
                        </a>
                    </div>
                </div>

                 {{-- report --}}
                <div class="border-t div border-white/30">
                    <a href="{{ route('mentor.reports')}}" class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                        <i class="w-5 fa-solid fa-user-astronaut"></i>
                        <span class="ml-0 capitalize">Report system</span>
                    </a>
                </div>

                {{-- Guidance tab --}}
                <div class="border-t div border-white/30">
                    <div class="flex items-center justify-between w-full hover:bg-gray-800 div">
                        <div class="flex items-center w-full px-2 py-3 text-gray-300 cursor-pointer nav-item" data-page="guidance">
                            <i class="w-5 fa-solid fa-circle-info"></i>
                            <span class="ml-0 capitalize">Guidance content</span>
                        </div>

                        <i id="showGuidanceIcon" class="mr-4 transition-all cursor-pointer fas fa-chevron-down arrow-rotate"></i>
                    </div>

                    <div id="guidance-sub-list" class="text-[12px]">
                        <a href="{{ route('mentor.hygiene')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="hygiene">
                            <i class="w-5 fa-solid fa-pump-medical"></i>
                            <span class="ml-3">Hygiene</span>
                        </a>
                        <a href="{{ route('mentor.general')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="General">
                            <i class="w-5 fa-solid fa-house-medical"></i>
                            <span class="ml-3 capitalize">General</span>
                        </a>
                        <a href="{{ route('mentor.emergency')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="emergency">
                            <i class="w-5 fa-solid fa-user-injured"></i>
                            <span class="ml-3">Emergency</span>
                        </a>
                    </div>
                </div>

                {{-- settings tab --}}
                <div  class="border-t div border-white/30">
                    <div class="flex items-center justify-between w-full hover:bg-gray-800 div">
                        <div class="flex items-center w-full px-2 py-3 text-gray-300 cursor-pointer nav-item" data-page="guidance">
                            <i class="w-5 fas fa-cog"></i>
                            <span class="ml-0 capitalize">settings</span>
                        </div>

                        <i id="showSettings" class="mr-4 transition-all cursor-pointer fas fa-chevron-down arrow-rotate"></i>
                    </div>

                    <div id="settings-sub-list" class="text-[12px]">

                        {{--  profile tab  --}}
                         <a href="{{ route('mentor.profile')}}" class="flex items-center px-6 py-3 text-gray-300 border-t border-white/30 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-circle-user"></i>
                            <span class="ml-3">Profile</span>
                        </a>

                        {{--  notification tab  --}}
                        <a href="{{ route('mentor.notifications')}}" class="flex items-center px-6 py-3 text-gray-300  hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-bell"></i>
                            <span class="ml-3">Notifications</span>
                        </a>

                        {{--  profile tab  --}}
                         <a href="{{ route('mentor.settings')}}" class="flex items-center px-6 py-3 text-gray-300  hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-circle-user"></i>
                            <span class="ml-3">Main settings</span>
                        </a>

                    </div>
                </div>
            </nav>

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
            <div class="bg-[#111827] flex justify-end sticky top-0 z-30 shadow-sm">
                <div class="flex items-center justify-between px-8 py-4">
                    <div class="flex items-center space-x-4">
                        {{-- notifications --}}
                        <div class="relative px-2 notifications">
                            <i class="text-xl text-yellow-500 cursor-pointer fas fa-bell hover:text-yellow-600" id="bellIcon"></i>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <p id="notifCount" class="absolute w-2 h-2 bg-yellow-500 rounded-full right-0 -top-1 text-[11px] text-white"></p>
                                @endif
                        </div>

                        {{-- name-section --}}
                        <div class="w-px h-8"></div>
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-100">{{ $mentorName ?? 'Mentor' }}</p>
                                <p class="text-xs text-gray-200">{{ $mentorEmail ?? 'mentor@example.com' }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName ?? 'Mentor') }}&background=0D8F81&color=fff&size=128" class="w-10 h-10 rounded-full">
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative p-1">
                @yield('content')

                <div id="notificationSideBar" class="notificationHide shadow flex flex-col bg-white fixed h-[89%] w-[20%] p-1 md:w-[30%] bottom-0 z-10">
                    <div class="flex items-center justify-between w-full p-3">
                        <p>Notifications</p>
                        <p id="closeNotif"><i class="text-xl text-red-600 fa-regular fa-circle-xmark"></i></p>
                    </div>
                    <div class="flex justify-between flex-1 w-full p-3 items-top">
                        <div class="flex flex-col w-full gap-2 text-sm">
                            @if(isset($unreadNotifications) && $unreadNotifications->isEmpty())
                                <p class="mt-4 text-center text-gray-500">No notifications</p>
                            @elseif(isset($unreadNotifications))
                                @foreach ($unreadNotifications as $notification)
                                    <div class="grid w-full grid-cols-3 p-2 text-sm rounded list bg-gray-100/20">
                                        <div class="flex-col col-span-3 text">
                                            <div class="flex items-center justify-between w-full mb-2 div">
                                                <h1 class="font-semibold">{{ $notification->data['title'] ?? 'Notification' }}</h1>
                                                <form action="{{ route('mentor.notification.read', $notification->id) }}" method="POST" class="bg-slate-800 rounded-3xl text-gray-200 hover:text-gray-100 cursor-pointer transition-all delay-75 text-[10px] p-1 px-3">
                                                    @csrf
                                                    <button type="submit">Mark as read</button>
                                                </form>
                                            </div>
                                            <p class="text-gray-500 wrap-break-word">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-sm text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Mark all as read --}}
                    <div class="flex items-center justify-between w-full p-2">
                        @if(isset($unreadNotifications) && $unreadNotifications->isNotEmpty())
                        <form action="{{ route('mentor.notification.read-all') }}" method="POST" class="bg-slate-800 rounded-3xl text-gray-200 hover:text-gray-100 cursor-pointer transition-all delay-75 text-[10px] p-2 px-3">
                            @csrf
                            <button type="submit">Mark all as read</button>
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
        // Make sure DOM is fully loaded before executing
        document.addEventListener('DOMContentLoaded', function() {

            // Chat dropdown toggle
            const showChatIcon = document.getElementById('showChatIcon');
            const chatSubList = document.getElementById('chat-sub-list');

            if (showChatIcon && chatSubList) {
                showChatIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    chatSubList.classList.toggle('show');
                    showChatIcon.classList.toggle('arrow-rotate');
                });
            }

            // Guidance dropdown toggle
            const showGuidanceIcon = document.getElementById('showGuidanceIcon');
            const guidanceSubList = document.getElementById('guidance-sub-list');

            if (showGuidanceIcon && guidanceSubList) {
                showGuidanceIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    guidanceSubList.classList.toggle('show');
                    showGuidanceIcon.classList.toggle('arrow-rotate');
                });
            }

            // settings dropdown toggle
            const showSettingsIcon = document.getElementById('showSettings');
            const settingsSubList = document.getElementById('settings-sub-list');

            if (showSettingsIcon && settingsSubList) {
                showSettingsIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    settingsSubList.classList.toggle('show');
                    showSettingsIcon.classList.toggle('arrow-rotate');
                });
            }

             // general dropdown toggle
            const showGeneralIcon = document.getElementById('showGeneralIcon');
            const generalSubList = document.getElementById('general-sub-list');

            if (showGeneralIcon && generalSubList) {
                showGeneralIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    generalSubList.classList.toggle('show');
                    showGeneralIcon.classList.toggle('arrow-rotate');
                });
            }


            // Navigation active state management
            const navItems = document.querySelectorAll('.nav-item');
            const currentPath = window.location.pathname;

            {{--  navItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && href !== '#' && currentPath.includes(href)) {
                    item.classList.add('bg-gray-800', 'text-white');
                    item.classList.remove('text-gray-300');
                }
            });  --}}

            // Notifications sidebar toggle
            const notificationSideBar = document.getElementById('notificationSideBar');
            const closeNotif = document.getElementById('closeNotif');
            const bellIcon = document.getElementById('bellIcon');

            if (bellIcon && notificationSideBar) {
                bellIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationSideBar.classList.remove('notificationHide');
                    notificationSideBar.classList.add('notificationShow');
                });
            }

            if (closeNotif && notificationSideBar) {
                closeNotif.addEventListener('click', function() {
                    notificationSideBar.classList.remove('notificationShow');
                    notificationSideBar.classList.add('notificationHide');
                });
            }

            // Click outside to close notification sidebar
            document.addEventListener('click', function(event) {
                if (notificationSideBar && notificationSideBar.classList.contains('notificationShow')) {
                    if (!notificationSideBar.contains(event.target) && !bellIcon.contains(event.target)) {
                        notificationSideBar.classList.remove('notificationShow');
                        notificationSideBar.classList.add('notificationHide');
                    }
                }
            });
        });

        // Chat request broadcast channel (needs Echo setup)
        @if(isset($userId))
        const userId = {{ $userId }};
        @else
        const userId = {{ auth()->id() ?? 0 }};
        @endif

        // Check if Echo is available (commented out to avoid errors if Echo not loaded)
        /*
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Echo !== 'undefined' && window.Echo) {
                console.log("Echo loaded");

                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('New notification:', notification);

                        const countEl = document.getElementById('notifCount');
                        if (countEl) {
                            const currentText = countEl.innerText;
                            const count = (currentText === '9+' ? 9 : (parseInt(currentText) || 0));
                            const newCount = count + 1;
                            countEl.innerText = newCount > 9 ? '9+' : newCount;
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
                            setTimeout(() => {
                                div.remove();
                            }, 5000);
                        }
                    });
            } else {
                console.log("Echo is not loaded or configured");
            }
        });
        */
    </script>

    @stack('scripts')
</body>
</html>
