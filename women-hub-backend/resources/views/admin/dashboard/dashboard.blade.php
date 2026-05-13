<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tithandizane Women Hub | Admin Dashboard</title>
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
        
        .nav-item { transition: all 0.2s ease; }
        
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
    </style>
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
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="dashboard" style="color: #FFFFFF; background: var(--blue);">
                <i class="fas fa-home w-5 text-white"></i>
                <span class="ml-3 font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.mentors.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="mentors" style="color: var(--sidebar-text);">
                <i class="fas fa-chalkboard-user w-5"></i>
                <span class="ml-3">Mentors</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="reports" style="color: var(--sidebar-text);">
                <i class="fas fa-flag w-5"></i>
                <span class="ml-3">Harassment Reports</span>
                <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full" style="background: var(--red); color: white;" id="pendingReportsBadge">0</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="guidance" style="color: var(--sidebar-text);">
                <i class="fas fa-book-open w-5"></i>
                <span class="ml-3">Guidance Content</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="users" style="color: var(--sidebar-text);">
                <i class="fas fa-user-circle w-5"></i>
                <span class="ml-3">Users</span>
                <span class="ml-auto text-xs px-2 py-0.5 rounded-full" style="background: var(--dark-blue); color: white;" id="totalUsersBadge">0</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="settings" style="color: var(--sidebar-text);">
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
                    <h2 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Welcome back, {{ Auth::guard('admin')->user()->name ?? 'Admin' }}</h2>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">Empowering women through mentorship & safety — Here's your live snapshot</p>
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
                    
                    <!-- Logout Button moved to top -->
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

        <div class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Total Mentors Card -->
                <div class="stat-card rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8 card-teal-border" style="background: var(--card-bg);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-wide" style="color: var(--text-secondary);">Total Mentors</p>
                            <p class="text-3xl font-extrabold mt-1" style="color: var(--text-primary);" id="statTotalMentors">0</p>
                        </div>
                        <div class="p-3 rounded-full" style="background: var(--light-teal);">
                            <i class="fas fa-chalkboard-user text-2xl text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-sm">
                        <span class="font-semibold text-success" id="newMentorsWeekStat">0</span> 
                        <span style="color: var(--text-secondary);">this week</span>
                    </div>
                </div>

                <!-- Active Mentors Card -->
                <div class="stat-card rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8 card-blue-border" style="background: var(--card-bg);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-wide" style="color: var(--text-secondary);">Active Mentors</p>
                            <p class="text-3xl font-extrabold" style="color: var(--text-primary);" id="statActiveMentors">0</p>
                        </div>
                        <div class="p-3 rounded-full" style="background: var(--light-blue);">
                            <i class="fas fa-user-check text-2xl text-info"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="w-full rounded-full h-2" style="background: var(--light-blue);">
                            <div class="h-2 rounded-full progress-bar-blue" style="width: 0%;" id="activePercentBar"></div>
                        </div>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);" id="activePercentText">0% of total mentors</p>
                    </div>
                </div>

                <!-- Pending Reports Card -->
                <div class="stat-card rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8 card-purple-border" style="background: var(--card-bg);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-wide" style="color: var(--text-secondary);">Pending Reports</p>
                            <p class="text-3xl font-extrabold" style="color: var(--text-primary);" id="statPendingReports">0</p>
                        </div>
                        <div class="p-3 rounded-full" style="background: var(--light-purple);">
                            <i class="fas fa-exclamation-triangle text-2xl text-purple"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-sm">
                        <span class="font-semibold text-purple" id="statInReview">0 in review</span>
                    </div>
                </div>

                <!-- Total Users Card -->
                <div class="stat-card rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8 card-orange-border" style="background: var(--card-bg);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-wide" style="color: var(--text-secondary);">Total Users</p>
                            <p class="text-3xl font-extrabold" style="color: var(--text-primary);" id="statTotalUsers">0</p>
                        </div>
                        <div class="p-3 rounded-full" style="background: var(--light-orange);">
                            <i class="fas fa-users text-2xl text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-sm">
                        <i class="fas fa-arrow-up text-success"></i> 
                        <span class="font-semibold text-success" id="userGrowthPercent">0%</span> 
                        <span style="color: var(--text-secondary);">from last month</span>
                    </div>
                </div>
            </div>

            <!-- Two column layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT: Quick actions + Add Mentor CTA -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Add Mentor CTA Card -->
                    <div class="rounded-2xl shadow-md overflow-hidden" style="background: var(--card-bg);">
                        <div class="p-5 border-b flex flex-wrap justify-between items-center" style="background: linear-gradient(90deg, var(--light-blue) 0%, var(--card-bg) 100%); border-color: var(--border-color);">
                            <div>
                                <h3 class="text-xl font-bold" style="color: var(--text-primary);">
                                    <i class="fas fa-user-plus mr-2 text-purple"></i>Mentor Management
                                </h3>
                                <p class="text-sm mt-1" style="color: var(--text-secondary);">Add and manage mentors on the platform</p>
                            </div>
                            <form method="GET" action="{{ route('admin.mentors.create') }}" style="display: inline;">
                                <button type="submit" class="transition px-5 py-2 rounded-xl text-sm shadow-sm flex items-center gap-2" style="background: var(--purple); color: white; cursor: pointer;">
                                    <i class="fas fa-plus-circle"></i> Add New Mentor
                                </button>
                            </form>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <a href="{{ route('admin.mentors.index') }}" class="mentor-box view-all flex flex-col items-center justify-center p-5 rounded-xl border-2 border-dashed transition group text-center" style="border-color: var(--border-color);">
                                    <i class="fas fa-list text-2xl mb-2 mentor-icon" style="color: var(--purple);"></i>
                                    <span class="text-sm font-semibold" style="color: var(--text-primary);">View All Mentors</span>
                                    <span class="text-xs mt-1" style="color: var(--text-secondary);">Browse full directory</span>
                                </a>
                                <a href="{{ route('admin.mentors.create') }}" class="mentor-box add-new flex flex-col items-center justify-center p-5 rounded-xl border-2 border-dashed transition group text-center" style="border-color: var(--border-color);">
                                    <i class="fas fa-user-plus text-2xl mb-2 mentor-icon text-success"></i>
                                    <span class="text-sm font-semibold" style="color: var(--text-primary);">Add New Mentor</span>
                                    <span class="text-xs mt-1" style="color: var(--text-secondary);">Register a mentor</span>
                                </a>
                                <a href="{{ route('admin.reports.index') }}" class="mentor-box pending flex flex-col items-center justify-center p-5 rounded-xl border-2 border-dashed transition group text-center" style="border-color: var(--border-color);">
                                    <i class="fas fa-clipboard-check text-2xl mb-2 mentor-icon text-purple"></i>
                                    <span class="text-sm font-semibold" style="color: var(--text-primary);">Pending Approvals</span>
                                    <span class="text-xs mt-1" style="color: var(--text-secondary);">Review mentor schedules</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- New Mentors This Week -->
                    <div class="rounded-2xl shadow-md" style="background: var(--card-bg);">
                        <div class="p-5 border-b" style="background: var(--light-teal); border-color: var(--border-color);">
                            <h3 class="font-bold" style="color: var(--text-primary);">
                                <i class="fas fa-seedling mr-2 text-success"></i> New Mentors
                            </h3>
                            <p class="text-xs" style="color: var(--text-secondary);">Joined this week</p>
                        </div>
                        <div id="newMentorsList" class="divide-y" style="border-color: var(--border-color);">
                            <div class="p-6 text-center" style="color: var(--text-secondary);">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2 text-purple"></i>
                                <p>Loading new mentors...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="rounded-2xl shadow-lg p-6 text-white empower-card">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-chalkboard fa-fw text-3xl text-white"></i>
                            <h3 class="text-xl font-bold">Empower a session</h3>
                        </div>
                        <p class="text-sm mt-2 opacity-90">Organize next mentor training or community circle</p>
                        <button class="mt-5 w-full bg-white font-semibold py-2.5 rounded-xl hover:bg-gray-100 transition flex items-center justify-center gap-2 shadow-md" style="color: var(--purple);" id="scheduleTrainingBtn">
                            <i class="fas fa-calendar-alt"></i> Schedule Training
                        </button>
                        <div class="mt-5 pt-2 border-t border-white/30 text-xs text-center opacity-80">45+ active community members this month</div>
                    </div>
                </div>

                <!-- RIGHT: Recent Reports -->
                <div class="space-y-7">
                    <!-- Recent Reports Card -->
                    <div class="rounded-2xl shadow-md overflow-hidden" style="background: var(--card-bg);">
                        <div class="p-5 border-b flex justify-between items-center" style="background: var(--light-purple); border-color: var(--border-color);">
                            <h3 class="font-bold" style="color: var(--text-primary);">
                                <i class="fas fa-flag-checkered mr-2 text-purple"></i> Recent Harassment Reports
                            </h3>
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold badge-purple" id="reportsCountBadge">0 new</span>
                        </div>
                        <div id="reportsListContainer" class="divide-y max-h-64 overflow-y-auto" style="border-color: var(--border-color);">
                            <div class="p-6 text-center" style="color: var(--text-secondary);">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2 text-purple"></i>
                                <p>Loading reports...</p>
                            </div>
                        </div>
                        <div class="p-3 border-t text-center" style="border-color: var(--border-color);">
                            <a href="{{ route('admin.reports.index') }}" class="text-sm font-medium hover:underline text-purple">Review all reports →</a>
                        </div>
                    </div>

                    <!-- Stats Summary Card -->
                    <div class="rounded-2xl shadow-md p-5" style="background: var(--card-bg);">
                        <h3 class="font-bold mb-4" style="color: var(--text-primary);">
                            <i class="fas fa-chart-pie mr-2 text-info"></i> Platform Summary
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border-color);">
                                <span class="text-sm" style="color: var(--text-secondary);">Total Mentors</span>
                                <span class="font-bold" style="color: var(--text-primary);" id="summaryTotalMentors">—</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border-color);">
                                <span class="text-sm" style="color: var(--text-secondary);">Active Mentors</span>
                                <span class="font-bold text-success" id="summaryActiveMentors">—</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--border-color);">
                                <span class="text-sm" style="color: var(--text-secondary);">Pending Approval</span>
                                <span class="font-bold text-warning" id="summaryPendingMentors">—</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm" style="color: var(--text-secondary);">Open Reports</span>
                                <span class="font-bold text-danger" id="summaryOpenReports">—</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.mentors.index') }}" class="mt-4 block text-center text-sm font-medium py-2.5 rounded-xl transition" style="background: var(--light-purple); color: var(--purple);">
                            <i class="fas fa-arrow-right mr-1"></i> Go to Mentor Directory
                        </a>
                    </div>
                </div>
            </div>
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
        
        if (isDark) {
            themeIcon.className = 'fas fa-sun';
            themeText.textContent = 'Light Mode';
        } else {
            themeIcon.className = 'fas fa-moon';
            themeText.textContent = 'Dark Mode';
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
    
    // Apply theme to all pages by storing in localStorage
    // When navigating to other pages, they can check localStorage.getItem('theme')
    
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function loadMentors(searchTerm = '') {
        const url = '{{ route("admin.mentors.index") }}' + (searchTerm ? `?search=${encodeURIComponent(searchTerm)}` : '');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.mentors && Array.isArray(data.mentors)) {
                const mentors = data.mentors;
                const totalMentors = mentors.length;
                const activeMentors = mentors.filter(m => m.status === 'active').length;
                const pendingMentors = mentors.filter(m => m.status === 'pending').length;
                const newThisWeek = mentors.filter(m => {
                    const createdDate = new Date(m.created_at);
                    const weekAgo = new Date();
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    return createdDate >= weekAgo;
                }).length;

                document.getElementById('statTotalMentors').innerText = totalMentors;
                document.getElementById('statActiveMentors').innerText = activeMentors;
                document.getElementById('newMentorsWeekStat').innerText = newThisWeek;

                const activePercent = totalMentors > 0 ? Math.round((activeMentors / totalMentors) * 100) : 0;
                document.getElementById('activePercentBar').style.width = `${activePercent}%`;
                document.getElementById('activePercentText').innerText = `${activePercent}% of total mentors`;

                document.getElementById('summaryTotalMentors').innerText = totalMentors;
                document.getElementById('summaryActiveMentors').innerText = activeMentors;
                document.getElementById('summaryPendingMentors').innerText = pendingMentors;

                renderNewMentors(mentors);
            }
        })
        .catch(error => {
            console.error('Error loading mentors:', error);
        });
    }

    function renderNewMentors(mentors) {
        const container = document.getElementById('newMentorsList');
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);

        const newMentors = mentors.filter(m => new Date(m.created_at) >= weekAgo).slice(0, 4);

        if (!newMentors || newMentors.length === 0) {
            container.innerHTML = '<div class="p-5 text-center" style="color: var(--text-secondary);">✨ No new mentors this week</div>';
            return;
        }

        container.innerHTML = newMentors.map(m => {
            const photoUrl = m.photo ? `/storage/${m.photo}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(m.name)}&background=9b59b6&color=fff`;
            return `
                <div class="p-4 flex items-center gap-3">
                    <img src="${photoUrl}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <p class="font-medium" style="color: var(--text-primary);">${escapeHtml(m.name)}</p>
                        <p class="text-xs" style="color: var(--text-secondary);">Joined ${new Date(m.created_at).toLocaleDateString()}</p>
                    </div>
                    <a href="/admin/mentors/${m.id}" class="ml-auto text-xs px-3 py-1 rounded-full" style="background: var(--light-teal); color: var(--teal-green);">View</a>
                </div>
            `;
        }).join('');
    }

    function loadRecentReports() {
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
                document.getElementById('statPendingReports').innerText = pendingReports;
                document.getElementById('pendingReportsBadge').innerText = pendingReports;
                document.getElementById('reportsCountBadge').innerText = `${pendingReports} new`;
                document.getElementById('notificationBadge').innerText = pendingReports;
                document.getElementById('summaryOpenReports').innerText = pendingReports;

                const inReview = data.reports.filter(r => r.status === 'in_review').length;
                document.getElementById('statInReview').innerHTML = `${inReview} in review`;

                renderRecentReports(data.reports.slice(0, 3));
            }
        })
        .catch(error => console.error('Error loading reports:', error));
    }

    function renderRecentReports(reports) {
        const container = document.getElementById('reportsListContainer');

        if (!reports || reports.length === 0) {
            container.innerHTML = '<div class="p-6 text-center" style="color: var(--text-secondary);">✅ No pending reports</div>';
            return;
        }

        container.innerHTML = reports.map(r => `
            <div class="p-4 transition">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="font-mono text-sm font-bold" style="color: var(--text-primary);">#${escapeHtml(r.id)}</span>
                        ${r.status === 'new' ? '<span class="ml-2 text-[10px] px-2 py-0.5 rounded-full" style="background: var(--light-red); color: var(--red);">New</span>' : ''}
                        ${r.status === 'in_review' ? '<span class="ml-2 text-[10px] px-2 py-0.5 rounded-full" style="background: var(--light-orange); color: var(--orange);">Review</span>' : ''}
                    </div>
                    <span class="text-xs" style="color: var(--text-secondary);">${new Date(r.created_at).toLocaleDateString()}</span>
                </div>
                <p class="text-sm mt-1 line-clamp-2" style="color: var(--text-secondary);">${escapeHtml(r.description?.substring(0, 80) || 'No description')}${r.description?.length > 80 ? '…' : ''}</p>
                <div class="mt-2"><span class="text-xs px-2 py-0.5 rounded-full" style="background: var(--light-gray); color: var(--text-secondary);">${escapeHtml(r.report_type || 'harassment')}</span></div>
            </div>
        `).join('');
    }

    function initNav() {
        const items = document.querySelectorAll('.nav-item');
        const currentPath = window.location.pathname;
        items.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath === href) {
                item.style.background = '#3498db';
                item.style.color = '#FFFFFF';
            }
        });
    }

    function initDashboard() {
        initNav();
        initTheme();
        loadMentors();
        loadRecentReports();

        document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);
        document.getElementById('scheduleTrainingBtn')?.addEventListener('click', () => {
            alert('📅 Schedule training: Calendar integration ready.');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDashboard();
    });
</script>
</body>
</html>