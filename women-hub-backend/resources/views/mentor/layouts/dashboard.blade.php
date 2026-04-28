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
        #sub-list{
            display: none;
        }
         #sub-list2{
            display: none;
        }
        #sub-list.show {
            display: block;
        }

         #sub-list2.show {
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

            <nav class="relative flex-1 h-full mx-2 mt-0 overflow-auto text-sm " id="sidebar-nav">
                <a href="{{ route('mentor.dashboard')}}" class="sticky top-0 flex items-center justify-between gap-4 px-6 py-3 text-center text-gray-600 transition-all ease-in-out delay-75 bg-white rounded-3xl hover:bg-gray-50 nav-item" data-page="dashboard">
                    <i class="w-5 fas fa-home"></i>
                    <span class="ml-0">Dashboard</span>
                </a>

                {{-- appointments --}}
                <a href="{{ route('mentor.appointment')}}" class="flex items-center px-2 py-3 mt-3 text-gray-300 border-t border-white/30 hover:text-gray-50 hover:bg-gray-800 nav-item" data-page="guidance">
                    <i class="w-5 fa-regular fa-calendar"></i>
                    <span class="ml-3">Appointments</span>
                </a>

                {{-- chat tab --}}
                <div class="border-t div hover:bg-gray-800 border-white/30 ">
                    <div class="flex items-center justify-between w-full div">
                        <div class="flex items-center px-2 py-3 text-gray-300 cursor-pointer hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-0">chats</span>
                        </div>

                        <i id="showIcon" class="mr-4 transition-all cursor-pointer fas fa-chevron-down arrow-rotate"></i>
                     </div>

                    <div id="sub-list" class="sub-list text-[12px]">
                        <a href="{{ route('mentor.chat')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-3">MentorShipSessions</span>
                        </a>
                        <a href="{{ route('mentor.group')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-3">Create group</span>
                        </a>
                        <a href="{{ route('mentor.groups')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-regular fa-comment"></i>
                            <span class="ml-3">Groups</span>
                        </a>
                    </div>

                </div>

                {{-- Guidance tab --}}
                <div class="border-t div border-white/30 ">
                    <div class="flex items-center justify-between w-full hover:bg-gray-800 div">
                        <div class="flex items-center w-full px-2 py-3 text-gray-300 cursor-pointer nav-item" data-page="guidance">
                            <i class="w-5 fa-solid fa-circle-info"></i>
                            <span class="ml-0 capitalize">guidance content</span>
                        </div>

                    <i id="showIcon2" class="mr-4 transition-all cursor-pointer fas fa-chevron-down arrow-rotate"></i>

                        <i id="showIcon" class="mr-4 transition-all cursor-pointer fas fa-chevron-down arrow-rotate"></i>
                     </div>

                    <div id="sub-list" class=" text-[12px]">
                        <a href="{{ route('mentor.hygiene')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="hygiene">
                            <i class="w-5 fa-solid fa-pump-medical"></i>
                            <span class="ml-3">hygiene</span>
                        </a>
                        <a href="{{ route('mentor.general')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="General">
                            <i class="w-5 fa-solid fa-house-medical"></i>
                            <span class="ml-3 capitalize">General</span>
                        </a>
                        <a href="{{ route('mentor.emergency')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="emergency">
                            <i class="w-5 fa-solid fa-user-injured"></i>
                            <span class="ml-3">Emergency    </span>
                        </a>
                    </div>

                </div>

                {{-- calender --}}
                <a  href="{{ route('mentor.calender')}}" class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item">
                    <i class="w-5 fa-regular fa-calendar"></i>
                    <span class="ml-3">Calender</span>
                </a>

                {{-- report --}}
                <div class="border-t div border-white/30 ">
                        <a  href="{{ route('mentor.reports')}}" class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="w-5 fa-solid fa-user-astronaut"></i>
                            <span class="ml-0 capitalize">Report system</span>
                        </a>

                </div>

                {{-- profile tab --}}
                <a href="{{ route('mentor.profile')}}" class="flex items-center px-2 py-3 text-gray-300 border-t border-white/30 hover:bg-gray-800 nav-item" data-page="guidance">
                    <i class="w-5 fa-regular fa-circle-user"></i>
                    <span class="ml-3">Profile</span>
                </a>

                {{-- settings tab --}}
                <a href="{{ route('mentor.settings')}}" class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="settings">
                    <i class="w-5 fas fa-cog"></i>
                    <span class="ml-3">Settings</span>
                </a>

            </nav>

            <!-- Logout Button -->
            <div class="pt-6 mt-auto text-sm">
                <a href="{{ route('mentor.logout') }}" class="flex items-center px-6 py-3 text-gray-300 transition-colors bg-red-600 hover:bg-red-700 hover:text-white" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="w-5 fas fa-sign-out-alt"></i>
                    <span class="ml-3">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('mentor.logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

            {{-- <div class="px-2 py-5 border-t border-gray-800 wrap-break-word">
                <div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName) }}&background=0D8F81&color=fff" class="w-10 h-10 rounded-full">
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ $mentorName }}</p>
                        <p class="text-xs text-gray-400">{{ $mentorEmail }}</p>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">

            <!-- Top Header - Like "Welcome back, Andrea" -->
            <div class="bg-[#111827] flex justify-end sticky top-0 z-30 shadow-sm">
                <div class="flex items-center justify-between px-8 py-4">

                    <div class="flex items-center space-x-4">

                        {{-- notifications --}}
                        <div class="relative px-2 notifications">
                            <i class="text-xl text-yellow-500 cursor-pointer fas fa-bell hover:text-yellow-600"></i>
                            <p id="notifCount" class=" absolute right-0 -top-2 text-[11px] text-white">
                                @if ($unreadCount > 0)
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                @endif
                            </p>
                        </div>

                        {{-- name-section --}}
                        <div class="w-px h-8"></div>
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-100">{{ $mentorName }}</p>
                                <p class="text-xs text-gray-200">{{ $mentorEmail }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName) }}&background=0D8F81&color=fff&size=128" class="w-10 h-10 rounded-full">
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative p-1">
                @yield('content')


                <div id="notificationSideBar" class="notificationHide shadow flex flex-col bg-white fixed h-[89%] w-[20%] p-1 md:w-[30%] bottom-0 z-10">
                    <div class="flex items-center justify-between w-full p-3 ">
                        <p>Notifications</p>
                        <p id="closeNotif"><i class="text-xl text-red-600 fa-regular fa-circle-xmark"></i></p>
                    </div>
                    <div class="flex justify-between flex-1 w-full p-3 items-top">

                        {{-- notifications --}}
                        <div  class="flex flex-col w-full gap-2 text-sm ">


                            @if ($unreadNotifications->isEmpty())
                                <p class="mt-4 text-center text-gray-500">No notifications</p>

                            @else

                                @foreach ($unreadNotifications as $notification)

                                    {{-- notification --}}
                                    <div  class="grid w-full grid-cols-3 p-2 text-sm rounded list bg-gray-100/20">
                                        <div class="flex-col col-span-3 text">
                                            <div class="flex items-center justify-between w-full mb-2 div">
                                                <h1 class="font-semibold ">{{ $notification->data['title'] ?? 'Notification'}}</h1>
                                                <form  action="{{ route("mentor.notification.read", $notification->id )}}" method="POST" class="  bg-slate-800 rounded-3xl text-gray-200 hover:text-gray-100 cursor-pointer transition-all delay-75 text-[10px] p-1 px-3">
                                                    @csrf
                                                    <button type="submit">Mark as read</button>
                                                </form>
                                            </div>

                                            <p class="text-gray-500 wrap-break-word">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-sm text-gray-400 ">{{ $notification->created_at->diffForHumans()}}</p>
                                        </div>
                                    </div>

                                @endforeach

                            @endif



                       </div>

                    </div>

                    {{-- Mark all as read --}}
                    <div class="flex items-center justify-between w-full p-2 ">
                        <form  action="{{ route('mentor.notification.read-all')}}" method="POST" class="  bg-slate-800 rounded-3xl text-gray-200 hover:text-gray-100 cursor-pointer transition-all delay-75 text-[10px] p-2 px-3">
                                @csrf
                               <button type="submit"> Mark all as read</button>
                        </form>

                        <p class="text-sm text-gray-400 ">info@Tithandizane.com</p>
                    </div>

                </div>

                <div id="notificationPopUp" class="fixed z-50 space-y-2 text-xs bg-white select-none bottom-3 right-10 w-60 ">

                </div>

            </div>
        </div>


    </div>

    <script>
        // Fix: Use different IDs for different dropdowns to avoid conflicts
        // Chat dropdown
        const showIcon = document.getElementById('showIcon');
        const subList = document.getElementById('sub-list');

        if (showIcon && subList) {
            showIcon.addEventListener('click', function() {
                subList.classList.toggle('show');
                showIcon.classList.toggle('arrow-rotate');
            });
        }

        // Guidance dropdown
        const showIconGuidance = document.getElementById('showIconGuidance');
        const subListGuidance = document.getElementById('sub-list-guidance');

        if (showIconGuidance && subListGuidance) {
            showIconGuidance.addEventListener('click', function() {
                subListGuidance.classList.toggle('show');
                showIconGuidance.classList.toggle('arrow-rotate');
            });
        }

        //  Navigation active state management
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item');
            const currentPage = window.location.pathname.split('/').pop() || 'dashboard';

            // Set active based on current page
            navItems.forEach(item => {
                const page = item.getAttribute('data-page');
                if (page === currentPage) {
                    item.classList.add('bg-gray-800');
                }
            });
        });

        // notifications
        const notificationSideBar = document.getElementById('notificationSideBar');
        const closeNotif = document.getElementById('closeNotif');
        const bellIcon = document.querySelector('.fa-bell');

        if (bellIcon) {
            bellIcon.addEventListener('click', function() {
                if (notificationSideBar) {
                    notificationSideBar.classList.remove('notificationHide');
                    notificationSideBar.classList.add('notificationShow');
                }
            });
        }

        closeNotif.addEventListener('click', function() {
            notificationSideBar.classList.remove('notificationShow');
            notificationSideBar.classList.add('notificationHide');
        });


        // dropDownList logic for chat tab
        const showIcon = document.getElementById('showIcon');
        const subList = document.getElementById('sub-list');

        showIcon.addEventListener('click', function() {
            subList.classList.toggle('show');
            showIcon.classList.toggle('arrow-rotate');
        });


        // chat request broadcast channel
        const userId = {{ auth()->id() ?? 0 }};

        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                console.log("Echo loaded");

                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('New notification:', notification);

                        // increase badge
                        const countEl = document.getElementById('notifCount');
                        if (countEl) {
                            const count = parseInt(countEl.innerText) || 0;
                            countEl.innerText = count + 1;
                        }

                    // Show popup
                    const container = document.getElementById('notificationPopUp');
                    const div = document.createElement('div');
                    div.className = ' bg-white shadow p-3 rounded border-l-4 border-blue-500'

                    div.innerHTML = `
                        <p class= "font-bold "> ${notification.name} </p>
                        <p class= "text-sm font-bold text-gray-500 ">${notification.message} </p>
                    `;

                    container.appendChild(div);

                    setTimeout(() => {
                        div.remove();
                    }, 5000);

                    location.reload();
                });


            } else {
                console.log("Echo is not loaded or configured");
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
