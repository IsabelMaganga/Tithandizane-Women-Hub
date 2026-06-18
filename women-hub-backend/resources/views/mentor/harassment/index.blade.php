@extends('mentor.layouts.dashboard')
@section('title') Assigned Reports @endsection

@push('styles')
<style>
    .stat-pill {
        display: flex; flex-direction: column; align-items: center;
        background: white; border-radius: 12px; padding: 14px 20px;
        border: 1px solid #e5e7eb; min-width: 100px;
    }
    .stat-pill .num  { font-size: 24px; font-weight: 800; color: #7c3aed; }
    .stat-pill .lbl  { font-size: 11px; color: #6b7280; margin-top: 2px; text-transform: uppercase; letter-spacing: .04em; }

    .report-row {
        background: white; border: 1px solid #e5e7eb; border-radius: 12px;
        padding: 16px 20px; margin-bottom: 10px; display: flex;
        align-items: center; gap: 14px; transition: box-shadow .15s;
    }
    .report-row:hover { box-shadow: 0 4px 16px rgba(124,58,237,.1); border-color: #c4b5fd; }

    .type-chip {
        font-size: 11px; font-weight: 600; padding: 3px 10px;
        border-radius: 20px; text-transform: capitalize; white-space: nowrap;
    }
    .type-physical { background:#ede9fe; color:#7c3aed; }
    .type-verbal   { background:#fef3c7; color:#d97706; }
    .type-sexual   { background:#fce7f3; color:#db2777; }
    .type-cyber    { background:#dbeafe; color:#2563eb; }
    .type-other    { background:#f3f4f6; color:#6b7280; }

    .status-chip {
        font-size: 11px; font-weight: 700; padding: 3px 10px;
        border-radius: 20px; white-space: nowrap;
    }
    .status-assigned  { background:#ede9fe; color:#7c3aed; }
    .status-reviewing { background:#dbeafe; color:#2563eb; }
    .status-resolved  { background:#d1fae5; color:#059669; }
    .status-dismissed { background:#f3f4f6; color:#6b7280; }
    .status-pending   { background:#fef3c7; color:#d97706; }

    .anon-tag {
        font-size: 10px; font-weight: 600; padding: 2px 8px;
        background: #f3f4f6; color: #374151; border-radius: 20px;
    }
    .response-tag {
        font-size: 10px; font-weight: 600; padding: 2px 8px;
        background: #d1fae5; color: #059669; border-radius: 20px;
    }

    .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }
</style>
@endpush

@section('content')
<div class="p-6 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Assigned Harassment Reports</h1>
        <p class="text-sm text-gray-500 mt-1">Reports the admin has assigned to you. Read each one and write your response.</p>
    </div>

    {{-- Stats --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <div class="stat-pill">
            <span class="num">{{ $stats['total'] }}</span>
            <span class="lbl">Total Assigned</span>
        </div>
        <div class="stat-pill">
            <span class="num" style="color:#2563eb;">{{ $stats['pending'] }}</span>
            <span class="lbl">Needs Response</span>
        </div>
        <div class="stat-pill">
            <span class="num" style="color:#059669;">{{ $stats['resolved'] }}</span>
            <span class="lbl">Resolved</span>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 mb-5 text-sm font-medium">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Reports list --}}
    @if($reports->isEmpty())
    <div class="empty-state bg-white rounded-2xl border border-gray-100">
        <i class="fas fa-clipboard-list text-gray-300"></i>
        <p class="text-gray-500 font-semibold text-lg">No reports assigned yet</p>
        <p class="text-gray-400 text-sm mt-1">The admin will assign reports to you when they need your support.</p>
    </div>
    @else
    @foreach($reports as $report)
    <a href="{{ route('mentor.harassment.show', $report->id) }}" class="report-row block no-underline" style="text-decoration:none;">
        <div style="flex-shrink:0;">
            <div style="width:42px;height:42px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-file-alt" style="color:#7c3aed;font-size:18px;"></i>
            </div>
        </div>
        <div style="flex:1;min-width:0;">
            <div class="flex items-start justify-between gap-2 flex-wrap">
                <p class="font-semibold text-gray-900 text-sm truncate" style="max-width:420px;">{{ $report->incident_title }}</p>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($report->admin_response)
                        <span class="response-tag"><i class="fas fa-check"></i> Responded</span>
                    @endif
                    <span class="status-chip status-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-1 flex-wrap">
                <span class="type-chip type-{{ $report->incident_type }}">{{ ucfirst(str_replace('_',' ',$report->incident_type)) }}</span>
                @if($report->is_anonymous)
                    <span class="anon-tag"><i class="fas fa-user-secret"></i> Anonymous</span>
                @else
                    <span class="text-xs text-gray-500">{{ $report->victim_name }}</span>
                @endif
                <span class="text-xs text-gray-400"><i class="far fa-clock"></i> {{ $report->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <div style="flex-shrink:0;">
            <i class="fas fa-chevron-right text-gray-300"></i>
        </div>
    </a>
    @endforeach

    <div class="mt-4">{{ $reports->links() }}</div>
    @endif

</div>
@endsection
