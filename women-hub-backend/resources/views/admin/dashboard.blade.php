<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tithandizane Women Hub</title>
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
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="dashboard">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="mentors">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Mentors</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="reports">
                    <i class="fas fa-flag w-5"></i>
                    <span class="ml-3">Harassment Reports</span>
                    @if($pendingReports > 0)
                        <span class="ml-auto bg-red-500 text-xs px-2 py-1 rounded-full">{{ $pendingReports }}</span>
                    @endif
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="guidance">
                    <i class="fas fa-book-open w-5"></i>
                    <span class="ml-3">Guidance Content</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="users">
                    <i class="fas fa-user-circle w-5"></i>
                    <span class="ml-3">Users</span>
                    <span class="ml-auto bg-gray-700 text-xs px-2 py-1 rounded-full">{{ $totalUsers }}</span>
                </a>
                <a href="#" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 nav-item" data-page="settings">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Settings</span>
                </a>
                
                <!-- Logout Button -->
                <div class="mt-auto pt-6">
                    <a href="{{ route('admin.logout') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-red-600 hover:text-white transition-colors" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </nav>
            
            <div class="p-6 border-t border-gray-800">
                <div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($adminName) }}&background=0D8F81&color=fff" class="w-10 h-10 rounded-full">
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ $adminName }}</p>
                        <p class="text-xs text-gray-400">{{ $adminEmail }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top Header - Like "Welcome back, Andrea" -->
            <div class="bg-white shadow-sm">
                <div class="flex justify-between items-center px-8 py-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Welcome back, {{ $adminName }}</h2>
                        <p class="text-sm text-gray-500">Here's what's happening with your platform today</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-bell text-gray-500 text-xl cursor-pointer hover:text-blue-600"></i>
                        <i class="fas fa-envelope text-gray-500 text-xl cursor-pointer hover:text-blue-600"></i>
                        <div class="h-8 w-px bg-gray-300"></div>
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium">{{ $adminName }}</p>
                                <p class="text-xs text-gray-500">{{ $adminEmail }}</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($adminName) }}&background=0D8F81&color=fff&size=128" class="w-10 h-10 rounded-full">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <!-- Stats Cards - Like the design -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Mentors Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-full">
                                <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Total Mentors</p>
                                <p class="text-2xl font-bold">{{ $totalMentors }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-green-500 font-semibold">+{{ $newMentorsThisWeek }}</span>
                            <span class="text-gray-500 ml-2">this week</span>
                        </div>
                    </div>
                    
                    <!-- Active Mentors Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-full">
                                <i class="fas fa-user-check text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Active Mentors</p>
                                <p class="text-2xl font-bold">{{ $activeMentors }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $mentorCompletionRate }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $mentorCompletionRate }}% of total mentors</p>
                        </div>
                    </div>
                    
                    <!-- Pending Reports Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Pending Reports</p>
                                <p class="text-2xl font-bold">{{ $pendingReports }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <span class="text-yellow-500 font-semibold">{{ $inReviewReports }}</span>
                            <span class="text-gray-500 ml-2">in review</span>
                        </div>
                    </div>
                    
                    <!-- Total Users Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-full">
                                <i class="fas fa-users text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Total Users</p>
                                <p class="text-2xl font-bold">{{ $totalUsers }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm">
                            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                            <span class="text-green-500">12%</span>
                            <span class="text-gray-500 ml-2">from last month</span>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content Area - Two Columns Like Design -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Manage Mentors Section (2/3 width) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6 border-b flex justify-between items-center">
                                <h3 class="text-lg font-semibold">Manage Mentors</h3>
                                <a href="{{ route('admin.mentors.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>Add New Mentor
                                </a>
                            </div>
                            
                            <!-- Search Bar -->
                            <div class="p-6 border-b">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    <input type="text" placeholder="Search by name or expertise..." 
                                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                </div>
                            </div>
                            
                            <!-- Mentors Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expertise</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Availability</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($recentMentors as $mentor)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mentor->name) }}&background=random" class="w-8 h-8 rounded-full">
                                                    <div class="ml-3">
                                                        <p class="font-medium">{{ $mentor->name }}</p>
                                                        <p class="text-sm text-gray-500">{{ $mentor->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if(is_array($mentor->expertise))
                                                    @foreach(array_slice($mentor->expertise, 0, 2) as $exp)
                                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $exp }}</span>
                                                    @endforeach
                                                    @if(count($mentor->expertise) > 2)
                                                        <span class="text-xs text-gray-500">+{{ count($mentor->expertise) - 2 }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-500">Not specified</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                {{ is_array($mentor->availability) ? ($mentor->availability['schedule'] ?? 'Not set') : $mentor->availability }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($mentor->status == 'active')
                                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                                                @elseif($mentor->status == 'inactive')
                                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">Inactive</span>
                                                @else
                                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <button class="text-blue-600 hover:text-blue-800 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- View All Link -->
                            <div class="p-4 border-t text-center">
                                <a href="#" class="text-blue-600 text-sm hover:underline">View All Mentors</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Recent Reports & New Mentors (1/3 width) -->
                    <div class="space-y-6">
                        <!-- Recent Harassment Reports Card -->
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6 border-b flex justify-between items-center">
                                <h3 class="text-lg font-semibold">Recent Reports</h3>
                                @if($pendingReports > 0)
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">{{ $pendingReports }} new</span>
                                @endif
                            </div>
                            
                            <div class="divide-y">
                                @forelse($recentReports as $report)
                                <div class="p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-medium">{{ $report->report_id }}</span>
                                            @if($report->status == 'new')
                                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full ml-2">New</span>
                                            @elseif($report->status == 'in_review')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full ml-2">In Review</span>
                                            @endif
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1 truncate">{{ Str::limit($report->description, 60) }}</p>
                                    <div class="mt-2">
                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ ucfirst($report->report_type) }}</span>
                                    </div>
                                </div>
                                @empty
                                <div class="p-6 text-center text-gray-500">
                                    No recent reports
                                </div>
                                @endforelse
                            </div>
                            
                            <div class="p-4 border-t">
                                <a href="#" class="text-blue-600 text-sm hover:underline block text-center">View All Reports</a>
                            </div>
                        </div>
                        
                        <!-- New Mentors This Week Card -->
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6 border-b">
                                <h3 class="text-lg font-semibold">New Mentors</h3>
                                <p class="text-sm text-gray-500">Added this week</p>
                            </div>
                            
                            <div class="divide-y">
                                @forelse($recentMentors->take(3) as $mentor)
                                <div class="p-4 flex items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mentor->name) }}&background=random" class="w-10 h-10 rounded-full">
                                    <div class="ml-3">
                                        <p class="font-medium">{{ $mentor->name }}</p>
                                        <p class="text-sm text-gray-500">Added {{ $mentor->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @empty
                                <div class="p-6 text-center text-gray-500">
                                    No new mentors this week
                                </div>
                                @endforelse
                            </div>
                            
                            @if($newMentorsThisWeek > 3)
                            <div class="p-4 border-t text-center">
                                <span class="text-sm text-gray-500">+{{ $newMentorsThisWeek - 3 }} more</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Quick Stats Card -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow text-white p-6">
                            <h3 class="text-lg font-semibold mb-2">Quick Actions</h3>
                            <p class="text-sm opacity-90 mb-4">Setup next mentor training session</p>
                            <button class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100">
                                <i class="fas fa-calendar mr-2"></i>Schedule Training
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Navigation active state management
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item');
            const currentPage = window.location.pathname.split('/').pop() || 'dashboard';
            
            // Function to set active nav item
            function setActiveNavItem(activeItem) {
                navItems.forEach(item => {
                    item.classList.remove('bg-blue-600', 'border-l-4', 'border-blue-400');
                    item.classList.add('text-gray-300');
                });
                
                activeItem.classList.remove('text-gray-300');
                activeItem.classList.add('bg-blue-600', 'border-l-4', 'border-blue-400');
            }
            
            // Set active based on current page
            navItems.forEach(item => {
                const page = item.getAttribute('data-page');
                if (page === currentPage) {
                    setActiveNavItem(item);
                }
                
                // Add click handler
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    setActiveNavItem(this);
                    
                    // Here you would typically load the corresponding page content
                    // For now, we'll just update the URL
                    const page = this.getAttribute('data-page');
                    history.pushState({}, '', `/${page}`);
                });
            });
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