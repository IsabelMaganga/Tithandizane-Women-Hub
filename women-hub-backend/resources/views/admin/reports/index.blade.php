
@extends('admin.layouts.admin')

@section('title', 'Harassment Reports')

@section('page-title', 'Harassment Reports Management')
@section('page-subtitle', 'View and respond to harassment reports')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="card-bg rounded-xl p-5 shadow-sm border border-color">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-secondary-color text-sm">Total Reports</p>
                    <h3 class="text-2xl font-bold text-primary-color mt-1">{{ $stats['total'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-flag fa-2x text-blue-500 opacity-50"></i>
            </div>
        </div>
        
        <div class="card-bg rounded-xl p-5 shadow-sm border border-color">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-secondary-color text-sm">Pending</p>
                    <h3 class="text-2xl font-bold text-primary-color mt-1">{{ $stats['pending'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-clock fa-2x text-yellow-500 opacity-50"></i>
            </div>
        </div>
        
        <div class="card-bg rounded-xl p-5 shadow-sm border border-color">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-secondary-color text-sm">Reviewing</p>
                    <h3 class="text-2xl font-bold text-primary-color mt-1">{{ $stats['reviewing'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-search fa-2x text-blue-500 opacity-50"></i>
            </div>
        </div>
        
        <div class="card-bg rounded-xl p-5 shadow-sm border border-color">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-secondary-color text-sm">Resolved</p>
                    <h3 class="text-2xl font-bold text-primary-color mt-1">{{ $stats['resolved'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-check-circle fa-2x text-green-500 opacity-50"></i>
            </div>
        </div>
        
        <div class="card-bg rounded-xl p-5 shadow-sm border border-color">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-secondary-color text-sm">Dismissed</p>
                    <h3 class="text-2xl font-bold text-primary-color mt-1">{{ $stats['dismissed'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-ban fa-2x text-red-500 opacity-50"></i>
            </div>
        </div>
        
        <div class="card-bg rounded-xl p-5 shadow-sm border border-color">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-secondary-color text-sm">Anonymous</p>
                    <h3 class="text-2xl font-bold text-primary-color mt-1">{{ $stats['anonymous'] ?? 0 }}</h3>
                </div>
                <i class="fas fa-user-secret fa-2x text-purple-500 opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card-bg rounded-xl shadow-sm border border-color">
        <div class="p-6 border-b border-color">
            <h3 class="text-lg font-semibold text-primary-color">
                <i class="fas fa-filter mr-2"></i> Filter Reports
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">Search</label>
                    <input type="text" name="search" class="w-full px-3 py-2 border border-color rounded-lg bg-card-bg" 
                           placeholder="Search reports..." value="{{ request('search') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-color rounded-lg bg-card-bg">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-color rounded-lg bg-card-bg">
                        <option value="">All Types</option>
                        <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Physical</option>
                        <option value="verbal" {{ request('type') == 'verbal' ? 'selected' : '' }}>Verbal</option>
                        <option value="sexual" {{ request('type') == 'sexual' ? 'selected' : '' }}>Sexual</option>
                        <option value="cyber" {{ request('type') == 'cyber' ? 'selected' : '' }}>Cyber</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">From Date</label>
                    <input type="date" name="from_date" class="w-full px-3 py-2 border border-color rounded-lg bg-card-bg" 
                           value="{{ request('from_date') }}">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">To Date</label>
                    <input type="date" name="to_date" class="w-full px-3 py-2 border border-color rounded-lg bg-card-bg" 
                           value="{{ request('to_date') }}">
                </div>
                
                <div class="md:col-span-5 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </a>
                    <a href="{{ route('admin.reports.export', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-download mr-2"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card-bg rounded-xl shadow-sm border border-color">
        <div class="p-6 border-b border-color">
            <h3 class="text-lg font-semibold text-primary-color">
                <i class="fas fa-table mr-2"></i> Reports List
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr class="border-b border-color">
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-color uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-color uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-color uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-color uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-color uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-color uppercase tracking-wider">Reporter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-color uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-color uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-color">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-6 py-4 text-sm text-primary-color">{{ $report->id }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-primary-color">HR-{{ str_pad($report->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 text-sm text-primary-color">{{ Str::limit($report->incident_title, 40) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($report->incident_type == 'physical') bg-purple-100 text-purple-700
                                @elseif($report->incident_type == 'verbal') bg-red-100 text-red-700
                                @elseif($report->incident_type == 'sexual') bg-orange-100 text-orange-700
                                @elseif($report->incident_type == 'cyber') bg-teal-100 text-teal-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ ucfirst($report->incident_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($report->status == 'pending') bg-yellow-100 text-yellow-700
                                @elseif($report->status == 'reviewing') bg-blue-100 text-blue-700
                                @elseif($report->status == 'resolved') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-primary-color">
                            @if($report->is_anonymous)
                                <i class="fas fa-user-secret text-gray-400"></i> Anonymous
                            @else
                                <i class="fas fa-user text-blue-500"></i> {{ Str::limit($report->victim_name ?? 'N/A', 20) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-primary-color">{{ $report->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <button onclick="viewReport({{ $report->id }})" class="text-blue-600 hover:text-blue-800 mr-3" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="respondToReport({{ $report->id }})" class="text-green-600 hover:text-green-800" title="Respond">
                                <i class="fas fa-reply"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-secondary-color">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <p>No reports found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-color">
            {{ $reports->links() }}
        </div>
    </div>
</div>

<!-- View Report Modal -->
<div id="viewReportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="card-bg rounded-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-color flex justify-between items-center sticky top-0 bg-card-bg">
            <h3 class="text-xl font-semibold text-primary-color">Report Details</h3>
            <button onclick="closeViewModal()" class="text-secondary-color hover:text-primary-color">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6" id="reportDetails">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin fa-2x text-blue-500"></i>
                <p class="mt-2 text-secondary-color">Loading...</p>
            </div>
        </div>
        <div class="p-6 border-t border-color flex justify-end sticky bottom-0 bg-card-bg">
            <button onclick="closeViewModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Respond Modal -->
<div id="respondModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="card-bg rounded-xl max-w-2xl w-full mx-4">
        <form id="respondForm" method="POST" action="">
            @csrf
            <div class="p-6 border-b border-color flex justify-between items-center">
                <h3 class="text-xl font-semibold text-primary-color">Respond to Report</h3>
                <button type="button" onclick="closeRespondModal()" class="text-secondary-color hover:text-primary-color">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">Your Response *</label>
                    <textarea name="response" rows="6" class="w-full px-3 py-2 border border-color rounded-lg bg-card-bg text-primary-color" 
                              placeholder="Provide guidance, support, or next steps for the victim..." required></textarea>
                    <p class="text-xs text-secondary-color mt-1">Minimum 10 characters</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary-color mb-2">Update Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-color rounded-lg bg-card-bg text-primary-color" required>
                        <option value="reviewing">Mark as Reviewing</option>
                        <option value="resolved">Mark as Resolved</option>
                        <option value="dismissed">Dismiss</option>
                    </select>
                </div>
            </div>
            <div class="p-6 border-t border-color flex justify-end gap-3">
                <button type="button" onclick="closeRespondModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Send Response
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentReportId = null;
    
    function viewReport(id) {
        currentReportId = id;
        const modal = document.getElementById('viewReportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        fetch(`/admin/reports/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const report = data.data;
                const html = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-secondary-color">Reference Number</label>
                                <p class="text-primary-color font-mono">HR-${String(report.id).padStart(6, '0')}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-secondary-color">Status</label>
                                <p class="text-primary-color capitalize">${report.status}</p>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-secondary-color">Title</label>
                            <p class="text-primary-color">${escapeHtml(report.incident_title)}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-secondary-color">Type</label>
                                <p class="text-primary-color capitalize">${report.incident_type}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-secondary-color">Incident Date</label>
                                <p class="text-primary-color">${report.incident_date}</p>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-secondary-color">Location</label>
                            <p class="text-primary-color">${escapeHtml(report.incident_location)}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-secondary-color">Description</label>
                            <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-primary-color whitespace-pre-wrap">${escapeHtml(report.incident_description)}</p>
                            </div>
                        </div>
                        ${report.perpetrator_info ? `
                        <div>
                            <label class="text-sm font-medium text-secondary-color">Perpetrator Information</label>
                            <p class="text-primary-color">${escapeHtml(report.perpetrator_info)}</p>
                        </div>
                        ` : ''}
                        ${!report.is_anonymous ? `
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <label class="text-sm font-medium text-secondary-color">Victim Contact Information</label>
                            <div class="mt-2 space-y-1">
                                <p class="text-primary-color"><strong>Name:</strong> ${escapeHtml(report.victim_name)}</p>
                                <p class="text-primary-color"><strong>Email:</strong> ${escapeHtml(report.victim_email)}</p>
                                ${report.victim_phone ? `<p class="text-primary-color"><strong>Phone:</strong> ${escapeHtml(report.victim_phone)}</p>` : ''}
                            </div>
                        </div>
                        ` : ''}
                        ${report.admin_response ? `
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <label class="text-sm font-medium text-secondary-color">Your Response</label>
                            <p class="text-primary-color mt-1">${escapeHtml(report.admin_response)}</p>
                            <p class="text-xs text-secondary-color mt-2">Responded on: ${new Date(report.responded_at).toLocaleString()}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                document.getElementById('reportDetails').innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('reportDetails').innerHTML = '<div class="text-center text-red-500">Failed to load report details</div>';
        });
    }
    
    function closeViewModal() {
        const modal = document.getElementById('viewReportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('reportDetails').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin fa-2x text-blue-500"></i><p class="mt-2 text-secondary-color">Loading...</p></div>';
    }
    
    function respondToReport(id) {
        currentReportId = id;
        const modal = document.getElementById('respondModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('respondForm').action = `/admin/reports/${id}/respond`;
    }
    
    function closeRespondModal() {
        const modal = document.getElementById('respondModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('respondForm').reset();
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Close modals when clicking outside
    document.getElementById('viewReportModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeViewModal();
    });
    
    document.getElementById('respondModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRespondModal();
    });
</script>
@endpush