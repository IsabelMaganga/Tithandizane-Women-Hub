{{-- resources/views/admin/mentors/index.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Manage Mentors')
@section('page-title', 'Manage Mentors')
@section('page-subtitle', 'View, edit, and manage all mentor profiles')

@section('content')
<!-- Stats Cards Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Mentors -->
    <div class="rounded-2xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-xl" style="background: linear-gradient(135deg, var(--card-bg) 0%, var(--card-bg) 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-wide" style="color: var(--text-secondary);">Total Mentors</p>
                <p class="text-4xl font-extrabold mt-2" id="statTotalMentorsCount" style="color: var(--text-primary);">0</p>
                <p class="text-xs mt-2" style="color: var(--text-secondary);">All registered mentors</p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(34, 197, 94, 0.1);">
                <i class="fas fa-users text-2xl" style="color: #22c55e;"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color: var(--text-secondary);">Completion</span>
                <span style="color: var(--text-secondary);" id="statTotalMentorsPercent">0%</span>
            </div>
            <div class="w-full rounded-full h-2" style="background: var(--light-gray);">
                <div class="h-2 rounded-full transition-all duration-500" id="totalProgressBar" style="width: 0%; background: #22c55e;"></div>
            </div>
        </div>
    </div>

    <!-- Active Mentors -->
    <div class="rounded-2xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-xl" style="background: linear-gradient(135deg, var(--card-bg) 0%, var(--card-bg) 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-wide" style="color: var(--text-secondary);">Active Mentors</p>
                <p class="text-4xl font-extrabold mt-2" id="statActiveMentorsCount" style="color: var(--text-primary);">0</p>
                <p class="text-xs mt-2" style="color: var(--text-secondary);">Currently available</p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(59, 130, 246, 0.1);">
                <i class="fas fa-user-check text-2xl" style="color: #3b82f6;"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color: var(--text-secondary);">Active Rate</span>
                <span style="color: var(--text-secondary);" id="statActiveMentorsPercent">0%</span>
            </div>
            <div class="w-full rounded-full h-2" style="background: var(--light-gray);">
                <div class="h-2 rounded-full transition-all duration-500" id="activeProgressBar" style="width: 0%; background: #3b82f6;"></div>
            </div>
        </div>
    </div>

    <!-- Pending Approval -->
    <div class="rounded-2xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-xl" style="background: linear-gradient(135deg, var(--card-bg) 0%, var(--card-bg) 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-wide" style="color: var(--text-secondary);">Pending Approval</p>
                <p class="text-4xl font-extrabold mt-2" id="statPendingMentorsCount" style="color: var(--text-primary);">0</p>
                <p class="text-xs mt-2" style="color: var(--text-secondary);">Awaiting review</p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(168, 85, 247, 0.1);">
                <i class="fas fa-hourglass-half text-2xl" style="color: #a855f7;"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color: var(--text-secondary);">Pending Rate</span>
                <span style="color: var(--text-secondary);" id="statPendingMentorsPercent">0%</span>
            </div>
            <div class="w-full rounded-full h-2" style="background: var(--light-gray);">
                <div class="h-2 rounded-full transition-all duration-500" id="pendingProgressBar" style="width: 0%; background: #a855f7;"></div>
            </div>
        </div>
    </div>

    <!-- Inactive Mentors -->
    <div class="rounded-2xl p-6 transition-all duration-300 hover:scale-105 hover:shadow-xl" style="background: linear-gradient(135deg, var(--card-bg) 0%, var(--card-bg) 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-wide" style="color: var(--text-secondary);">Inactive Mentors</p>
                <p class="text-4xl font-extrabold mt-2" id="statInactiveMentorsCount" style="color: var(--text-primary);">0</p>
                <p class="text-xs mt-2" style="color: var(--text-secondary);">Temporarily unavailable</p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(236, 72, 153, 0.1);">
                <i class="fas fa-user-slash text-2xl" style="color: #ec4899;"></i>
            </div>
        </div>
        <div class="mt-3">
            <div class="flex justify-between text-xs mb-1">
                <span style="color: var(--text-secondary);">Inactive Rate</span>
                <span style="color: var(--text-secondary);" id="statInactiveMentorsPercent">0%</span>
            </div>
            <div class="w-full rounded-full h-2" style="background: var(--light-gray);">
                <div class="h-2 rounded-full transition-all duration-500" id="inactiveProgressBar" style="width: 0%; background: #ec4899;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Main Mentor List Card -->
<div class="rounded-2xl shadow-lg overflow-hidden" style="background: var(--card-bg);">
    <!-- Header -->
    <div class="p-6 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="background: linear-gradient(90deg, var(--light-purple) 0%, var(--card-bg) 100%); border-color: var(--border-color);">
        <div>
            <h3 class="text-xl font-bold" style="color: var(--text-primary);">
                <i class="fas fa-hands-helping mr-2" style="color: var(--purple);"></i>Mentor Directory
            </h3>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">Manage and oversee all registered mentors</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-3" style="color: var(--text-secondary);"></i>
                <input type="text" id="searchInput" placeholder="Search by name, email, or expertise..."
                       class="pl-11 pr-4 py-2.5 w-full sm:w-80 rounded-xl border focus:ring-2 transition-all"
                       style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
            </div>
            <a href="{{ route('admin.mentors.create') }}" class="px-5 py-2.5 rounded-xl text-white flex items-center gap-2 transition shadow-md hover:shadow-lg" style="background: var(--purple);">
                <i class="fas fa-plus-circle"></i> Add New Mentor
            </a>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="p-4 border-b flex flex-wrap gap-3 items-center" style="border-color: var(--border-color); background: var(--light-gray);">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm" style="color: var(--text-secondary);"><i class="fas fa-filter mr-1"></i> Filter:</span>
            <button class="status-filter px-3 py-1.5 text-sm rounded-full transition-all" data-status="all" style="background: var(--purple); color: white;">All</button>
            <button class="status-filter px-3 py-1.5 text-sm rounded-full transition-all" data-status="active" style="background: var(--light-gray); color: var(--text-primary);">Active</button>
            <button class="status-filter px-3 py-1.5 text-sm rounded-full transition-all" data-status="pending" style="background: var(--light-gray); color: var(--text-primary);">Pending</button>
            <button class="status-filter px-3 py-1.5 text-sm rounded-full transition-all" data-status="inactive" style="background: var(--light-gray); color: var(--text-primary);">Inactive</button>
        </div>
        <div class="flex-1"></div>
        <div class="flex items-center gap-2">
            <span class="text-sm" style="color: var(--text-secondary);">Show:</span>
            <select id="perPageSelect" class="border rounded-lg px-3 py-1.5 text-sm focus:ring-2" style="border-color: var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- Mentors Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="border-b" style="background: var(--light-gray); border-color: var(--border-color);">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Mentor</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Expertise</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Contact</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Availability</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Joined</th>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Actions</th>
                </tr>
            </thead>
            <tbody id="mentorsTableBody" class="divide-y" style="border-color: var(--border-color);">
                <tr>
                    <td colspan="7" class="text-center py-12">
                        <div class="flex flex-col items-center gap-3">
                            <i class="fas fa-spinner fa-spin text-3xl" style="color: var(--purple);"></i>
                            <p style="color: var(--text-secondary);">Loading mentors...</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" style="border-color: var(--border-color); background: var(--light-gray);">
        <div class="text-sm" id="paginationInfo" style="color: var(--text-secondary);">
            Showing 0 to 0 of 0 results
        </div>
        <div class="flex gap-2" id="paginationButtons"></div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="modal-overlay absolute inset-0" id="modalOverlay"></div>
    <div class="relative rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all" style="background: var(--card-bg);">
        <div class="p-6 text-center">
            <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-4" style="background: var(--light-red);">
                <i class="fas fa-trash-alt text-2xl" style="color: var(--red);"></i>
            </div>
            <h3 class="text-xl font-bold mb-2" style="color: var(--text-primary);">Delete Mentor</h3>
            <p class="mb-6" id="deleteModalMessage" style="color: var(--text-secondary);">Are you sure you want to delete this mentor? This action cannot be undone.</p>
            <div class="flex gap-3 justify-center">
                <button id="cancelDeleteBtn" class="px-5 py-2.5 border rounded-lg font-medium transition" style="border-color: var(--border-color); color: var(--text-primary); background: var(--card-bg);">Cancel</button>
                <button id="confirmDeleteBtn" class="px-5 py-2.5 rounded-lg font-medium transition" style="background: var(--red); color: white;">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .toast {
        visibility: hidden;
        min-width: 250px;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 16px;
        position: fixed;
        z-index: 9999;
        left: 50%;
        bottom: 30px;
        font-size: 14px;
        transform: translateX(-50%);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .toast.show {
        visibility: visible;
        animation: fadein 0.4s, fadeout 0.5s 2.5s;
    }
    @keyframes fadein  { from { bottom: 0; opacity: 0; } to { bottom: 30px; opacity: 1; } }
    @keyframes fadeout { from { bottom: 30px; opacity: 1; } to { bottom: 0; opacity: 0; } }

    .modal-overlay {
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(2px);
    }

    .number-update {
        animation: numPulse 0.3s ease-in-out;
    }
    @keyframes numPulse {
        0%   { transform: scale(1); }
        50%  { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .badge-purple  { background: var(--light-purple); color: var(--purple); }
    .badge-success { background: #dcfce7; color: #16a34a; }
    .badge-warning { background: #fef9c3; color: #ca8a04; }
    .badge-danger  { background: #fee2e2; color: #dc2626; }
</style>
@endpush

@push('scripts')
<script>
// ─── Helpers ──────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    document.querySelectorAll('.toast').forEach(t => t.remove());
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toast.style.backgroundColor = type === 'error' ? '#dc2626' : '#10b981';
    document.body.appendChild(toast);
    toast.offsetHeight; // reflow
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 500); }, 3000);
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

function animateNumber(el, start, end, duration = 500, isPercent = false) {
    if (!el) return;
    const increment = (end - start) / (duration / 16);
    let current = start;
    const timer = setInterval(() => {
        current += increment;
        const done = increment >= 0 ? current >= end : current <= end;
        if (done) {
            clearInterval(timer);
            el.textContent = isPercent ? end + '%' : end;
            el.classList.add('number-update');
            setTimeout(() => el.classList.remove('number-update'), 300);
        } else {
            el.textContent = isPercent ? Math.round(current) + '%' : Math.round(current);
        }
    }, 16);
}

// ─── State ────────────────────────────────────────────────────────────────────
let currentPage    = 1;
let currentStatus  = 'all';
let currentSearch  = '';
let currentPerPage = 10;
let deleteMentorId = null;

// ─── Fetch & render mentors ───────────────────────────────────────────────────
async function fetchMentors() {
    const tbody = document.getElementById('mentorsTableBody');
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12">
            <div class="flex flex-col items-center gap-3">
                <i class="fas fa-spinner fa-spin text-3xl" style="color: var(--purple);"></i>
                <p style="color: var(--text-secondary);">Loading mentors...</p>
            </div></td></tr>`;
    }

    try {
        const params = new URLSearchParams({
            page:     currentPage,
            per_page: currentPerPage,
            status:   currentStatus,
            search:   currentSearch,
        });

        const response = await fetch(`{{ route('admin.mentors.index') }}?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();

        if (data.success !== false) {
            updateStats(data.stats || {});
            renderMentorsTable(data.mentors || []);
            renderPagination(data);
        }
    } catch (err) {
        console.error('fetchMentors error:', err);
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12">
                <i class="fas fa-exclamation-circle text-3xl mb-2 block" style="color: var(--red);"></i>
                <p style="color: var(--text-secondary);">Error loading mentors. Please refresh the page.</p>
            </td></tr>`;
        }
    }
}

// ─── Stats update ─────────────────────────────────────────────────────────────
function updateStats(stats) {
    const total    = stats.total    || 0;
    const active   = stats.active   || 0;
    const pending  = stats.pending  || 0;
    const inactive = stats.inactive || 0;

    const activePercent   = total ? Math.round((active   / total) * 100) : 0;
    const pendingPercent  = total ? Math.round((pending  / total) * 100) : 0;
    const inactivePercent = total ? Math.round((inactive / total) * 100) : 0;

    // Counts
    animateNumber(document.getElementById('statTotalMentorsCount'),    0, total,    600);
    animateNumber(document.getElementById('statActiveMentorsCount'),   0, active,   600);
    animateNumber(document.getElementById('statPendingMentorsCount'),  0, pending,  600);
    animateNumber(document.getElementById('statInactiveMentorsCount'), 0, inactive, 600);

    // Percentages
    animateNumber(document.getElementById('statTotalMentorsPercent'),    0, 100,           600, true);
    animateNumber(document.getElementById('statActiveMentorsPercent'),   0, activePercent,  600, true);
    animateNumber(document.getElementById('statPendingMentorsPercent'),  0, pendingPercent, 600, true);
    animateNumber(document.getElementById('statInactiveMentorsPercent'), 0, inactivePercent,600, true);

    // Progress bars
    setTimeout(() => {
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.style.width = val + '%'; };
        set('totalProgressBar',    100);
        set('activeProgressBar',   activePercent);
        set('pendingProgressBar',  pendingPercent);
        set('inactiveProgressBar', inactivePercent);
    }, 150);
}

// ─── Table render ─────────────────────────────────────────────────────────────
function renderMentorsTable(mentors) {
    const tbody = document.getElementById('mentorsTableBody');
    if (!tbody) return;

    if (!mentors.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-12">
            <i class="fas fa-users-slash text-3xl mb-2 block" style="color: var(--purple);"></i>
            <p style="color: var(--text-secondary);">No mentors found matching your criteria.</p>
        </td></tr>`;
        return;
    }

    tbody.innerHTML = mentors.map(mentor => {
        // ── Expertise: handle both relationship array and plain array ──
        let expertiseArray = [];
        if (Array.isArray(mentor.expertises) && mentor.expertises.length) {
            // Loaded via ->with('expertises') — each item is an object with a name property
            expertiseArray = mentor.expertises.map(e => (typeof e === 'object' && e.name) ? e.name : e);
        } else if (Array.isArray(mentor.expertise)) {
            expertiseArray = mentor.expertise;
        } else if (typeof mentor.expertise === 'string') {
            try { expertiseArray = JSON.parse(mentor.expertise); } catch (e) { expertiseArray = []; }
        }

        const photoUrl = mentor.photo
            ? `/storage/${mentor.photo}`
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=9b59b6&color=fff&size=40`;

        const statusBadge = mentor.status === 'active'
            ? '<span class="badge-success text-xs px-3 py-1 rounded-full font-medium">Active</span>'
            : mentor.status === 'pending'
                ? '<span class="badge-warning text-xs px-3 py-1 rounded-full font-medium">Pending</span>'
                : '<span class="badge-danger text-xs px-3 py-1 rounded-full font-medium">Inactive</span>';

        const joinDate = mentor.created_at
            ? new Date(mentor.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
            : '—';

        const expertiseTags = expertiseArray.slice(0, 2)
            .map(e => `<span class="inline-block text-xs px-2 py-1 rounded-full badge-purple">${escapeHtml(e)}</span>`)
            .join('');
        const extraTag = expertiseArray.length > 2
            ? `<span class="inline-block text-xs px-2 py-1 rounded-full" style="background: var(--light-gray); color: var(--text-secondary);">+${expertiseArray.length - 2}</span>`
            : '';

        return `
            <tr class="transition" style="background: var(--card-bg);" data-mentor-id="${mentor.id}">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <img src="${escapeHtml(photoUrl)}" class="w-10 h-10 rounded-full object-cover border" style="border-color: var(--border-color);" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(mentor.name)}&background=9b59b6&color=fff&size=40'">
                        <div>
                            <p class="font-semibold" style="color: var(--text-primary);">${escapeHtml(mentor.name)}</p>
                            <p class="text-xs" style="color: var(--text-secondary);">ID: ${mentor.id}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                        ${expertiseTags}${extraTag}
                        ${!expertiseArray.length ? '<span class="text-xs" style="color: var(--text-secondary);">None</span>' : ''}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm" style="color: var(--text-primary);">${escapeHtml(mentor.email)}</div>
                    <div class="text-xs" style="color: var(--text-secondary);">${escapeHtml(mentor.phone || 'No phone')}</div>
                </td>
                <td class="px-6 py-4 text-sm" style="color: var(--text-primary); max-width: 150px;">
                    <div class="truncate">${escapeHtml(mentor.availability || 'Not specified')}</div>
                </td>
                <td class="px-6 py-4">${statusBadge}</td>
                <td class="px-6 py-4 text-sm" style="color: var(--text-secondary);">${joinDate}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="/admin/mentors/${mentor.id}"
                           class="p-2 rounded-lg transition"
                           style="color: var(--blue);"
                           onmouseover="this.style.background='var(--light-blue)'"
                           onmouseout="this.style.background='transparent'"
                           title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/admin/mentors/${mentor.id}/edit"
                           class="p-2 rounded-lg transition"
                           style="color: var(--orange);"
                           onmouseover="this.style.background='var(--light-orange)'"
                           onmouseout="this.style.background='transparent'"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="delete-mentor-btn p-2 rounded-lg transition"
                                style="color: var(--red);"
                                onmouseover="this.style.background='var(--light-red)'"
                                onmouseout="this.style.background='transparent'"
                                data-id="${mentor.id}"
                                data-name="${escapeHtml(mentor.name)}"
                                title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
    }).join('');

    // Attach delete listeners
    document.querySelectorAll('.delete-mentor-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            deleteMentorId = btn.dataset.id;
            const msgEl = document.getElementById('deleteModalMessage');
            if (msgEl) msgEl.innerHTML = `Are you sure you want to delete <strong>${escapeHtml(btn.dataset.name)}</strong>? This action cannot be undone.`;
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });
}

// ─── Pagination render ────────────────────────────────────────────────────────
function renderPagination(data) {
    const { current_page, last_page, total, from, to } = data;

    const infoEl = document.getElementById('paginationInfo');
    if (infoEl) infoEl.textContent = `Showing ${from || 0} to ${to || 0} of ${total || 0} results`;

    const container = document.getElementById('paginationButtons');
    if (!container) return;
    if (!last_page || last_page <= 1) { container.innerHTML = ''; return; }

    const btnClass = (page, active = false, disabled = false) =>
        `<button class="pagination-btn px-3 py-1.5 rounded-lg border transition ${disabled ? 'opacity-50 cursor-not-allowed' : ''} ${active ? 'text-white' : ''}"
         style="${active ? 'background: var(--purple); border-color: var(--purple);' : 'border-color: var(--border-color); color: var(--text-primary);'}"
         data-page="${page}" ${disabled ? 'disabled' : ''}>`;

    let html = btnClass(current_page - 1, false, current_page === 1) + '<i class="fas fa-chevron-left"></i></button>';

    const start = Math.max(1, current_page - 2);
    const end   = Math.min(last_page, current_page + 2);

    if (start > 1) {
        html += btnClass(1) + '1</button>';
        if (start > 2) html += `<span class="px-2" style="color:var(--text-secondary);">…</span>`;
    }
    for (let i = start; i <= end; i++) {
        html += btnClass(i, i === current_page) + i + '</button>';
    }
    if (end < last_page) {
        if (end < last_page - 1) html += `<span class="px-2" style="color:var(--text-secondary);">…</span>`;
        html += btnClass(last_page) + last_page + '</button>';
    }

    html += btnClass(current_page + 1, false, current_page === last_page) + '<i class="fas fa-chevron-right"></i></button>';

    container.innerHTML = html;

    container.querySelectorAll('.pagination-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const page = parseInt(btn.dataset.page);
            if (!isNaN(page) && page !== current_page && page >= 1 && page <= last_page) {
                currentPage = page;
                fetchMentors();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });
}

// ─── Delete ───────────────────────────────────────────────────────────────────
async function deleteMentor(id) {
    if (!id) return;
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) { confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...'; confirmBtn.disabled = true; }

    try {
        const response = await fetch(`/admin/mentors/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'Content-Type':     'application/json',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        });
        const data = await response.json();
        if (response.ok && data.success) {
            showToast('Mentor deleted successfully!');
            await fetchMentors();
        } else {
            showToast(data.message || 'Failed to delete mentor', 'error');
        }
    } catch (err) {
        showToast('An error occurred while deleting the mentor', 'error');
    } finally {
        if (confirmBtn) { confirmBtn.innerHTML = 'Delete'; confirmBtn.disabled = false; }
    }
}

// ─── Modal helpers ────────────────────────────────────────────────────────────
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    deleteMentorId = null;
}

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    fetchMentors();

    // Search with debounce
    let searchTimeout;
    document.getElementById('searchInput')?.addEventListener('input', e => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = e.target.value.trim();
            currentPage   = 1;
            fetchMentors();
        }, 300);
    });

    // Status filter buttons
    document.querySelectorAll('.status-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.status-filter').forEach(b => {
                b.style.background = 'var(--light-gray)';
                b.style.color      = 'var(--text-primary)';
            });
            btn.style.background = 'var(--purple)';
            btn.style.color      = 'white';
            currentStatus = btn.dataset.status;
            currentPage   = 1;
            fetchMentors();
        });
    });

    // Per-page select
    document.getElementById('perPageSelect')?.addEventListener('change', e => {
        currentPerPage = parseInt(e.target.value);
        currentPage    = 1;
        fetchMentors();
    });

    // Modal actions
    document.getElementById('modalOverlay')?.addEventListener('click', closeDeleteModal);
    document.getElementById('cancelDeleteBtn')?.addEventListener('click', closeDeleteModal);
    document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
        if (deleteMentorId) { await deleteMentor(deleteMentorId); closeDeleteModal(); }
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeDeleteModal();
    });
});
</script>
@endpush