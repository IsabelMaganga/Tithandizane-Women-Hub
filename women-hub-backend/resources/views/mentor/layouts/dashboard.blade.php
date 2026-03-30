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


    @vite('resources/js/app.js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .sub-list{
            display: none;
        }
         .sub-list.show {
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

            <nav class="mt-0 flex-1 h-full relative text-sm overflow-auto mx-2 " id="sidebar-nav">
                <a href="{{ route('mentor.dashboard')}}" class="flex sticky top-0 bg-white rounded-3xl justify-center items-center px-6 text-center py-3 text-gray-600 hover:bg-gray-50 transition-all delay-75 ease-in-out nav-item" data-page="dashboard">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>

                {{-- appointments --}}
                <a href="{{ route('mentor.appointment')}}" class="flex items-center px-2 py-3 mt-3 border-t border-white/30 text-gray-300 hover:text-gray-50 hover:bg-gray-800 nav-item" data-page="guidance">
                    <i class="fa-regular fa-calendar w-5"></i>
                    <span class="ml-3">Appointments</span>
                </a>

                {{-- chat tab --}}
                <div class="div border-t border-white/30 ">
                    <div class="div flex justify-between items-center w-full">
                        <a class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="fa-regular fa-comment w-5"></i>
                            <span class="ml-0">chats</span>
                        </a>

                        <i id="showIcon" class="fas fa-chevron-down arrow-rotate mr-4 cursor-pointer transition-all"></i>
                     </div>

                    <div id="sub-list" class="sub-list text-[12px]">
                        <a href="{{ route('mentor.chat')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="fa-regular fa-comment w-5"></i>
                            <span class="ml-3">MentorShipSessions</span>
                        </a>
                        <a href="{{ route('mentor.group')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="fa-regular fa-comment w-5"></i>
                            <span class="ml-3">Create group</span>
                        </a>
                        <a href="{{ route('mentor.groups')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="fa-regular fa-comment w-5"></i>
                            <span class="ml-3">Groups</span>
                        </a>
                    </div>

                </div>

                {{-- Guidance tab --}}
                <div class="div border-t border-white/30 ">
                    <div class="div flex justify-between items-center w-full">
                        <a href="{{ route('mentor.Guidance')}}" class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="fa-solid fa-circle-info w-5"></i>
                            <span class="ml-0 capitalize">guidance content</span>
                        </a>

                        <i id="showIcon" class="fas fa-chevron-down arrow-rotate mr-4 cursor-pointer transition-all"></i>
                     </div>

                    <div id="sub-list" class=" text-[12px]">
                        <a href="{{ route('mentor.hygiene')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="hygiene">
                            <i class="fa-solid fa-pump-medical w-5"></i>
                            <span class="ml-3">hygiene</span>
                        </a>
                        <a href="{{ route('mentor.general')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="General">
                            <i class="fa-solid fa-house-medical w-5"></i>
                            <span class="ml-3 capitalize">General</span>
                        </a>
                        <a href="{{ route('mentor.emergency')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="emergency">
                            <i class="fa-solid fa-user-injured w-5"></i>
                            <span class="ml-3">Emergency    </span>
                        </a>
                    </div>

                </div>

                {{-- calender --}}
                <a  href="{{ route('mentor.calender')}}" class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item">
                    <i class="fa-regular fa-calendar w-5"></i>
                    <span class="ml-3">Calender</span>
                </a>

                {{-- report --}}
                <div class="div border-t border-white/30 ">
                        <a  href="{{ route('mentor.reports')}}" class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                            <i class="fa-solid fa-user-astronaut w-5"></i>
                            <span class="ml-0 capitalize">Report system</span>
                        </a>

                </div>

                {{-- profile tab --}}
                <a href="{{ route('mentor.profile')}}" class="flex border-t border-white/30 items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                    <i class="fa-regular fa-circle-user w-5"></i>
                    <span class="ml-3">Profile</span>
                </a>

                {{-- settings tab --}}
                <a href="{{ route('mentor.settings')}}" class="flex items-center px-2 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="settings">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Settings</span>
                </a>

            </nav>

            <!-- Logout Button -->
            <div class="mt-auto text-sm pt-6">
                <a href="{{ route('mentor.logout') }}" class="flex bg-red-600 items-center px-6 py-3 text-gray-300 hover:bg-red-700 hover:text-white transition-colors" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span class="ml-3">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('mentor.logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

            {{-- <div class="py-5 px-2 wrap-break-word border-t border-gray-800">
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
                <div class="flex justify-between items-center px-8 py-4">

                    <div class="flex items-center space-x-4">
                        {{-- notifications --}}
                        <div class=" relative notifications px-2">
                            <i class="fas fa-bell text-yellow-500 text-xl cursor-pointer hover:text-yellow-600"></i>
                            <p class=" absolute right-0 -top-2 text-[11px] text-white">2</p>
                        </div>

                        {{-- mails --}}
                        <div class=" relative notifications px-2">
                            <i class="fas fa-envelope text-gray-200 text-xl cursor-pointer hover:text-gray-100"></i>
                            <p class=" absolute right-0 -top-2 text-[11px] text-white">2</p>
                        </div>

                        {{-- name-section --}}
                        <div class="h-8 w-px"></div>
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm text-gray-100 font-medium">{{ $mentorName }}</p>
                                <p class="text-xs text-gray-200">{{ $mentorEmail }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName) }}&background=0D8F81&color=fff&size=128" class="w-10 h-10 rounded-full">
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 relative">
                @yield('content')


                <div id="notificationSideBar" class="notificationHide shadow flex flex-col bg-white fixed h-[89%] w-[20%] p-1 md:w-[30%] bottom-0 z-10">
                    <div class=" p-3 flex justify-between items-center w-full">
                        <p>Notifications</p>
                        <p id="closeNotif"><i class="fa-regular fa-circle-xmark text-red-600 text-xl"></i></p>
                    </div>
                    <div class=" p-3 flex flex-1 justify-between items-top w-full">

                        {{-- notifications --}}
                        <div  class=" text-sm h-full flex  flex-col overflow-auto w-full">

                            {{-- @foreach (auth()->user()->notifications as $notification) --}}

                            {{-- notification --}}
                            <div  class="list text-sm grid rounded bg-gray-100/20 p-2 grid-cols-3 w-full">
                                <div class="text flex-col col-span-3">
                                    <div class="div flex justify-between items-center w-full">
                                        <h1 class=" font-semibold">New notification</h1>
                                        <h1 class="  bg-slate-800 rounded-3xl text-gray-200 hover:text-gray-100 cursor-pointer transition-all delay-75 text-[10px] p-2">Mark as read</h1>
                                    </div>

                                    {{-- <p class=" wrap-break-word text-gray-500">{{ $notification->data['message'] }}</p> --}}
                                    {{-- <p class=" text-sm text-gray-400">{{ $notification->data['times']}}</p> --}}
                                </div>
                            </div>

                            {{-- @endforeach --}}

                       </div>

                    </div>
                    <div class=" p-2 flex justify-center items-center w-full">
                        <p class=" text-sm text-gray-400">info@Tithandizane.com</p>
                    </div>

                </div>

                <div id="notificationChatSideBar" class="notificationChatHide select-none shadow flex flex-col bg-white fixed h-[89%] w-[20%] p-1 md:w-[30%] bottom-0 z-10">
                    <div class=" p-3 flex justify-end items-center w-full">
                        <p id="close"><i class="fa-regular fa-circle-xmark text-red-600 text-xl"></i></p>
                    </div>
                    <div class=" p-3 flex flex-1 justify-between items-top w-full">

                        {{-- notifications --}}
                        <div  class=" text-sm h-full flex  flex-col overflow-auto w-full">

                            @for ($i = 0; $i < 2; $i++)

                            {{-- notification --}}
                            <div  class="list text-sm grid rounded bg-gray-100/20 p-2 grid-cols-3 w-full">
                                <div class="text flex-col col-span-3">
                                    <div class="div flex justify-between items-center w-full">
                                        <h1 class=" font-semibold">Chat request</h1>
                                        <h1 class="  bg-slate-800 rounded-3xl text-gray-200 hover:text-gray-100 cursor-pointer transition-all delay-75 text-[12px] p-2">View Now</h1>
                                    </div>

                                    <p class=" wrap-break-word text-gray-500">From Isabella: Love issues</p>
                                    <p class=" text-sm text-gray-400">12:00 am</p>
                                </div>
                            </div>

                            @endfor
                       </div>

                    </div>
                    <div class=" p-2 flex justify-center items-center w-full">
                        <p class=" text-sm text-gray-400">info@Tithandizane.com</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- <script src=" {{ asset('resources/js/app.js')}}"></script> --}}
    <script>
        // Navigation active state management
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item');
            const currentPage = window.location.pathname.split('/').pop() || 'dashboard';

            // Function to set active nav item
            // function setActiveNavItem(activeItem) {
            //     navItems.forEach(item => {
            //         item.classList.remove('bg-blue-600', 'border-l-4', 'border-blue-400');
            //         item.classList.add('text-gray-300');
            //     });

            //     activeItem.classList.remove('text-gray-300');
            //     activeItem.classList.add('bg-blue-600', 'border-l-4', 'border-blue-400');
            // }

            // Set active based on current page
            // navItems.forEach(item => {
            //     const page = item.getAttribute('data-page');
            //     if (page === currentPage) {
            //         setActiveNavItem(item);
            //     }

            //     // Add click handler
            //     item.addEventListener('click', function(e) {
            //         e.preventDefault();
            //         // setActiveNavItem(this);

            //         const page = this.getAttribute('data-page');
            //     });
            // });
        });

        // random notifications
        notificationSideBar = document.getElementById('notificationSideBar');
        closeBtn = document.getElementById('close');
        closeNotif = document.getElementById('closeNotif');

        document.querySelector('.fa-bell ').addEventListener('click', function() {

            notificationSideBar.classList.remove('notificationHide');
            notificationSideBar.classList.add('notificationShow');
            notificationChatSideBar.classList.add('notificationChatHide');
        });

        closeBtn.addEventListener('click', function() {
            notificationChatSideBar.classList.add('notificationChatHide');
            notificationChatSideBar.classList.remove('notificationChatShow');
        });

        closeNotif.addEventListener('click', function() {
            notificationSideBar.classList.remove('notificationShow');
            notificationSideBar.classList.add('notificationHide');
        });


        // chat logic for notification
        notificationChatSideBar = document.getElementById('notificationChatSideBar');

        document.querySelector('.fa-envelope ').addEventListener('click', function() {

            notificationChatSideBar.classList.remove('notificationChatHide');
            notificationChatSideBar.classList.add('notificationChatShow');
            notificationSideBar.classList.add('notificationHide');
        });

        // dropDownList logic for chat tab
        const showIcon = document.getElementById('showIcon');
        const subList = document.getElementById('sub-list');

        showIcon.addEventListener('click', function() {
            subList.classList.toggle('show');
            showIcon.classList.toggle('arrow-rotate');
        });

        // alert('test');

    </script>

    @stack('scripts')
</body>
</html>
