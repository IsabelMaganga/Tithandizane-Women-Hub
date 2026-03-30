<!-- @extends('layouts.admin')

@section('title', 'Add Mentor')
@section('page-title', 'Add Mentor')

@section('content')
<div class="page-header">
    <h2>Add New Mentor</h2>
    <p>Fill in the details below to register a new mentor.</p>
</div>

<form method="POST" action="{{ route('admin.mentors.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.mentors._form')
    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary-hub"><i class="bi bi-check-lg me-1"></i>Save Mentor</button>
        <a href="{{ route('admin.mentors.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">Cancel</a>
    </div>
</form>
@endsection -->



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tithandizane Women Hub | Admin Dashboard</title>
    <!-- Tailwind + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js for optional mini chart (enhancement) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Custom smooth transitions & custom scrollbar */
        body {
            background: #f7f3eb;  /* warm earthy background */
        }
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #e6dfd3;
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #b87c4f;
            border-radius: 10px;
        }
        .hover-scale {
            transition: transform 0.2s ease;
        }
        .hover-scale:hover {
            transform: translateY(-2px);
        }
        .card-shadow {
            box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 6px rgba(0,0,0,0.05);
        }
        .golden-glow {
            background: linear-gradient(135deg, #F59E0B15, #D9770610);
        }
    </style>
</head>
<body class="font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    <!-- ================= LEFT SIDEBAR - Earth & Terracotta meets deep indigo ================= -->
    <div class="w-64 flex flex-col shadow-xl" style="background: #1E1A2F; /* deep purple/indigo vibe */ border-right: 1px solid #2D2A40;">
        <!-- Branding with golden/earth accent -->
        <div class="p-6 border-b border-indigo-900/40">
            <h1 class="text-2xl font-bold tracking-tight" style="color: #FDE68A;">Tithandizane</h1>
            <p class="text-xs mt-1 opacity-80" style="color: #D4B87A;">Women Empowerment Hub</p>
        </div>

        <nav class="flex-1 mt-6 space-y-1 px-3" id="sidebar-nav">
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="dashboard" style="color: #E2E8F0; background: #2D2A44;">
                <i class="fas fa-home w-5 text-amber-400"></i>
                <span class="ml-3 font-medium">Dashboard</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="mentors" style="color: #CBD5E1;">
                <i class="fas fa-chalkboard-user w-5 text-emerald-400"></i>
                <span class="ml-3">Mentors</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="reports" style="color: #CBD5E1;">
                <i class="fas fa-flag w-5 text-rose-400"></i>
                <span class="ml-3">Harassment Reports</span>
                <span class="ml-auto bg-rose-600 text-white text-xs font-bold px-2 py-0.5 rounded-full" id="pendingReportsBadge">3</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="guidance" style="color: #CBD5E1;">
                <i class="fas fa-book-open w-5 text-teal-400"></i>
                <span class="ml-3">Guidance Content</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="users" style="color: #CBD5E1;">
                <i class="fas fa-user-circle w-5 text-indigo-300"></i>
                <span class="ml-3">Users</span>
                <span class="ml-auto bg-gray-600 text-xs px-2 py-0.5 rounded-full" id="totalUsersBadge">248</span>
            </a>
            <a href="#" class="nav-item flex items-center px-4 py-3 rounded-lg transition-all duration-200 group" data-page="settings" style="color: #CBD5E1;">
                <i class="fas fa-cog w-5 text-stone-400"></i>
                <span class="ml-3">Settings</span>
            </a>
            <div class="pt-8 mt-auto">
                <form id="logout-form" method="POST">
                    <button type="button" onclick="handleLogout()" class="w-full flex items-center px-4 py-3 rounded-lg transition hover:bg-rose-800/50 text-stone-300 hover:text-white">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        </nav>

        <!-- Admin user card - earthy & warm -->
        <div class="p-5 m-3 rounded-xl mt-2" style="background: #2A253D; border: 1px solid #3E3658;">
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name=Thandiwe+Nkosi&background=CA8A65&color=fff&bold=true&size=40" class="w-10 h-10 rounded-full border-2 border-amber-500">
                <div class="ml-3">
                    <p class="text-sm font-semibold text-amber-100">Thandiwe Nkosi</p>
                    <p class="text-xs text-stone-400">admin@tithandizane.org</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MAIN CONTENT - Golden hour & Earthy Tones ================= -->
    <div class="flex-1 overflow-y-auto custom-scroll" style="background: #FDF8F0;">
        <!-- Top welcome bar (warm & golden) -->
        <div class="sticky top-0 z-10 bg-white/90 backdrop-blur-sm shadow-sm border-b border-amber-100/60">
            <div class="flex justify-between items-center px-8 py-5">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight" style="color: #7C4A2E;">Welcome back, Thandiwe 🌾</h2>
                    <p class="text-sm mt-1" style="color: #B87C4F;">Empowering women through mentorship & safety — Here's your snapshot</p>
                </div>
                <div class="flex items-center gap-5">
                    <div class="relative">
                        <i class="fas fa-bell text-2xl cursor-pointer transition" style="color: #B87A48;"></i>
                        <span class="absolute -top-1 -right-2 bg-red-500 text-white text-[10px] rounded-full px-1.5">3</span>
                    </div>
                    <i class="fas fa-envelope text-2xl cursor-pointer" style="color: #B87A48;"></i>
                    <div class="h-8 w-px bg-amber-200"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-stone-800">Thandiwe Nkosi</p>
                            <p class="text-xs text-stone-500">Program Director</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Thandiwe+Nkosi&background=BB7E5A&color=fff&size=48" class="w-11 h-11 rounded-full border-2 border-amber-400">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Stats Cards (earthy + green + terracotta accents) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #2F855A;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium uppercase tracking-wide" style="color: #7F5E3A;">Total Mentors</p>
                            <p class="text-3xl font-extrabold mt-1 text-gray-800" id="statTotalMentors">24</p>
                        </div>
                        <div class="p-3 rounded-full" style="background: #E7F0EA;"><i class="fas fa-chalkboard-user text-2xl" style="color: #2F855A;"></i></div>
                    </div>
                    <div class="mt-3 text-sm"><span class="font-semibold text-green-700">+5</span> <span class="text-stone-500">this week</span></div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #D99E4C;">
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium uppercase" style="color: #7F5E3A;">Active Mentors</p><p class="text-3xl font-extrabold" id="statActiveMentors">18</p></div>
                        <div class="p-3 rounded-full" style="background: #FDF3E2;"><i class="fas fa-user-check text-2xl" style="color: #D99E4C;"></i></div>
                    </div>
                    <div class="mt-2"><div class="w-full bg-amber-100 rounded-full h-2"><div class="h-2 rounded-full" style="width: 75%; background: #D99E4C;"></div></div><p class="text-xs text-stone-500 mt-1">75% of total mentors</p></div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #C7522A;">
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium uppercase" style="color: #7F5E3A;">Pending Reports</p><p class="text-3xl font-extrabold" id="statPendingReports">3</p></div>
                        <div class="p-3 rounded-full" style="background: #FCE9E3;"><i class="fas fa-exclamation-triangle text-2xl" style="color: #C7522A;"></i></div>
                    </div>
                    <div class="mt-3 text-sm"><span class="font-semibold text-amber-700" id="statInReview">2 in review</span></div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-md card-shadow hover-scale transition border-l-8" style="border-left-color: #8B5F8C;">
                    <div class="flex items-center justify-between">
                        <div><p class="text-sm font-medium uppercase" style="color: #7F5E3A;">Total Users</p><p class="text-3xl font-extrabold" id="statTotalUsers">248</p></div>
                        <div class="p-3 rounded-full" style="background: #F0E9F0;"><i class="fas fa-users text-2xl" style="color: #8B5F8C;"></i></div>
                    </div>
                    <div class="mt-3 text-sm"><i class="fas fa-arrow-up text-green-600"></i> <span class="text-green-700 font-semibold">12%</span> <span class="text-stone-500">from last month</span></div>
                </div>
            </div>

            <!-- Two column layout: Mentors table + right sidebar (reports, quick actions) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT: Manage Mentors Table (wider) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="p-5 border-b border-stone-100 flex flex-wrap justify-between items-center bg-gradient-to-r from-amber-50/30 to-white">
                            <h3 class="text-xl font-bold" style="color: #6B3F1F;"><i class="fas fa-hands-helping mr-2 text-emerald-700"></i>Mentor Directory</h3>
                            <button class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2 rounded-xl text-sm shadow-sm transition flex items-center gap-2"><i class="fas fa-plus-circle"></i> Add New Mentor</button>
                        </div>
                        <div class="p-5 border-b">
                            <div class="relative"><i class="fas fa-search absolute left-4 top-3.5 text-stone-400"></i><input type="text" id="mentorSearch" placeholder="Search by name, expertise or village..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-stone-200 focus:border-emerald-400 focus:ring-1 focus:ring-emerald-300 bg-stone-50"></div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-stone-50/90">
                                    <tr><th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase">Mentor</th><th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase">Expertise</th><th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase">Availability</th><th class="px-6 py-4 text-left text-xs font-bold text-stone-500 uppercase">Status</th><th class="px-6 py-4 text-right text-xs font-bold text-stone-500 uppercase">Actions</th></tr>
                                </thead>
                                <tbody id="mentorsTableBody" class="divide-y divide-stone-100"></tbody>
                            </table>
                        </div>
                        <div class="p-4 text-center border-t"><a href="#" class="text-emerald-700 text-sm font-medium hover:underline">View all mentors →</a></div>
                    </div>
                </div>

                <!-- RIGHT: Recent Reports + New Mentors + Quick Actions (earthy & deep red/purple accents) -->
                <div class="space-y-7">
                    <!-- Recent Reports Card -->
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="p-5 border-b flex justify-between items-center" style="background: #FEF7EE;">
                            <h3 class="font-bold text-stone-800"><i class="fas fa-flag-checkered text-rose-600 mr-2"></i> Recent Harassment Reports</h3>
                            <span class="bg-rose-100 text-rose-700 text-xs px-2.5 py-1 rounded-full font-semibold" id="reportsCountBadge">3 new</span>
                        </div>
                        <div id="reportsListContainer" class="divide-y divide-stone-100 max-h-64 overflow-y-auto"></div>
                        <div class="p-3 border-t text-center"><a href="#" class="text-amber-700 text-sm font-medium">Review all reports →</a></div>
                    </div>

                    <!-- New Mentors This Week (earthy tone) -->
                    <div class="bg-white rounded-2xl shadow-md">
                        <div class="p-5 border-b border-stone-100" style="background: #FEF2E6;"><h3 class="font-bold text-stone-800"><i class="fas fa-seedling text-emerald-600 mr-2"></i> New Mentors</h3><p class="text-xs text-stone-500">Joined this week</p></div>
                        <div id="newMentorsList" class="divide-y divide-stone-100"></div>
                    </div>

                    <!-- Quick Actions - Terracotta/Golden vibe -->
                    <div class="rounded-2xl shadow-lg p-6 text-white" style="background: linear-gradient(135deg, #C55A2C, #9C4A2C);">
                        <div class="flex items-center gap-3"><i class="fas fa-chalkboard fa-fw text-3xl text-amber-200"></i><h3 class="text-xl font-bold">Empower a session</h3></div>
                        <p class="text-sm mt-2 opacity-90">Organize next mentor training or community circle</p>
                        <button class="mt-5 w-full bg-amber-100 text-amber-900 font-semibold py-2.5 rounded-xl hover:bg-white transition flex items-center justify-center gap-2"><i class="fas fa-calendar-alt"></i> Schedule Training</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ------------------------- MOCK DATA (rich, warm palette representation) -------------------------
    const mentorsData = [
        { id:1, name:"Chisomo Banda", email:"chisomo@tithandizane.org", expertise:["Mental Health","Trauma Support"], availability:"Mon/Wed 9-12", status:"active", avatarBg:"CA8A65", created_at:"2025-02-18" },
        { id:2, name:"Wezi Phiri", email:"wezi@hub.mw", expertise:["Financial Literacy","Entrepreneurship"], availability:"Tue/Thu 14-17", status:"active", avatarBg:"5F7A55", created_at:"2025-02-20" },
        { id:3, name:"Tionge Mwale", email:"tionge@womenhub.org", expertise:["Legal Rights","Gender Based Violence"], availability:"Fri 10-15", status:"pending", avatarBg:"B4653A", created_at:"2025-02-22" },
        { id:4, name:"Grace Kalilani", email:"grace@empower.mw", expertise:["Digital Skills","Career Mentorship"], availability:"Weekends 9-12", status:"active", avatarBg:"876B4F", created_at:"2025-02-15" },
        { id:5, name:"Esther Nkhoma", email:"esther@tithandizane.com", expertise:["Agribusiness","Climate Resilience"], availability:"Mon 13-16, Wed 9-11", status:"active", avatarBg:"B87333", created_at:"2025-02-23" }
    ];

    const reportsData = [
        { report_id:"RPT-101", description:"User reported offensive comments from a community member.", report_type:"verbal_harassment", status:"new", created_at: new Date(Date.now() - 2*86400000) },
        { report_id:"RPT-102", description:"Inappropriate behavior during group session. Requires review.", report_type:"misconduct", status:"in_review", created_at: new Date(Date.now() - 86400000) },
        { report_id:"RPT-103", description:"Received threats via direct message. Escalate.", report_type:"threat", status:"new", created_at: new Date(Date.now() - 5*3600000) },
        { report_id:"RPT-104", description:"Harassment at safe space meeting", report_type:"physical", status:"in_review", created_at: new Date(Date.now() - 3*86400000) }
    ];
    const pendingReportsCount = reportsData.filter(r => r.status === 'new' || r.status === 'in_review').length;
    const inReviewCount = reportsData.filter(r => r.status === 'in_review').length;

    // Additional stats
    let totalMentors = mentorsData.length;
    let activeMentors = mentorsData.filter(m => m.status === 'active').length;
    let newThisWeek = mentorsData.filter(m => {
        let date = new Date(m.created_at);
        let now = new Date();
        let diffDays = (now - date) / (1000*60*60*24);
        return diffDays <= 7;
    }).length;
    let totalUsers = 248;  // static dynamic

    // Update DOM badges & stat cards
    document.getElementById('statTotalMentors').innerText = totalMentors;
    document.getElementById('statActiveMentors').innerText = activeMentors;
    document.getElementById('statPendingReports').innerText = pendingReportsCount;
    document.getElementById('statInReview').innerHTML = `${inReviewCount} in review`;
    document.getElementById('statTotalUsers').innerText = totalUsers;
    document.getElementById('pendingReportsBadge').innerText = pendingReportsCount;
    document.getElementById('totalUsersBadge').innerText = totalUsers;
    document.getElementById('reportsCountBadge').innerText = `${pendingReportsCount} new`;

    // Render mentors table with search
    function renderMentorsTable(filterText = "") {
        const filtered = mentorsData.filter(m => m.name.toLowerCase().includes(filterText) || m.expertise.some(e => e.toLowerCase().includes(filterText)) || (m.availability && m.availability.toLowerCase().includes(filterText)));
        const tbody = document.getElementById('mentorsTableBody');
        if(!tbody) return;
        tbody.innerHTML = filtered.map(mentor => `
            <tr class="hover:bg-stone-50 transition">
                <td class="px-6 py-4"><div class="flex items-center gap-3"><img src="https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=${mentor.avatarBg}&color=fff&size=36" class="w-9 h-9 rounded-full border border-amber-200"><div><p class="font-semibold text-stone-800">${mentor.name}</p><p class="text-xs text-stone-400">${mentor.email}</p></div></div></td>
                <td class="px-6 py-4">${mentor.expertise.map(e => `<span class="inline-block bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">${e}</span>`).join('')}</td>
                <td class="px-6 py-4 text-sm text-stone-600">${mentor.availability}</td>
                <td class="px-6 py-4">${mentor.status === 'active' ? '<span class="bg-emerald-100 text-emerald-700 text-xs px-3 py-1 rounded-full font-medium">Active</span>' : (mentor.status === 'inactive' ? '<span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">Inactive</span>' : '<span class="bg-amber-100 text-amber-700 text-xs px-3 py-1 rounded-full">Pending</span>')}</td>
                <td class="px-6 py-4 text-right"><button class="text-indigo-600 hover:text-indigo-800 mr-3"><i class="fas fa-edit"></i></button><button class="text-rose-500 hover:text-rose-700"><i class="fas fa-trash-alt"></i></button></td>
            </tr>
        `).join('');
        if(filtered.length === 0) tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-stone-400">No mentors found 🌱</td></tr>';
    }

    // Render recent reports (latest 3)
    function renderRecentReports() {
        const container = document.getElementById('reportsListContainer');
        const sorted = [...reportsData].sort((a,b)=> new Date(b.created_at) - new Date(a.created_at)).slice(0,3);
        container.innerHTML = sorted.map(r => `
            <div class="p-4 hover:bg-amber-50/40 transition">
                <div class="flex justify-between items-start"><div><span class="font-mono text-sm font-bold text-stone-700">${r.report_id}</span>${r.status === 'new' ? '<span class="ml-2 bg-red-100 text-red-700 text-[10px] px-2 py-0.5 rounded-full">New</span>' : (r.status === 'in_review' ? '<span class="ml-2 bg-yellow-100 text-yellow-700 text-[10px] px-2 py-0.5 rounded-full">Review</span>' : '')}</div><span class="text-xs text-stone-400">${timeAgo(r.created_at)}</span></div>
                <p class="text-sm text-stone-600 mt-1 line-clamp-2">${r.description.substring(0,70)}${r.description.length>70?'…':''}</p>
                <div class="mt-2"><span class="bg-stone-100 text-stone-600 text-xs px-2 py-0.5 rounded-full">${r.report_type.replace('_',' ')}</span></div>
            </div>
        `).join('');
        if(sorted.length===0) container.innerHTML = '<div class="p-6 text-center text-stone-400">✅ No pending reports</div>';
    }

    // New mentors (this week)
    function renderNewMentors() {
        const container = document.getElementById('newMentorsList');
        const oneWeekAgo = new Date(); oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
        const recent = mentorsData.filter(m => new Date(m.created_at) >= oneWeekAgo).slice(0,3);
        container.innerHTML = recent.map(m => `
            <div class="p-4 flex items-center gap-3"><img src="https://ui-avatars.com/api/?name=${encodeURIComponent(m.name)}&background=BD7A5A&color=fff" class="w-10 h-10 rounded-full"><div><p class="font-medium text-stone-800">${m.name}</p><p class="text-xs text-stone-400">Joined ${timeAgo(new Date(m.created_at))}</p></div></div>
        `).join('');
        if(recent.length===0) container.innerHTML = '<div class="p-5 text-center text-stone-400">✨ No new mentors this week</div>';
        else if(mentorsData.filter(m => new Date(m.created_at) >= oneWeekAgo).length > 3) {
            const extra = document.createElement('div'); extra.className = "p-3 text-center border-t text-stone-500 text-sm"; extra.innerText = `+${mentorsData.filter(m => new Date(m.created_at) >= oneWeekAgo).length - 3} more joined`;
            container.appendChild(extra);
        }
    }

    // Helper timeAgo
    function timeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000; if(interval > 1) return Math.floor(interval)+'y';
        interval = seconds / 2592000; if(interval > 1) return Math.floor(interval)+'mo';
        interval = seconds / 86400; if(interval > 1) return Math.floor(interval)+'d';
        interval = seconds / 3600; if(interval > 1) return Math.floor(interval)+'h';
        interval = seconds / 60; if(interval > 1) return Math.floor(interval)+'m';
        return Math.floor(seconds)+'s';
    }

    // Nav Active styling (simulate SPA)
    function initNav() {
        const items = document.querySelectorAll('.nav-item');
        items.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                items.forEach(i => { i.style.background = 'transparent'; i.style.color = '#CBD5E1'; i.classList.remove('bg-indigo-800/50', 'shadow-sm'); });
                item.style.background = '#2D2A44'; item.style.color = '#FDE68A'; item.classList.add('shadow-sm');
                const page = item.getAttribute('data-page');
                // mock alert for demonstration (page routing simulation)
                if(page !== 'dashboard') alert(`✨ ${page} section ready — content would load here (enhanced with real data).`);
                else location.reload(); // fake refresh
            });
            if(item.getAttribute('data-page') === 'dashboard') item.click();
        });
    }

    // Search functionality
    document.getElementById('mentorSearch')?.addEventListener('input', (e) => renderMentorsTable(e.target.value.toLowerCase()));

    // Logout handler
    window.handleLogout = function() { alert("Logged out safely 🌾 (session ended)"); window.location.reload(); };

    // Add some small chart / golden hour accent - optional chart in dashboard for trends
    function initMiniChart() { /* Optional chart enhancement - just visual warmth */ }

    renderMentorsTable('');
    renderRecentReports();
    renderNewMentors();
    initNav();
    
    // Extra flair: update mentor completion rate tooltip (already shown via active percentage)
    document.querySelectorAll('.fa-bell, .fa-envelope').forEach(icon => {
        icon.addEventListener('mouseenter', function(){ this.classList.add('text-amber-600'); this.classList.remove('text-[#B87A48]');});
        icon.addEventListener('mouseleave', function(){ this.classList.remove('text-amber-600'); this.classList.add('text-[#B87A48]');});
    });
</script>
</body>
</html>