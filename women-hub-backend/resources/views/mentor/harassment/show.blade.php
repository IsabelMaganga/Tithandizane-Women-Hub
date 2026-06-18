@extends('mentor.layouts.dashboard')
@section('title') Report — {{ $report->reference_number }} @endsection

@push('styles')
<style>
    .card {
        background: white; border: 1px solid #e5e7eb;
        border-radius: 14px; padding: 20px 24px; margin-bottom: 16px;
    }
    .card-title {
        font-size: 13px; font-weight: 700; color: #374151;
        display: flex; align-items: center; gap: 8px;
        padding-bottom: 12px; margin-bottom: 14px;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 0;
        border: 1px solid #f3f4f6; border-radius: 10px; overflow: hidden;
    }
    .info-cell { padding: 10px 14px; border-bottom: 1px solid #f3f4f6; border-right: 1px solid #f3f4f6; }
    .info-cell:nth-child(2n) { border-right: none; }
    .info-cell:nth-last-child(-n+2) { border-bottom: none; }
    .info-lbl { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 3px; }
    .info-val { font-size: 13px; font-weight: 600; color: #111827; }

    .status-chip { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; }
    .status-assigned  { background:#ede9fe; color:#7c3aed; }
    .status-reviewing { background:#dbeafe; color:#2563eb; }
    .status-resolved  { background:#d1fae5; color:#059669; }
    .status-dismissed { background:#f3f4f6; color:#6b7280; }
    .status-pending   { background:#fef3c7; color:#d97706; }

    .type-chip { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; text-transform: capitalize; }
    .type-physical { background:#ede9fe; color:#7c3aed; }
    .type-verbal   { background:#fef3c7; color:#d97706; }
    .type-sexual   { background:#fce7f3; color:#db2777; }
    .type-cyber    { background:#dbeafe; color:#2563eb; }
    .type-other    { background:#f3f4f6; color:#6b7280; }

    .desc-block  { margin-top: 14px; padding-top: 14px; border-top: 1px solid #f3f4f6; }
    .desc-lbl    { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing:.04em; margin-bottom: 6px; }
    .desc-text   { font-size: 13px; line-height: 1.8; color: #111827; white-space: pre-wrap; }

    .anon-box {
        display: flex; align-items: center; gap: 12px;
        background: #f9fafb; border-radius: 10px; padding: 14px;
        border: 1px solid #f3f4f6;
    }
    .anon-icon {
        width: 38px; height: 38px; border-radius: 8px;
        background: #f3f4f6; display: flex; align-items: center;
        justify-content: center; font-size: 18px; color: #6b7280; flex-shrink: 0;
    }

    .existing-response {
        background: #f0fdf4; border-left: 3px solid #10b981;
        border-radius: 0 10px 10px 0; padding: 14px 16px;
    }
    .existing-response .resp-text { font-size: 13px; line-height: 1.8; color: #111827; white-space: pre-wrap; }
    .existing-response .resp-meta { font-size: 11px; color: #6b7280; margin-top: 8px; }

    .perp-box {
        background: #fff7f7; border-left: 3px solid #f87171;
        border-radius: 0 10px 10px 0; padding: 12px 14px;
        font-size: 13px; color: #b91c1c; line-height: 1.6;
    }

    .response-form textarea {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: 12px 14px; font-size: 13px; line-height: 1.7;
        resize: vertical; min-height: 140px; font-family: inherit;
        color: #111827; transition: border-color .2s, box-shadow .2s;
    }
    .response-form textarea:focus {
        outline: none; border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124,58,237,.1);
    }
    .response-form select {
        border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: 9px 12px; font-size: 13px; color: #111827;
        background: white; transition: border-color .2s;
    }
    .response-form select:focus { outline: none; border-color: #7c3aed; }

    .btn-submit {
        background: #7c3aed; color: white; border: none;
        padding: 11px 28px; border-radius: 10px; font-size: 14px;
        font-weight: 700; cursor: pointer; display: inline-flex;
        align-items: center; gap: 8px; transition: background .2s, transform .1s;
    }
    .btn-submit:hover { background: #6d28d9; transform: translateY(-1px); }
    .btn-submit:disabled { opacity:.7; cursor:not-allowed; }

    .alert-success {
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
        border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500;
    }

    @media (max-width: 640px) {
        .info-grid { grid-template-columns: 1fr; }
        .info-cell { border-right: none; }
        .info-cell:nth-last-child(-n+2) { border-bottom: 1px solid #f3f4f6; }
        .info-cell:last-child { border-bottom: none; }
    }
</style>
@endpush

@section('content')
<div class="p-6 max-w-3xl mx-auto">

    {{-- Back --}}
    <div class="mb-5">
        <a href="{{ route('mentor.harassment.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-purple-700 hover:text-purple-900">
            <i class="fas fa-arrow-left"></i> Back to assigned reports
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="card" style="border-color:#c4b5fd;">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-xs text-gray-400 font-mono tracking-widest mb-1">{{ $report->reference_number }}</p>
                <h1 class="text-xl font-bold text-gray-900">{{ $report->incident_title }}</h1>
                <p class="text-xs text-gray-400 mt-1">
                    <i class="far fa-clock"></i> Submitted {{ $report->created_at->diffForHumans() }}
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="type-chip type-{{ $report->incident_type }}">{{ ucfirst(str_replace('_',' ',$report->incident_type)) }}</span>
                <span class="status-chip status-{{ $report->status }}">
                    <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
                    {{ ucfirst($report->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Incident Details --}}
    <div class="card">
        <div class="card-title">
            <i class="fas fa-file-alt" style="color:#7c3aed;"></i>
            Incident details
        </div>
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-lbl">Date of incident</div>
                <div class="info-val">{{ \Carbon\Carbon::parse($report->incident_date)->format('F d, Y') }}</div>
            </div>
            <div class="info-cell">
                <div class="info-lbl">Location</div>
                <div class="info-val">{{ $report->incident_location ?: '—' }}</div>
            </div>
        </div>
        <div class="desc-block">
            <div class="desc-lbl">Description</div>
            <div class="desc-text">{{ $report->incident_description }}</div>
        </div>
    </div>

    {{-- Perpetrator info --}}
    @if($report->perpetrator_info)
    <div class="card">
        <div class="card-title">
            <i class="fas fa-user-times" style="color:#ef4444;"></i>
            Perpetrator information
        </div>
        <div class="perp-box">{{ $report->perpetrator_info }}</div>
    </div>
    @endif

    {{-- Reporter --}}
    <div class="card">
        <div class="card-title">
            <i class="fas fa-user-shield" style="color:#7c3aed;"></i>
            Reporter
        </div>
        @if($report->is_anonymous)
        <div class="anon-box">
            <div class="anon-icon"><i class="fas fa-user-secret"></i></div>
            <div>
                <p class="font-semibold text-sm text-gray-800">Anonymous submission</p>
                <p class="text-xs text-gray-500 mt-1">
                    The reporter chose to remain anonymous. Write your response below — they will read it by entering their reference code in the app.
                </p>
            </div>
        </div>
        @else
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-lbl">Name</div>
                <div class="info-val">{{ $report->victim_name }}</div>
            </div>
            <div class="info-cell">
                <div class="info-lbl">Email</div>
                <div class="info-val">{{ $report->victim_email }}</div>
            </div>
            @if($report->victim_phone)
            <div class="info-cell" style="grid-column:span 2;">
                <div class="info-lbl">Phone</div>
                <div class="info-val">{{ $report->victim_phone }}</div>
            </div>
            @endif
        </div>
        @if(!$report->is_anonymous)
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-lbl">Name</div>
                <div class="info-val">{{ $report->victim_name }}</div>
            </div>
            <div class="info-cell">
                <div class="info-lbl">Email</div>
                <div class="info-val">{{ $report->victim_email }}</div>
            </div>
            @if($report->victim_phone)
            <div class="info-cell" style="grid-column:span 2;">
                <div class="info-lbl">Phone</div>
                <div class="info-val">{{ $report->victim_phone }}</div>
            </div>
            @endif
        </div>
        <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
            <p class="text-xs text-gray-400">
                <i class="fas fa-info-circle"></i>
                This reporter shared their identity. You can also reach them through the private in-app chat.
            </p>
            <a href="{{ route('mentor.harassment.chat', $report->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">
                <i class="fas fa-comment"></i> Open chat
            </a>
        </div>
        @endif
        @endif
    </div>

    {{-- Existing response (if already responded) --}}
    @if($report->admin_response)
    <div class="card">
        <div class="card-title">
            <i class="fas fa-reply" style="color:#059669;"></i>
            Your previous response
        </div>
        <div class="existing-response">
            <div class="resp-text">{{ $report->admin_response }}</div>
            <div class="resp-meta">
                <i class="far fa-clock"></i>
                Sent {{ \Carbon\Carbon::parse($report->responded_at)->diffForHumans() }}
            </div>
        </div>
    </div>
    @endif

    {{-- Response form --}}
    @if(!in_array($report->status, ['dismissed']))
    <div class="card response-form" style="border-color:#c4b5fd;">
        <div class="card-title">
            <i class="fas fa-pen" style="color:#7c3aed;"></i>
            {{ $report->admin_response ? 'Update your response' : 'Write your response' }}
        </div>

        <form method="POST" action="{{ route('mentor.harassment.respond', $report->id) }}" id="respondForm">
            @csrf

            @error('response')
            <p class="text-red-500 text-xs mb-3"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
            @enderror

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    Your response / advice
                </label>
                <textarea name="response" placeholder="Write your guidance or advice for this report...">{{ old('response', $report->admin_response) }}</textarea>
            </div>

            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Mark report as
                    </label>
                    <select name="status">
                        <option value="reviewing" {{ $report->status === 'reviewing' ? 'selected' : '' }}>Still Reviewing</option>
                        <option value="resolved"  {{ $report->status === 'resolved'  ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    {{ $report->admin_response ? 'Update Response' : 'Send Response' }}
                </button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.getElementById('respondForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
});
</script>
@endpush
