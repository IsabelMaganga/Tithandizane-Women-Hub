@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Welcome back, ' . Auth::guard('admin')->user()->name ?? 'Admin')
@section('page-subtitle', 'Empowering women through mentorship & safety Here\'s your live snapshot')

@push('styles')
<style>
    /* Dashboard specific styles if needed */
</style>
@endpush

@section('content')
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
@endsection

@push('scripts')
<script>
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
            container.innerHTML = '<div class="p-5 text-center" style="color: var(--text-secondary);"> No new mentors this week</div>';
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
            // Normalize reports array from paginated or direct response
            let reports = [];
            if (Array.isArray(data.reports)) reports = data.reports;
            else if (data.reports && Array.isArray(data.reports.data)) reports = data.reports.data;
            else if (Array.isArray(data.data)) reports = data.data;

            // Map alternate status names used elsewhere (new -> pending, in_review -> reviewing)
            const normalizeStatus = (s) => {
                if (!s) return s;
                if (s === 'new') return 'pending';
                if (s === 'in_review') return 'reviewing';
                return s;
            };

            const pendingReports = reports.filter(r => {
                const st = normalizeStatus(r.status);
                return st === 'pending' || st === 'reviewing' || st === 'assigned';
            }).length;

            document.getElementById('statPendingReports').innerText = pendingReports;
            document.getElementById('reportsCountBadge').innerText = `${pendingReports} new`;
            const notif = document.getElementById('notificationBadge');
            if (notif) notif.innerText = pendingReports;
            const summaryOpen = document.getElementById('summaryOpenReports');
            if (summaryOpen) summaryOpen.innerText = pendingReports;

            const inReview = reports.filter(r => normalizeStatus(r.status) === 'reviewing').length;
            const inReviewEl = document.getElementById('statInReview');
            if (inReviewEl) inReviewEl.innerHTML = `${inReview} in review`;

            renderRecentReports(reports.slice(0, 3));
        })
        .catch(error => console.error('Error loading reports:', error));
    }

    function renderRecentReports(reports) {
        const container = document.getElementById('reportsListContainer');

        if (!reports || reports.length === 0) {
            container.innerHTML = '<div class="p-6 text-center" style="color: var(--text-secondary);"> No pending reports</div>';
            return;
        }

        container.innerHTML = reports.map(r => {
            const status = (r.status === 'new') ? 'pending' : (r.status === 'in_review' ? 'reviewing' : (r.status || 'pending'));
            const badgeHtml = status === 'pending'
                ? '<span class="ml-2 text-[10px] px-2 py-0.5 rounded-full" style="background: var(--light-red); color: var(--red);">New</span>'
                : (status === 'reviewing' ? '<span class="ml-2 text-[10px] px-2 py-0.5 rounded-full" style="background: var(--light-orange); color: var(--orange);">Review</span>' : (status === 'assigned' ? '<span class="ml-2 text-[10px] px-2 py-0.5 rounded-full" style="background: var(--light-purple); color: var(--purple);">Assigned</span>' : ''));

            const assignedHtml = (r.assigned_mentor_id || r.assigned_mentor) ? '<span class="text-xs text-green-600"><i class="fas fa-user-check"></i> Assigned</span>' : '<span class="text-xs text-yellow-600"><i class="fas fa-clock"></i> Pending</span>';

            return `
            <div class="p-4 transition hover:bg-gray-50 cursor-pointer" onclick="window.location.href='/admin/reports/${r.id}'">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="font-mono text-sm font-bold" style="color: var(--text-primary);">#${escapeHtml(r.reference_number || r.id)}</span>
                        ${badgeHtml}
                    </div>
                    <span class="text-xs" style="color: var(--text-secondary);">${new Date(r.created_at).toLocaleDateString()}</span>
                </div>
                <p class="text-sm mt-1 line-clamp-2" style="color: var(--text-secondary);">${escapeHtml(r.incident_title || (r.incident_description ? (r.incident_description.substring(0,80)) : 'No description'))}</p>
                <div class="mt-2 flex justify-between items-center">
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: var(--light-gray); color: var(--text-secondary);">${escapeHtml(r.incident_type || 'harassment')}</span>
                    ${assignedHtml}
                </div>
            </div>
        `;
        }).join('');
    }

    // Load total users count for stat card
    function loadTotalUsersStat() {
        const url = '{{ route("admin.users.index") }}';
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const statTotalUsers = document.getElementById('statTotalUsers');
            if (statTotalUsers && data.totalUsers !== undefined) {
                statTotalUsers.innerText = data.totalUsers;
            }
            if (data.userGrowthPercent !== undefined) {
                const userGrowthPercent = document.getElementById('userGrowthPercent');
                if (userGrowthPercent) {
                    userGrowthPercent.innerText = data.userGrowthPercent + '%';
                }
            }
        })
        .catch(error => console.error('Error loading users stat:', error));
    }

    function loadNotificationBadge() {
        const url = '{{ route("admin.notifications") }}';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (badge && data.unread_count !== undefined) {
                badge.innerText = data.unread_count;
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadMentors();
        loadRecentReports();
        loadTotalUsersStat();
        loadNotificationBadge();
        
        document.getElementById('scheduleTrainingBtn')?.addEventListener('click', () => {
            // Redirect to mentor management where admin can arrange training or schedule sessions
            window.location.href = '{{ route("admin.mentors.index") }}';
        });
    });
</script>
@endpush