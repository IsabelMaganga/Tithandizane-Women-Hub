@extends('admin.layouts.admin')
@section('title', 'Harassment Reports')
@section('page-title', 'Harassment Reports Management')
@section('page-subtitle', 'View and manage all harassment reports')

@push('styles')
<style>
    /* Status Badges - Using CSS Variables */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-pending { background: var(--light-orange); color: var(--orange); }
    .status-reviewing { background: var(--light-blue); color: var(--blue); }
    .status-assigned { background: var(--light-purple); color: var(--purple); }
    .status-resolved { background: var(--light-teal); color: var(--teal-green); }
    .status-dismissed { background: var(--light-red); color: var(--red); }
    
    /* Type Badges - Using CSS Variables */
    .type-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .type-physical { background: var(--light-purple); color: var(--purple); }
    .type-verbal { background: var(--light-red); color: var(--red); }
    .type-sexual { background: var(--light-orange); color: var(--orange); }
    .type-cyber { background: var(--light-blue); color: var(--blue); }
    .type-other { background: var(--light-gray); color: var(--text-secondary); }
    
    /* Stats Cards - Using CSS Variables */
    .stat-card {
        background: var(--card-bg);
        border-radius: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    }
    
    /* Filter Section */
    .filter-section {
        background: var(--card-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    
    /* Table Styles */
    .reports-table {
        background: var(--card-bg);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .reports-table thead th {
        background: var(--light-gray);
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }
    
    .reports-table tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: background 0.2s ease;
    }
    
    .reports-table tbody tr:hover {
        background: var(--light-gray);
    }
    
    .reports-table td {
        color: var(--text-primary);
    }
    
    /* Report Card */
    .report-card {
        transition: all 0.3s ease;
    }
    
    /* Form Inputs */
    .form-input, 
    .form-select,
    input[type="text"],
    input[type="date"],
    select {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        transition: all 0.2s ease;
    }
    
    .form-input:focus,
    input[type="text"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: var(--purple);
        box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.1);
        outline: none;
    }
    
    /* Button Styles */
    .btn-purple {
        background: var(--purple);
        color: white;
        transition: all 0.2s ease;
    }
    
    .btn-purple:hover {
        background: #7c3aed;
        transform: translateY(-1px);
    }
    
    .btn-gray {
        background: var(--light-gray);
        color: var(--text-primary);
        transition: all 0.2s ease;
    }
    
    .btn-gray:hover {
        background: var(--border-color);
        transform: translateY(-1px);
    }
    
    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        padding: 16px;
    }
    
    .pagination .page-item {
        list-style: none;
    }
    
    .pagination .page-link {
        padding: 8px 12px;
        border-radius: 8px;
        background: var(--bg-secondary);
        color: var(--text-primary);
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid var(--border-color);
    }
    
    .pagination .page-item.active .page-link {
        background: var(--purple);
        color: white;
        border-color: var(--purple);
    }
    
    .pagination .page-link:hover {
        background: var(--light-gray);
        transform: translateY(-1px);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-secondary);
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <!-- Pending -->
        <div class="stat-card p-4 border-l-4" style="border-left-color: var(--orange);">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Pending</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);">{{ $stats['pending'] }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: var(--light-orange);">
                    <i class="fas fa-clock" style="color: var(--orange);"></i>
                </div>
            </div>
            <div class="mt-2">
                <div class="w-full rounded-full h-1" style="background: var(--light-orange);">
                    <div class="h-1 rounded-full" style="width: {{ $stats['total'] > 0 ? ($stats['pending'] / $stats['total']) * 100 : 0 }}%; background: var(--orange);"></div>
                </div>
            </div>
        </div>

        <!-- Reviewing -->
        <div class="stat-card p-4 border-l-4" style="border-left-color: var(--blue);">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Reviewing</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);">{{ $stats['reviewing'] }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: var(--light-blue);">
                    <i class="fas fa-search" style="color: var(--blue);"></i>
                </div>
            </div>
        </div>

        <!-- Assigned -->
        <div class="stat-card p-4 border-l-4" style="border-left-color: var(--purple);">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Assigned</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);">{{ $stats['assigned'] }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: var(--light-purple);">
                    <i class="fas fa-user-check" style="color: var(--purple);"></i>
                </div>
            </div>
        </div>

        <!-- Resolved -->
        <div class="stat-card p-4 border-l-4" style="border-left-color: var(--teal-green);">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Resolved</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);">{{ $stats['resolved'] }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: var(--light-teal);">
                    <i class="fas fa-check-circle" style="color: var(--teal-green);"></i>
                </div>
            </div>
        </div>

        <!-- Dismissed -->
        <div class="stat-card p-4 border-l-4" style="border-left-color: var(--red);">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Dismissed</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);">{{ $stats['dismissed'] }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: var(--light-red);">
                    <i class="fas fa-times-circle" style="color: var(--red);"></i>
                </div>
            </div>
        </div>

        <!-- Anonymous -->
        <div class="stat-card p-4 border-l-4" style="border-left-color: var(--text-secondary);">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" style="color: var(--text-secondary);">Anonymous</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);">{{ $stats['anonymous'] }}</p>
                </div>
                <div class="p-2 rounded-lg" style="background: var(--light-gray);">
                    <i class="fas fa-user-secret" style="color: var(--text-secondary);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section mb-6 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Search</label>
                <input type="text" name="search" placeholder="Search by reference, title..." 
                       value="{{ request('search') }}" 
                       class="form-input w-full rounded-lg px-3 py-2 text-sm">
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Status</label>
                <select name="status" class="form-select w-full rounded-lg px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">Type</label>
                <select name="type" class="form-select w-full rounded-lg px-3 py-2 text-sm">
                    <option value="">All Types</option>
                    <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Physical</option>
                    <option value="verbal" {{ request('type') == 'verbal' ? 'selected' : '' }}>Verbal</option>
                    <option value="sexual" {{ request('type') == 'sexual' ? 'selected' : '' }}>Sexual</option>
                    <option value="cyber" {{ request('type') == 'cyber' ? 'selected' : '' }}>Cyber</option>
                    <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                       class="form-input w-full rounded-lg px-3 py-2 text-sm">
            </div>
            
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--text-secondary);">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                       class="form-input w-full rounded-lg px-3 py-2 text-sm">
            </div>
            
            <div class="md:col-span-2 lg:col-span-5 flex justify-end gap-2 mt-2">
                <button type="submit" class="btn-purple px-5 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.reports.index') }}" class="btn-gray px-5 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div class="reports-table">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Ref #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-color);">
                    @forelse($reports as $report)
                    <tr class="report-card transition" style="cursor: pointer;" onclick="window.location.href='{{ route('admin.reports.show', $report->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium" style="color: var(--text-primary);">{{ $report->reference_number }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="type-badge type-{{ $report->incident_type }}">
                                {{ ucfirst($report->incident_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium max-w-xs truncate" style="color: var(--text-primary);">{{ $report->incident_title }}</div>
                            @if($report->is_anonymous)
                                <span class="text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-user-secret"></i> Anonymous
                                </span>
                            @else
                                <span class="text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-user"></i> {{ $report->victim_name ?? $report->user?->name ?? 'N/A' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="status-badge status-{{ $report->status }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($report->assignedMentor)
                                <span class="text-sm" style="color: var(--text-primary);">{{ $report->assignedMentor->name }}</span>
                            @else
                                <span class="text-sm" style="color: var(--text-secondary);">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: var(--text-secondary);">
                            {{ $report->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.reports.show', $report->id) }}" 
                               class="transition mr-3" style="color: var(--purple);">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p class="mt-2">No reports found</p>
                            <p class="text-sm mt-1">Try adjusting your filters or check back later</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reports->hasPages())
        <div class="border-t" style="border-color: var(--border-color);">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Add click handler for table rows to make them clickable
    document.querySelectorAll('.report-card').forEach(row => {
        const viewLink = row.querySelector('a[href*="reports/"]');
        if (viewLink) {
            const url = viewLink.getAttribute('href');
            row.style.cursor = 'pointer';
            row.addEventListener('click', (e) => {
                // Don't trigger if clicking on the link itself
                if (!e.target.closest('a')) {
                    window.location.href = url;
                }
            });
        }
    });
</script>
@endpush
@endsection