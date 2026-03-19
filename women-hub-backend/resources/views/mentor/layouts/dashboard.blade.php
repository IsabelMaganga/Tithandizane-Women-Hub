<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Tithandizane Women Hub</title>
    <link rel="short icon" href="{{ asset('images/Ellipse 3.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Left Sidebar - Dark Navigation -->
        <div class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="p-6">
                <h1 class="text-xl font-bold">Tithandizane</h1>
                <p class="text-sm text-gray-400">Women Hub</p>
            </div>

            <nav class="mt-6 flex-1" id="sidebar-nav">
                <a href="{{ route('mentor.dashboard')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="dashboard">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                    <i class="fa-regular fa-calendar w-5"></i>
                    <span class="ml-3">Appointments</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                    <i class="fa-regular fa-comment w-5"></i>
                    <span class="ml-3">chats</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                    <i class="fa-regular fa-circle-user w-5"></i>
                    <span class="ml-3">Profile</span>
                </a>
                <a href="{{ route('mentor.settings')}}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="settings">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Settings</span>
                </a>

                <!-- Logout Button -->
                <div class="mt-auto pt-6">
                    <a href="{{ route('mentor.logout') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-red-600 hover:text-white transition-colors" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('mentor.logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </nav>

            <div class="p-6 border-t border-gray-800">
                <div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName) }}&background=0D8F81&color=fff" class="w-10 h-10 rounded-full">
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ $mentorName }}</p>
                        <p class="text-xs text-gray-400">{{ $mentorEmail }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top Header - Like "Welcome back, Andrea" -->
            <div class="bg-white sticky top-0 z-30 shadow-2xl shadow-sm">
                <div class="flex justify-between items-center px-8 py-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Hi, {{ $mentorName }}</h2>
                        <p class="text-sm text-gray-500">Here's what's happening with your platform today</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-bell text-gray-500 text-xl cursor-pointer hover:text-blue-600"></i>
                        <i class="fas fa-envelope text-gray-500 text-xl cursor-pointer hover:text-blue-600"></i>
                        <div class="h-8 w-px bg-gray-300"></div>
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium">{{ $mentorName }}</p>
                                <p class="text-xs text-gray-500">{{ $mentorEmail }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName) }}&background=0D8F81&color=fff&size=128" class="w-10 h-10 rounded-full">
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                @yield('content')
            </div>
        </div>
    </div>

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

        // Notification and email icon hover effects
        document.querySelectorAll('.fa-bell, .fa-envelope').forEach(icon => {
            icon.addEventListener('mouseenter', function() {
                this.classList.add('text-blue-600');
            });
            icon.addEventListener('mouseleave', function() {
                this.classList.remove('text-blue-600');
            });
        });
    </script>
</body>
</html>
