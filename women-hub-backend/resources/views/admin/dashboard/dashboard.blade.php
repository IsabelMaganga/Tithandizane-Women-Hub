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
        /* Custom smooth transitions & custom scrollbar */
        body {
            background: #F8FAFE;
            font-family: system-ui, 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #E2E8F0;
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #3B59A8;
            border-radius: 10px;
        }
        .hover-scale {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-scale:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.15);
        }
        .card-shadow {
            box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 6px rgba(0,0,0,0.05);
        }
        .nav-item {
            transition: all 0.2s ease;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        input:focus, button:focus {
            outline: none;
            box-shadow: 0 0 0 2px #5CB8E4;
        }
        .stat-card {
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .empower-card {
            background: linear-gradient(135deg, #3B59A8 0%, #5CB8E4 100%);
        }
    </style>
</head>
<body class="font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    
    <div class="w-64 flex flex-col shadow-xl" style="background: #874179; border-right: 1px solid #6d3661;">
        <div class="p-6 border-b" style="border-color: #6d3661;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo2.png') }}" alt="Tithandizane Logo" class="w-12 h-12 rounded-full object-cover shadow-md border-2 border-white/30">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">Tithandizane</h1>
                    <p class="text-xs mt-1 opacity-90 text-white">Women Hub</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 mt-6 space-y-1 px-3" id="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="dashboard" style="color: #FFFFFF; background: #2C4A8C;">
                <i class="fas fa-home w-5 text-white"></i>
                <span class="ml-3 font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.mentors.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="mentors" style="color: #E2E8F0;">
                <i class="fas fa-chalkboard-user w-5" style="color: #8BC34A;"></i>
                <span class="ml-3">Mentors</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="reports" style="color: #E2E8F0;">
                <i class="fas fa-flag w-5" style="color: #9C27B0;"></i>
                <span class="ml-3">Harassment Reports</span>
                <span class="ml-auto bg-rose-600 text-white text-xs font-bold px-2 py-0.5 rounded-full" id="pendingReportsBadge">0</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="guidance" style="color: #E2E8F0;">
                <i class="fas fa-book-open w-5" style="color: #4CAF50;"></i>
                <span class="ml-3">Guidance Content</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="users" style="color: #E2E8F0;">
                <i class="fas fa-user-circle w-5" style="color: #5CB8E4;"></i>
                <span class="ml-3">Users</span>
                <span class="ml-auto bg-gray-600 text-xs px-2 py-0.5 rounded-full" id="totalUsersBadge">0</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="settings" style="color: #E2E8F0;">
                <i class="fas fa-cog w-5" style="color: #8BC34A;"></i>
                <span class="ml-3">Settings</span>
            </a>
        </nav>

        <div class="pt-8 mt-auto px-3 pb-6">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg transition hover:bg-rose-800/50 text-stone-200 hover:text-white">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span class="ml-3">Logout</span>
                </button>
            </form>
        </div>

        <!-- Admin user card -->
        <div class="p-5 m-3 rounded-xl mb-6" style="background: #6d3661; border: 1px solid #af5c9c;">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name ?? 'Admin User') }}&background=5CB8E4&color=fff&bold=true&size=40" class="w-10 h-10 rounded-full border-2 border-white" id="adminAvatarImg">
                <div class="ml-3">
                    <p class="text-sm font-semibold text-white" id="adminNameDisplay">{{ Auth::guard('admin')->user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-white/80" id="adminEmailDisplay">{{ Auth::guard('admin')->user()->email ?? 'admin@tithandizane.org' }}</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="flex-1 overflow-y-auto custom-scroll" style="background: #F8FAFE;">
        
        <!-- Top welcome bar -->
        <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm shadow-sm border-b" style="border-color: #E2E8F0;">
            <div class="flex justify-between items-center px-8 py-5 flex-wrap gap-3">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900" id="welcomeMessage">Welcome back, {{ Auth::guard('admin')->user()->name ?? 'Admin' }} </h2>
                    <p class="text-sm mt-1 text-gray-700">Empowering women through mentorship & safety Here's your live snapshot</p>
                </div>
                <div class="flex items-center gap-5">
                    <div class="relative">
                        <i class="fas fa-bell text-2xl cursor-pointer transition text-gray-600 hover:text-#3B59A8"></i>
                        <span class="absolute -top-1 -right-2 bg-rose-500 text-white text-[10px] rounded-full px-1.5" id="notificationBadge">0</span>
                    </div>
                    <i class="fas fa-envelope text-2xl cursor-pointer text-gray-600 hover:text-#3B59A8"></i>
                    <div class="h-8 w-px bg-gray-300"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800" id="topAdminName">{{ Auth::guard('admin')->user()->name ?? 'Admin User' }}</p>
                            <p class="text-xs text-gray-600">Lead Administrator</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('admin')->user()->name ?? 'Admin User') }}&background=5CB8E4&color=fff&size=48" class="w-11 h-11 rounded-full border-2 border-#3B59A8" id="topAdminAvatar">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="stat-card bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #8BC34A;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-wide text-gray-600">Total Mentors</p>
                            <p class="text-3xl font-extrabold mt-1 text-gray-900" id="statTotalMentors">0</p>
                        </div>
                        <div class="p-3 rounded-full" style="background: #F1F8E9;"><i class="fas fa-chalkboard-user text-2xl" style="color: #8BC34A;"></i></div>
                    </div>
                    <div class="mt-3 text-sm"><span class="font-semibold text-green-700" id="newMentorsWeekStat">0</span> <span class="text-gray-600">this week</span></div>
                </div>
                <div class="stat-card bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #5CB8E4;">
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium uppercase text-gray-600">Active Mentors</p><p class="text-3xl font-extrabold text-gray-900" id="statActiveMentors">0</p></div>
                        <div class="p-3 rounded-full" style="background: #E6F7FF;"><i class="fas fa-user-check text-2xl" style="color: #5CB8E4;"></i></div>
                    </div>
                    <div class="mt-2"><div class="w-full bg-blue-100 rounded-full h-2"><div class="h-2 rounded-full" style="width: 0%; background: #5CB8E4;" id="activePercentBar"></div></div><p class="text-xs text-gray-600 mt-1" id="activePercentText">0% of total mentors</p></div>
                </div>
                <div class="stat-card bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #9C27B0;">
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium uppercase text-gray-600">Pending Reports</p><p class="text-3xl font-extrabold text-gray-900" id="statPendingReports">0</p></div>
                        <div class="p-3 rounded-full" style="background: #F3E5F5;"><i class="fas fa-exclamation-triangle text-2xl" style="color: #9C27B0;"></i></div>
                    </div>
                    <div class="mt-3 text-sm"><span class="font-semibold text-purple-700" id="statInReview">0 in review</span></div>
                </div>
                <div class="stat-card bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #3B59A8;">
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium uppercase text-gray-600">Total Users</p><p class="text-3xl font-extrabold text-gray-900" id="statTotalUsers">0</p></div>
                        <div class="p-3 rounded-full" style="background: #E8EAF6;"><i class="fas fa-users text-2xl" style="color: #3B59A8;"></i></div>
                    </div>
                    <div class="mt-3 text-sm"><i class="fas fa-arrow-up text-green-600"></i> <span class="text-green-700 font-semibold" id="userGrowthPercent">0%</span> <span class="text-gray-600">from last month</span></div>
                </div>
            </div>

            <!-- Two column layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT: Manage Mentors Table -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="p-5 border-b border-gray-100 flex flex-wrap justify-between items-center" style="background: linear-gradient(90deg, #F0F4FF 0%, #FFFFFF 100%);">
                            <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-hands-helping mr-2" style="color: #3B59A8;"></i>Mentor Directory</h3>
                            {{-- CRITICAL FIX: Use a form instead of an anchor tag to avoid AJAX interception --}}
                            <form method="GET" action="{{ route('admin.mentors.create') }}" style="display: inline;" id="addMentorForm">
                                <button type="submit" class="transition px-5 py-2 rounded-xl text-sm shadow-sm flex items-center gap-2" style="background: #874179; color: white; cursor: pointer;">
                                    <i class="fas fa-plus-circle"></i> Add New Mentor
                                </button>
                            </form>
                        </div>
                        <div class="p-5 border-b">
                            <div class="relative">
                                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                                <input type="text" id="mentorSearch" placeholder="Search by name, expertise..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-#3B59A8 focus:ring-1 focus:ring-#3B59A8 bg-gray-50">
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50/90">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Mentor</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Expertise</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Availability</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="mentorsTableBody" class="divide-y divide-gray-100">
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-gray-500">
                                            <div class="flex flex-col items-center gap-2">
                                                <i class="fas fa-spinner fa-spin text-2xl" style="color: #874179;"></i>
                                                <p>Loading mentors...</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 text-center border-t">
                            {{-- FIX: Link to the mentorList.blade.php page via the index route --}}
                            <a href="{{ route('admin.mentors.index') }}" class="font-medium hover:underline inline-flex items-center gap-1" style="color: #874179;">
                                View all mentors <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Recent Reports + New Mentors + Quick Actions -->
                <div class="space-y-7">
                    <!-- Recent Reports Card -->
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="p-5 border-b flex justify-between items-center" style="background: #F0F4FF;">
                            <h3 class="font-bold text-gray-800"><i class="fas fa-flag-checkered mr-2" style="color: #9C27B0;"></i> Recent Harassment Reports</h3>
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold" style="background: #F3E5F5; color: #9C27B0;" id="reportsCountBadge">0 new</span>
                        </div>
                        <div id="reportsListContainer" class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                            <div class="p-6 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2" style="color: #874179;"></i>
                                <p>Loading reports...</p>
                            </div>
                        </div>
                        <div class="p-3 border-t text-center">
                            <a href="{{ route('admin.reports.index') }}" class="text-sm font-medium hover:underline" style="color: #874179;">Review all reports →</a>
                        </div>
                    </div>

                    <!-- New Mentors This Week -->
                    <div class="bg-white rounded-2xl shadow-md">
                        <div class="p-5 border-b border-gray-100" style="background: #F1F8E9;">
                            <h3 class="font-bold text-gray-800"><i class="fas fa-seedling mr-2" style="color: #8BC34A;"></i> New Mentors</h3>
                            <p class="text-xs text-gray-600">Joined this week</p>
                        </div>
                        <div id="newMentorsList" class="divide-y divide-gray-100">
                            <div class="p-6 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2" style="color: #874179;"></i>
                                <p>Loading new mentors...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="rounded-2xl shadow-lg p-6 text-white empower-card">
                        <div class="flex items-center gap-3"><i class="fas fa-chalkboard fa-fw text-3xl" style="color: #FFFFFF;"></i><h3 class="text-xl font-bold">Empower a session</h3></div>
                        <p class="text-sm mt-2 opacity-90">Organize next mentor training or community circle</p>
                        <button class="mt-5 w-full bg-white text-[#874179] font-semibold py-2.5 rounded-xl hover:bg-gray-100 transition flex items-center justify-center gap-2 shadow-md" id="scheduleTrainingBtn"><i class="fas fa-calendar-alt"></i> Schedule Training</button>
                        <div class="mt-5 pt-2 border-t border-white/30 text-xs text-center opacity-80">45+ active community members this month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Helper functions
    function escapeHtml(str) { 
        if(!str) return ''; 
        return str.replace(/[&<>]/g, function(m){ 
            if(m === '&') return '&amp;'; 
            if(m === '<') return '&lt;'; 
            if(m === '>') return '&gt;'; 
            return m;
        }); 
    }
    
    // Load mentors using AJAX (for the dashboard table)
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
            if(data.mentors && Array.isArray(data.mentors)) {
                const mentors = data.mentors;
                const totalMentors = mentors.length;
                const activeMentors = mentors.filter(m => m.status === 'active').length;
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
                
                renderMentorsTable(mentors);
                renderNewMentors(mentors);
            }
        })
        .catch(error => {
            console.error('Error loading mentors:', error);
            const tbody = document.getElementById('mentorsTableBody');
            if(tbody) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-red-500">Error loading mentors. Please refresh the page.</td></tr>';
            }
        });
    }
    
    function renderMentorsTable(mentors) {
        const tbody = document.getElementById('mentorsTableBody');
        if(!tbody) return;
        
        if(!mentors || mentors.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">🌱 No mentors found</td></tr>';
            return;
        }
        
        // Show only first 5 mentors in dashboard
        const displayMentors = mentors.slice(0, 5);
        
        tbody.innerHTML = displayMentors.map(mentor => {
            let expertiseArray = [];
            if (mentor.expertise) {
                try {
                    expertiseArray = typeof mentor.expertise === 'string' ? JSON.parse(mentor.expertise) : mentor.expertise;
                } catch(e) {
                    expertiseArray = [];
                }
            }
            
            const photoUrl = mentor.photo ? `/storage/${mentor.photo}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=874179&color=fff&size=36`;
            
            return `<tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="${photoUrl}" class="w-9 h-9 rounded-full border border-gray-200 object-cover">
                        <div>
                            <p class="font-semibold text-gray-800">${escapeHtml(mentor.name)}</p>
                            <p class="text-xs text-gray-500">${escapeHtml(mentor.email)}</p>
                        </div>
                    </div>
                  </td>
                <td class="px-6 py-4">
                    ${expertiseArray.slice(0, 2).map(e => `<span class="inline-block text-xs px-2 py-1 rounded-full mr-1 mb-1" style="background: #E8EAF6; color: #3B59A8;">${escapeHtml(e)}</span>`).join('')}
                    ${expertiseArray.length > 2 ? `<span class="inline-block text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-600">+${expertiseArray.length - 2}</span>` : ''}
                  </td>
                <td class="px-6 py-4 text-sm text-gray-700">${escapeHtml(mentor.availability || 'Not specified')}</td>
                <td class="px-6 py-4">
                    ${mentor.status === 'active' ? '<span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Active</span>' : 
                      (mentor.status === 'inactive' ? '<span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">Inactive</span>' : 
                      '<span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-full">Pending</span>')}
                  </td>
                <td class="px-6 py-4 text-right">
                    <a href="/admin/mentors/${mentor.id}" class="mr-3 inline-block" style="color: #3B59A8;" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/admin/mentors/${mentor.id}/edit" class="mr-3 inline-block" style="color: #3B59A8;" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="delete-mentor" data-id="${mentor.id}" style="color: #9C27B0;" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                  </td>
              </tr>`;
        }).join('');
        
        attachMentorActions();
    }
    
    function attachMentorActions() {
        document.querySelectorAll('.delete-mentor').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const mentorId = btn.dataset.id;
                if(confirm('Remove mentor permanently? This action cannot be undone.')) {
                    fetch(`/admin/mentors/${mentorId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(() => {
                        alert('Mentor deleted successfully');
                        loadMentors(document.getElementById('mentorSearch')?.value || '');
                    })
                    .catch(error => console.error('Error deleting mentor:', error));
                }
            });
        });
    }
    
    function renderNewMentors(mentors) {
        const container = document.getElementById('newMentorsList');
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        
        const newMentors = mentors.filter(m => new Date(m.created_at) >= weekAgo).slice(0, 4);
        
        if(!newMentors || newMentors.length === 0) {
            container.innerHTML = '<div class="p-5 text-center text-gray-500">✨ No new mentors this week</div>';
            return;
        }
        
        container.innerHTML = newMentors.map(m => {
            const photoUrl = m.photo ? `/storage/${m.photo}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(m.name)}&background=874179&color=fff`;
            return `
                <div class="p-4 flex items-center gap-3">
                    <img src="${photoUrl}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <p class="font-medium text-gray-800">${escapeHtml(m.name)}</p>
                        <p class="text-xs text-gray-500">Joined ${new Date(m.created_at).toLocaleDateString()}</p>
                    </div>
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
            if(data.reports && Array.isArray(data.reports)) {
                const pendingReports = data.reports.filter(r => r.status === 'new' || r.status === 'in_review').length;
                document.getElementById('statPendingReports').innerText = pendingReports;
                document.getElementById('pendingReportsBadge').innerText = pendingReports;
                document.getElementById('reportsCountBadge').innerText = `${pendingReports} new`;
                document.getElementById('notificationBadge').innerText = pendingReports;
                
                const inReview = data.reports.filter(r => r.status === 'in_review').length;
                document.getElementById('statInReview').innerHTML = `${inReview} in review`;
                
                renderRecentReports(data.reports.slice(0, 3));
            }
        })
        .catch(error => console.error('Error loading reports:', error));
    }
    
    function renderRecentReports(reports) {
        const container = document.getElementById('reportsListContainer');
        
        if(!reports || reports.length === 0) {
            container.innerHTML = '<div class="p-6 text-center text-gray-500">✅ No pending reports</div>';
            return;
        }
        
        container.innerHTML = reports.map(r => `
            <div class="p-4 hover:bg-blue-50/40 transition">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="font-mono text-sm font-bold text-gray-800">#${escapeHtml(r.id)}</span>
                        ${r.status === 'new' ? '<span class="ml-2 bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded-full">New</span>' : ''}
                        ${r.status === 'in_review' ? '<span class="ml-2 bg-yellow-100 text-yellow-700 text-[10px] px-2 py-0.5 rounded-full">Review</span>' : ''}
                    </div>
                    <span class="text-xs text-gray-500">${new Date(r.created_at).toLocaleDateString()}</span>
                </div>
                <p class="text-sm text-gray-700 mt-1 line-clamp-2">${escapeHtml(r.description?.substring(0, 80) || 'No description')}${r.description?.length > 80 ? '…' : ''}</p>
                <div class="mt-2"><span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded-full">${escapeHtml(r.report_type || 'harassment')}</span></div>
            </div>
        `).join('');
    }
    
    function initNav() {
        const items = document.querySelectorAll('.nav-item');
        const currentPath = window.location.pathname;
        
        items.forEach(item => {
            const href = item.getAttribute('href');
            if (href && currentPath === href) {
                item.style.background = '#2C4A8C';
                item.style.color = '#FFFFFF';
            } else if (item.getAttribute('data-page') === 'dashboard' && currentPath === '/admin/dashboard') {
                item.style.background = '#2C4A8C';
                item.style.color = '#FFFFFF';
            }
        });
    }
    
    function initDashboard() {
        initNav();
        loadMentors();
        loadRecentReports();
        
        const searchInput = document.getElementById('mentorSearch');
        if(searchInput) {
            searchInput.addEventListener('input', (e) => loadMentors(e.target.value.toLowerCase()));
        }
        
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