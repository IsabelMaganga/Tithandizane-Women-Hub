<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Tithandizane Women Hub | Admin Dashboard</title>
    <!-- Tailwind + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js CDN for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Custom smooth transitions & custom scrollbar */
        body {
            background: #F8FAFE;  /* Light, cool background to complement Deep Blue */
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
            background: #3B59A8;  /* Deep Blue */
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
            ring: 2px solid #5CB8E4;  /* Bright Blue focus ring */
        }
        .stat-card {
            background: #ffffff;
            transition: all 0.2s ease;
        }
        /* Custom gradient for the quick actions card using Deep Blue and Bright Blue */
        .empower-card {
            background: linear-gradient(135deg, #3B59A8 0%, #5CB8E4 100%);
        }
    </style>
</head>
<body class="font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- ================= LEFT SIDEBAR - Deep Blue Dominant ================= -->
      <div class="w-64 flex flex-col shadow-xl" style="background: #874179; border-right: 1px solid #6d3661;">
       <div class="p-6 border-b" style="border-color: #6d3661;">
            <h1 class="text-2xl font-bold tracking-tight text-white">Tithandizane</h1>
            <p class="text-xs mt-1 opacity-90 text-white">Women Hub</p>
        </div>

        <nav class="flex-1 mt-6 space-y-1 px-3" id="sidebar-nav">
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="dashboard" style="color: #FFFFFF; background: #2C4A8C;">
                <i class="fas fa-home w-5 text-white"></i>
                <span class="ml-3 font-medium">Dashboard</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="mentors" style="color: #E2E8F0;">
                <i class="fas fa-chalkboard-user w-5" style="color: #8BC34A;"></i> <!-- Lime Green -->
                <span class="ml-3">Mentors</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="reports" style="color: #E2E8F0;">
                <i class="fas fa-flag w-5" style="color: #9C27B0;"></i> <!-- Purple/Magenta -->
                <span class="ml-3">Harassment Reports</span>
                <span class="ml-auto bg-rose-600 text-white text-xs font-bold px-2 py-0.5 rounded-full" id="pendingReportsBadge">0</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="guidance" style="color: #E2E8F0;">
                <i class="fas fa-book-open w-5" style="color: #4CAF50;"></i> <!-- Vibrant Green -->
                <span class="ml-3">Guidance Content</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="users" style="color: #E2E8F0;">
                <i class="fas fa-user-circle w-5" style="color: #5CB8E4;"></i> <!-- Bright Blue -->
                <span class="ml-3">Users</span>
                <span class="ml-auto bg-gray-600 text-xs px-2 py-0.5 rounded-full" id="totalUsersBadge">0</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="settings" style="color: #E2E8F0;">
                <i class="fas fa-cog w-5" style="color: #8BC34A;"></i> <!-- Lime Green -->
                <span class="ml-3">Settings</span>
            </a>
            <div class="pt-8 mt-auto">
                <button type="button" onclick="handleLogout()" class="w-full flex items-center px-4 py-3 rounded-lg transition hover:bg-rose-800/50 text-stone-200 hover:text-white">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span class="ml-3">Logout</span>
                </button>
            </div>
        </nav>

        <!-- Admin user card with Deep Blue tones -->
        <div class="p-5 m-3 rounded-xl mt-2" style="background: #6d3661; border: 1px solid #af5c9c;">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name=Admin+User&background=5CB8E4&color=fff&bold=true&size=40" class="w-10 h-10 rounded-full border-2 border-white" id="adminAvatarImg">
                <div class="ml-3">
                    <p class="text-sm font-semibold text-white" id="adminNameDisplay">Admin User</p>
                    <p class="text-xs text-white/80" id="adminEmailDisplay">admin@tithandizane.org</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="flex-1 overflow-y-auto custom-scroll" style="background: #F8FAFE;">
        
        <!-- Top welcome bar  -->
        <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-sm shadow-sm border-b" style="border-color: #E2E8F0;">
            <div class="flex justify-between items-center px-8 py-5 flex-wrap gap-3">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900" id="welcomeMessage">Welcome back, Admin</h2>
                    <p class="text-sm mt-1 text-gray-700" id="welcomeSubtitle">Empowering women through mentorship & safety — Here's your live snapshot</p>
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
                            <p class="text-sm font-semibold text-gray-800" id="topAdminName">Admin User</p>
                            <p class="text-xs text-gray-600" id="topAdminRole">System Administrator</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=5CB8E4&color=fff&size=48" class="w-11 h-11 rounded-full border-2 border-#3B59A8" id="topAdminAvatar">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Stats Cards - using the new color palette -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="stat-card bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #8BC34A;"> <!-- Lime Green -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-wide text-gray-600">Total Mentors</p>
                            <p class="text-3xl font-extrabold mt-1 text-gray-900" id="statTotalMentors">0</p>
                        </div>
                        <div class="p-3 rounded-full" style="background: #F1F8E9;"><i class="fas fa-chalkboard-user text-2xl" style="color: #8BC34A;"></i></div>
                    </div>
                    <div class="mt-3 text-sm"><span class="font-semibold text-green-700" id="newMentorsWeekStat">0</span> <span class="text-gray-600">this week</span></div>
                </div>
                <div class="stat-card bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #5CB8E4;"> <!-- Bright Blue -->
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium uppercase text-gray-600">Active Mentors</p><p class="text-3xl font-extrabold text-gray-900" id="statActiveMentors">0</p></div>
                        <div class="p-3 rounded-full" style="background: #E6F7FF;"><i class="fas fa-user-check text-2xl" style="color: #5CB8E4;"></i></div>
                    </div>
                    <div class="mt-2"><div class="w-full bg-blue-100 rounded-full h-2"><div class="h-2 rounded-full" style="width: 0%; background: #5CB8E4;" id="activePercentBar"></div></div><p class="text-xs text-gray-600 mt-1" id="activePercentText">0% of total mentors</p></div>
                </div>
                <div class="stat-card bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #9C27B0;"> <!-- Purple/Magenta -->
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium uppercase text-gray-600">Pending Reports</p><p class="text-3xl font-extrabold text-gray-900" id="statPendingReports">0</p></div>
                        <div class="p-3 rounded-full" style="background: #F3E5F5;"><i class="fas fa-exclamation-triangle text-2xl" style="color: #9C27B0;"></i></div>
                    </div>
                    <div class="mt-3 text-sm"><span class="font-semibold text-purple-700" id="statInReview">0 in review</span></div>
                </div>
                <div class="stat-card bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #3B59A8;"> <!-- Deep Blue (Dominant) -->
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
                            {{-- ADD MENTOR BUTTON LINKED TO Addmentor.blade.php --}}
                            <a href="{{ route('admin.mentors.create') }}" id="addMentorBtn" class="transition px-5 py-2 rounded-xl text-sm shadow-sm flex items-center gap-2" style="background: #874179; color: white;">
                                <i class="fas fa-plus-circle"></i> Add New Mentor
                            </a>
                        </div>
                        <div class="p-5 border-b">
                            <div class="relative"><i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i><input type="text" id="mentorSearch" placeholder="Search by name, expertise..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-#3B59A8 focus:ring-1 focus:ring-#3B59A8 bg-gray-50"></div>
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
                                <tbody id="mentorsTableBody" class="divide-y divide-gray-100"></tbody>
                            </table>
                        </div>
                        <div class="p-4 text-center border-t"><a href="#" class="font-medium hover:underline" id="viewAllMentorsLink" style="color: #874179;">View all mentors →</a></div>
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
                        <div id="reportsListContainer" class="divide-y divide-gray-100 max-h-64 overflow-y-auto"></div>
                        <div class="p-3 border-t text-center"><a href="#" class="text-sm font-medium hover:underline" id="viewAllReportsLink" style="color: #874179;">Review all reports →</a></div>
                    </div>

                    <!-- New Mentors This Week -->
                    <div class="bg-white rounded-2xl shadow-md">
                        <div class="p-5 border-b border-gray-100" style="background: #F1F8E9;">
                            <h3 class="font-bold text-gray-800"><i class="fas fa-seedling mr-2" style="color: #8BC34A;"></i> New Mentors</h3>
                            <p class="text-xs text-gray-600">Joined this week</p>
                        </div>
                        <div id="newMentorsList" class="divide-y divide-gray-100"></div>
                    </div>

                    <!-- Quick Actions + mini chart preview with Deep Blue and Bright Blue gradient -->
                         <div class="rounded-2xl shadow-lg p-6 text-white empower-card">
                        <div class="flex items-center gap-3"><i class="fas fa-chalkboard fa-fw text-3xl" style="color: #FFFFFF;"></i><h3 class="text-xl font-bold">Empower a session</h3></div>
                        <p class="text-sm mt-2 opacity-90">Organize next mentor training or community circle</p>
                        <!-- Schedule button with matching purple style -->
                        <button class="mt-5 w-full bg-white text-[#874179] font-semibold py-2.5 rounded-xl hover:bg-gray-100 transition flex items-center justify-center gap-2 shadow-md" id="scheduleTrainingBtn"><i class="fas fa-calendar-alt"></i> Schedule Training</button>
                        <div class="mt-5 pt-2 border-t border-white/30 text-xs text-center opacity-80"> 45+ active community members this month</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mock data for demonstration - matches the dashboard requirements
    // let mentorsList = [
    //     { id: 1, name: "Dr. Grace Mumba", email: "grace@tithandizane.org", expertise: ["Leadership", "Entrepreneurship"], availability: "Mon, Wed 10AM-2PM", status: "active", created_at: new Date(Date.now() - 2*24*60*60*1000) },
    //     { id: 2, name: "Esther Phiri", email: "esther@tithandizane.org", expertise: ["Financial Literacy", "Tech Skills"], availability: "Tue, Thu 1PM-5PM", status: "active", created_at: new Date(Date.now() - 1*24*60*60*1000) },
    //     { id: 3, name: "Ruth Chisale", email: "ruth@tithandizane.org", expertise: ["Mental Health", "Counseling"], availability: "Weekends 9AM-12PM", status: "pending", created_at: new Date(Date.now() - 10*24*60*60*1000) },
    //     { id: 4, name: "Mercy Banda", email: "mercy@tithandizane.org", expertise: ["Advocacy", "Legal Rights"], availability: "Flexible", status: "active", created_at: new Date(Date.now() - 3*24*60*60*1000) }
    // ];
    
    let reportsList = [
        { report_id: "HR-101", description: "User reported inappropriate messaging from a community member. Immediate action needed.", report_type: "harassment", status: "new", created_at: new Date(Date.now() - 5*60*60*1000) },
        { report_id: "HR-102", description: "Anonymous report about unsafe behavior during mentorship session.", report_type: "misconduct", status: "in_review", created_at: new Date(Date.now() - 2*24*60*60*1000) },
        { report_id: "HR-103", description: "Offensive content in group forum reported by multiple users.", report_type: "content", status: "new", created_at: new Date(Date.now() - 1*24*60*60*1000) }
    ];
    
    function getAvatarBg(name) {
        let hash = 0;
        for(let i=0; i<name.length; i++) hash = ((hash<<5)-hash)+name.charCodeAt(i);
        const colors = ['3B59A8', '5CB8E4', '9C27B0', '4CAF50', '8BC34A'];
        return colors[Math.abs(hash) % colors.length];
    }
    
    function timeAgo(date) {
        if(!(date instanceof Date)) date = new Date(date);
        const seconds = Math.floor((new Date() - date) / 1000);
        if(isNaN(seconds)) return 'recent';
        if(seconds < 60) return 'just now';
        const intervals = [
            {label: 'd', seconds: 86400}, {label: 'h', seconds: 3600}, {label: 'm', seconds: 60}
        ];
        for(let i of intervals) {
            const count = Math.floor(seconds / i.seconds);
            if(count >= 1 && i.label === 'd') return `${count}d`;
            if(count >= 1 && i.label === 'h') return `${count}h`;
            if(count >= 1 && i.label === 'm') return `${count}m`;
        }
        return 'recent';
    }
    
    function escapeHtml(str) { if(!str) return ''; return str.replace(/[&<>]/g, function(m){ if(m === '&') return '&amp;'; if(m === '<') return '&lt;'; if(m === '>') return '&gt;'; return m;}); }
    
    function renderMentorsTable(filterText = "") {
        const filtered = mentorsList.filter(m => 
            m.name.toLowerCase().includes(filterText) || 
            m.expertise.some(e => e.toLowerCase().includes(filterText)) || 
            (m.availability && m.availability.toLowerCase().includes(filterText))
        );
        const tbody = document.getElementById('mentorsTableBody');
        if(!tbody) return;
        tbody.innerHTML = filtered.map(mentor => {
            const bgColor = getAvatarBg(mentor.name);
            return `<tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4"><div class="flex items-center gap-3"><img src="https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=${bgColor}&color=fff&size=36" class="w-9 h-9 rounded-full border border-gray-200"><div><p class="font-semibold text-gray-800">${escapeHtml(mentor.name)}</p><p class="text-xs text-gray-500">${escapeHtml(mentor.email)}</p></div></div></td>
                <td class="px-6 py-4">${mentor.expertise.map(e => `<span class="inline-block text-xs px-2 py-1 rounded-full mr-1 mb-1" style="background: #E8EAF6; color: #3B59A8;">${escapeHtml(e)}</span>`).join('')}</td>
                <td class="px-6 py-4 text-sm text-gray-700">${escapeHtml(mentor.availability)}</td>
                <td class="px-6 py-4">${mentor.status === 'active' ? '<span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Active</span>' : (mentor.status === 'inactive' ? '<span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">Inactive</span>' : '<span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-full">Pending</span>')}</td>
                <td class="px-6 py-4 text-right"><button class="mr-3 edit-mentor" data-id="${mentor.id}" style="color: #3B59A8;"><i class="fas fa-edit"></i></button><button class="delete-mentor" style="color: #9C27B0;" data-id="${mentor.id}"><i class="fas fa-trash-alt"></i></button></td>
                </tr>`;
        }).join('');
        if(filtered.length === 0) tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">🌱 No mentors found</td></tr>';
        attachMentorActions();
    }
    
    function attachMentorActions() {
        document.querySelectorAll('.edit-mentor').forEach(btn => {
            btn.addEventListener('click', (e) => { alert(`✏️ Edit mentor ID ${btn.dataset.id} — Form integration ready.`); });
        });
        document.querySelectorAll('.delete-mentor').forEach(btn => {
            btn.addEventListener('click', (e) => { if(confirm('Remove mentor permanently?')) alert(`🗑️ Mentor ${btn.dataset.id} removed.`); });
        });
    }
    
    function renderRecentReports() {
        const container = document.getElementById('reportsListContainer');
        const sorted = [...reportsList].sort((a,b)=> new Date(b.created_at) - new Date(a.created_at)).slice(0,3);
        container.innerHTML = sorted.map(r => `
            <div class="p-4 hover:bg-blue-50/40 transition">
                <div class="flex justify-between items-start"><div><span class="font-mono text-sm font-bold text-gray-800">${escapeHtml(r.report_id)}</span>${r.status === 'new' ? '<span class="ml-2 bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded-full">New</span>' : (r.status === 'in_review' ? '<span class="ml-2 bg-yellow-100 text-yellow-700 text-[10px] px-2 py-0.5 rounded-full">Review</span>' : '')}</div><span class="text-xs text-gray-500">${timeAgo(new Date(r.created_at))}</span></div>
                <p class="text-sm text-gray-700 mt-1 line-clamp-2">${escapeHtml(r.description.substring(0,80))}${r.description.length>80?'…':''}</p>
                <div class="mt-2"><span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded-full">${escapeHtml(r.report_type.replace('_',' '))}</span></div>
            </div>
        `).join('');
        if(sorted.length===0) container.innerHTML = '<div class="p-6 text-center text-gray-500">✅ No pending reports</div>';
    }
    
    function renderNewMentors() {
        const container = document.getElementById('newMentorsList');
        const oneWeekAgo = new Date(); oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
        const recent = mentorsList.filter(m => new Date(m.created_at) >= oneWeekAgo).slice(0,4);
        container.innerHTML = recent.map(m => `<div class="p-4 flex items-center gap-3"><img src="https://ui-avatars.com/api/?name=${encodeURIComponent(m.name)}&background=${getAvatarBg(m.name)}&color=fff" class="w-10 h-10 rounded-full"><div><p class="font-medium text-gray-800">${escapeHtml(m.name)}</p><p class="text-xs text-gray-500">Joined ${timeAgo(new Date(m.created_at))}</p></div></div>`).join('');
        if(recent.length===0) container.innerHTML = '<div class="p-5 text-center text-gray-500">✨ No new mentors this week</div>';
    }
    
    function updateDashboardUI() {
        const totalMentors = mentorsList.length;
        const activeMentors = mentorsList.filter(m => m.status === 'active').length;
        const pendingReports = reportsList.filter(r => r.status === 'new' || r.status === 'in_review').length;
        const inReviewReports = reportsList.filter(r => r.status === 'in_review').length;
        const newMentorsThisWeek = mentorsList.filter(m => new Date(m.created_at) >= new Date(Date.now() - 7*24*60*60*1000)).length;
        const totalUsers = 1245; // mock users count
        const userGrowthPercent = 8.5;
        
        document.getElementById('statTotalMentors').innerText = totalMentors;
        document.getElementById('statActiveMentors').innerText = activeMentors;
        document.getElementById('statPendingReports').innerText = pendingReports;
        document.getElementById('statTotalUsers').innerText = totalUsers;
        document.getElementById('newMentorsWeekStat').innerText = newMentorsThisWeek;
        document.getElementById('statInReview').innerHTML = `${inReviewReports} in review`;
        document.getElementById('userGrowthPercent').innerText = userGrowthPercent;
        const activePercent = totalMentors > 0 ? Math.round((activeMentors / totalMentors) * 100) : 0;
        document.getElementById('activePercentBar').style.width = `${activePercent}%`;
        document.getElementById('activePercentText').innerText = `${activePercent}% of total mentors`;
        
        document.getElementById('pendingReportsBadge').innerText = pendingReports;
        document.getElementById('totalUsersBadge').innerText = totalUsers;
        document.getElementById('reportsCountBadge').innerText = `${pendingReports} new`;
        document.getElementById('notificationBadge').innerText = pendingReports;
        
        const adminName = "Grace Ndlovu";
        const adminEmail = "grace.admin@tithandizane.org";
        document.getElementById('adminNameDisplay').innerText = adminName;
        document.getElementById('adminEmailDisplay').innerText = adminEmail;
        document.getElementById('topAdminName').innerText = adminName;
        document.getElementById('topAdminRole').innerText = "Lead Administrator";
        document.getElementById('welcomeMessage').innerHTML = `Welcome back, Grace 🌾`;
        
        document.getElementById('adminAvatarImg').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(adminName)}&background=5CB8E4&color=fff&bold=true&size=40`;
        document.getElementById('topAdminAvatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(adminName)}&background=5CB8E4&color=fff&size=48`;
        
        renderMentorsTable('');
        renderRecentReports();
        renderNewMentors();
    }
    
    function initNav() {
        const items = document.querySelectorAll('.nav-item');
        items.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                items.forEach(i => { i.style.background = 'transparent'; i.style.color = '#E2E8F0'; });
                item.style.background = '#2C4A8C'; item.style.color = '#FFFFFF';
                const page = item.getAttribute('data-page');
                if(page !== 'dashboard') alert(`✨ ${page} section — ready for backend integration.`);
            });
            if(item.getAttribute('data-page') === 'dashboard') { item.style.background = '#2C4A8C'; item.style.color = '#FFFFFF'; }
        });
    }
    
    window.handleLogout = function() { 
        alert("✅ Logged out successfully.");
        window.location.href = "{{ route('admin.login') }}";
    };
    
    function initDashboard() {
        initNav();
        updateDashboardUI();
        const searchInput = document.getElementById('mentorSearch');
        if(searchInput) searchInput.addEventListener('input', (e) => renderMentorsTable(e.target.value.toLowerCase()));
        
        // Link for schedule training (optional)
        document.getElementById('scheduleTrainingBtn')?.addEventListener('click', () => alert('📅 Schedule training: Calendar integration ready.'));
        
        // Link for view all mentors (optional)
        document.getElementById('viewAllMentorsLink')?.addEventListener('click', (e) => { e.preventDefault(); alert('📋 Full mentor management page.'); });
        
        // Link for view all reports (optional)
        document.getElementById('viewAllReportsLink')?.addEventListener('click', (e) => { e.preventDefault(); alert('🚨 Full reports management system.'); });
    }
    
    initDashboard();
</script>
</body>
</html>