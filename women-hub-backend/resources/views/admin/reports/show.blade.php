@extends('admin.layouts.admin')

@section('title', 'Report Details')
@section('page-title', 'Harassment Report Details')
@section('page-subtitle', 'Review and assign a mentor to handle this report')

@push('styles')
<style>
    /* ── Hero ── */
    .hero-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 16px;
    }
    .hero-ref {
        font-size: 11px;
        font-family: monospace;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 4px;
    }
    .hero-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .hero-sub {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
    }

    /* ── Pills / Badges ── */
    .pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .pill .dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .pill-pending   { background: var(--light-orange); color: var(--orange); }
    .pill-reviewing { background: var(--light-blue);   color: var(--blue); }
    .pill-assigned  { background: var(--light-purple); color: var(--purple); }
    .pill-resolved  { background: var(--light-teal);   color: var(--teal-green); }
    .pill-dismissed { background: var(--light-red);    color: var(--red); }
    .pill-physical  { background: var(--light-purple); color: var(--purple); }
    .pill-verbal    { background: var(--light-red);    color: var(--red); }
    .pill-sexual    { background: var(--light-orange); color: var(--orange); }
    .pill-cyber     { background: var(--light-blue);   color: var(--blue); }
    .pill-other     { background: var(--light-gray);   color: var(--text-secondary); }
    .pill-sev-high  { background: var(--light-red);    color: var(--red); }
    .pill-sev-medium{ background: var(--light-orange); color: var(--orange); }
    .pill-sev-low   { background: var(--light-teal);   color: var(--teal-green); }

    /* ── Cards ── */
    .detail-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 16px;
    }
    .card-title {
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    /* ── Info Grid ── */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
    }
    .info-cell {
        padding: 10px 14px;
        border-bottom: 1px solid var(--border-color);
        border-right: 1px solid var(--border-color);
    }
    .info-cell:nth-child(2n) { border-right: none; }
    .info-cell:nth-last-child(-n+2) { border-bottom: none; }
    .info-lbl { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
    .info-val { font-size: 13px; font-weight: 600; color: var(--text-primary); }

    /* ── Description / Text blocks ── */
    .desc-block { margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border-color); }
    .desc-lbl   { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px; }
    .desc-text  { font-size: 13px; line-height: 1.75; color: var(--text-primary); white-space: pre-wrap; }

    /* ── Special blocks ── */
    .perp-box {
        background: var(--light-red);
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 13px;
        color: var(--red);
        line-height: 1.6;
        border-left: 3px solid var(--red);
        border-radius: 0 8px 8px 0;
    }
    .anon-box {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--light-gray);
        border-radius: 10px;
        padding: 14px;
    }
    .anon-icon {
        width: 38px; height: 38px;
        border-radius: 8px;
        background: var(--bg-secondary);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        color: var(--text-secondary);
        flex-shrink: 0;
    }
    .response-box {
        background: var(--light-blue);
        border-left: 3px solid var(--blue);
        border-radius: 0 8px 8px 0;
        padding: 12px 14px;
    }
    .response-text { font-size: 13px; line-height: 1.7; color: var(--text-primary); }
    .response-meta { font-size: 11px; color: var(--text-secondary); margin-top: 8px; }

    /* ── Sidebar: Assign Mentor ── */
    .assign-card {
        background: var(--card-bg);
        border: 2px solid var(--purple);
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 16px;
    }
    .assign-header { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .assign-icon {
        width: 38px; height: 38px;
        border-radius: 8px;
        background: var(--light-purple);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: var(--purple); flex-shrink: 0;
    }
    .assign-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
    .assign-sub   { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }
    .field-lbl    { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: var(--text-secondary); margin-bottom: 5px; }

    /* ── Form inputs ── */
    .form-select-custom, .form-textarea-custom {
        width: 100%;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--bg-secondary);
        color: var(--text-primary);
        font-size: 13px;
        margin-bottom: 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-select-custom:focus, .form-textarea-custom:focus {
        outline: none;
        border-color: var(--purple);
        box-shadow: 0 0 0 2px rgba(139,92,246,0.12);
    }

    /* ── Buttons ── */
    .btn-assign {
        width: 100%;
        padding: 9px;
        border-radius: 8px;
        background: var(--purple);
        color: white;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background 0.2s, transform 0.1s;
    }
    .btn-assign:hover { background: #6d28d9; transform: translateY(-1px); }
    .btn-assign:disabled { opacity: .7; cursor: not-allowed; }

    .btn-act {
        width: 100%;
        padding: 9px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        transition: filter 0.15s, transform 0.1s;
    }
    .btn-act:last-child { margin-bottom: 0; }
    .btn-act:hover { filter: brightness(.95); transform: translateY(-1px); }
    .btn-reviewing { background: var(--light-blue);   color: var(--blue);       border-color: var(--blue); }
    .btn-resolved  { background: var(--light-teal);   color: var(--teal-green); border-color: var(--teal-green); }
    .btn-dismissed { background: var(--light-red);    color: var(--red);        border-color: var(--red); }

    /* ── Mentor card ── */
    .mentor-row { display: flex; align-items: center; gap: 12px; }
    .mentor-avatar {
        width: 42px; height: 42px;
        border-radius: 10px;
        background: var(--light-purple);
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 700;
        color: var(--purple); flex-shrink: 0;
    }
    .mentor-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .mentor-email { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
    .mentor-spec  {
        display: inline-block; margin-top: 5px;
        font-size: 11px; padding: 2px 8px;
        border-radius: 20px;
        background: var(--light-purple); color: var(--purple);
    }

    /* ── Meta table ── */
    .meta-table { width: 100%; border-collapse: collapse; }
    .meta-table td { padding: 8px 0; font-size: 12px; border-bottom: 1px solid var(--border-color); }
    .meta-table tr:last-child td { border-bottom: none; }
    .meta-table .mlbl { color: var(--text-secondary); }
    .meta-table .mval { text-align: right; font-weight: 600; font-family: monospace; font-size: 12px; color: var(--text-primary); }
    .meta-table .mval-normal { text-align: right; font-size: 12px; font-weight: 600; color: var(--text-primary); }

    /* ── Victim grid ── */
    .victim-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px; }
    .vlbl { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 3px; }
    .vval { font-size: 13px; font-weight: 600; color: var(--text-primary); }

    /* ── Toast notification ── */
    .toast {
        position: absolute;
        top: 1rem; right: 1rem;
        min-width: 280px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: white;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        transition: opacity 0.3s ease;
    }
    .toast-success { background: var(--teal-green); }
    .toast-error   { background: var(--red); }

    @media (max-width: 768px) {
        .info-grid { grid-template-columns: 1fr; }
        .info-cell { border-right: none; }
        .info-cell:nth-last-child(-n+2) { border-bottom: 1px solid var(--border-color); }
        .info-cell:last-child { border-bottom: none; }
        .victim-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Back link --}}
    <div class="mb-4">
        <a href="{{ route('admin.reports.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold transition hover:gap-3"
           style="color: var(--purple);">
            <i class="fas fa-arrow-left"></i> Back to reports
        </a>
    </div>

    {{-- ── Hero Header ── --}}
    <div class="hero-card">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <div class="hero-ref">{{ $report->reference_number }}</div>
                <div class="hero-title">{{ $report->incident_title }}</div>
                <div class="hero-sub">
                    <i class="far fa-clock"></i>
                    Submitted {{ $report->created_at->diffForHumans() }}
                    &nbsp;·&nbsp; Last updated {{ $report->updated_at->diffForHumans() }}
                </div>
            </div>
            <span class="pill pill-{{ $report->status }}">
                <span class="dot"></span>
                {{ ucfirst($report->status) }}
            </span>
        </div>
        <div class="hero-meta">
            <span class="pill pill-{{ $report->incident_type }}">{{ ucfirst($report->incident_type) }}</span>
            @php $sev = $report->severity ?? 'medium'; @endphp
            <span class="pill pill-sev-{{ $sev }}">
                <i class="fas fa-exclamation-triangle" style="font-size:10px;"></i>
                {{ ucfirst($sev) }} severity
            </span>
            @if($report->is_anonymous)
            <span class="pill" style="background: var(--light-gray); color: var(--text-secondary);">
                <i class="fas fa-user-secret" style="font-size:10px;"></i> Anonymous
            </span>
            @endif
        </div>
    </div>

    {{-- ── Two-column layout ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ── Left: Main content ── --}}
        <div class="lg:col-span-2">

            {{-- Incident Details --}}
            <div class="detail-card">
                <div class="card-title">
                    <i class="fas fa-file-alt" style="color: var(--purple);"></i>
                    Incident details
                </div>
                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-lbl">Incident date</div>
                        <div class="info-val">{{ $report->incident_date->format('F d, Y') }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-lbl">Location</div>
                        <div class="info-val">{{ $report->incident_location }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-lbl">Type</div>
                        <div class="info-val">
                            <span class="pill pill-{{ $report->incident_type }}">{{ ucfirst($report->incident_type) }}</span>
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="info-lbl">Severity</div>
                        <div class="info-val">
                            <span class="pill pill-sev-{{ $sev }}">{{ ucfirst($sev) }}</span>
                        </div>
                    </div>
                </div>
                <div class="desc-block">
                    <div class="desc-lbl">Description</div>
                    <div class="desc-text">{{ $report->incident_description }}</div>
                </div>
            </div>

            {{-- Perpetrator --}}
            @if($report->perpetrator_info)
            <div class="detail-card">
                <div class="card-title">
                    <i class="fas fa-user-times" style="color: var(--red);"></i>
                    Perpetrator information
                </div>
                <div class="perp-box">{{ $report->perpetrator_info }}</div>
            </div>
            @endif

            {{-- Reporter / Victim --}}
            <div class="detail-card">
                <div class="card-title">
                    <i class="fas fa-user-shield" style="color: var(--purple);"></i>
                    Reporter information
                </div>
                @if($report->is_anonymous)
                <div class="anon-box">
                    <div class="anon-icon"><i class="fas fa-user-secret"></i></div>
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--text-primary);">Anonymous submission</p>
                        <p class="text-xs mt-1" style="color: var(--text-secondary);">
                            The reporter chose to remain anonymous. Their identity is protected and cannot be disclosed.
                        </p>
                    </div>
                </div>
                @else
                <div class="victim-grid">
                    <div>
                        <div class="vlbl">Full name</div>
                        <div class="vval">{{ $report->victim_name }}</div>
                    </div>
                    <div>
                        <div class="vlbl">Email address</div>
                        <div class="vval">{{ $report->victim_email }}</div>
                    </div>
                    @if($report->victim_phone)
                    <div>
                        <div class="vlbl">Phone number</div>
                        <div class="vval">{{ $report->victim_phone }}</div>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Admin Response --}}
            @if($report->admin_response)
            <div class="detail-card">
                <div class="card-title">
                    <i class="fas fa-reply-all" style="color: var(--blue);"></i>
                    Admin response
                </div>
                <div class="response-box">
                    <div class="response-text">{{ $report->admin_response }}</div>
                    <div class="response-meta">
                        <i class="far fa-clock"></i>
                        Responded {{ $report->responded_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ── Right: Sidebar ── --}}
        <div>

            {{-- Assign Mentor --}}
            @if(!in_array($report->status, ['resolved', 'dismissed']))
            <div class="assign-card">
                <div class="assign-header">
                    <div class="assign-icon"><i class="fas fa-user-plus"></i></div>
                    <div>
                        <div class="assign-title">Assign mentor</div>
                        <div class="assign-sub">
                            @if($report->assignedMentor) Reassign or update this case
                            @else Assign a mentor to this case @endif
                        </div>
                    </div>
                </div>
                <form id="assignMentorForm" method="POST" action="{{ route('admin.reports.assign', $report->id) }}">
                    @csrf
                    <div class="field-lbl">Select mentor</div>
                    <select name="mentor_id" required class="form-select-custom">
                        <option value="">Choose a mentor...</option>
                        @foreach($mentors as $mentor)
                        <option value="{{ $mentor->id }}"
                            {{ $report->assigned_mentor_id == $mentor->id ? 'selected' : '' }}>
                            {{ $mentor->name }}
                            ({{ $mentor->assignedReports->whereIn('status', ['assigned','reviewing'])->count() }} active)
                        </option>
                        @endforeach
                    </select>
                    <div class="field-lbl">Notes for mentor</div>
                    <textarea name="notes" rows="3"
                              placeholder="Add instructions or context for the mentor..."
                              class="form-textarea-custom"></textarea>
                    <button type="submit" class="btn-assign" id="assignBtn">
                        <i class="fas fa-check-circle"></i> Assign mentor
                    </button>
                </form>
            </div>
            @endif

            {{-- Current Assignment --}}
            @if($report->assignedMentor)
            <div class="detail-card">
                <div class="card-title">
                    <i class="fas fa-chalkboard-teacher" style="color: var(--purple);"></i>
                    Assigned mentor
                </div>
                <div class="mentor-row">
                    <div class="mentor-avatar">
                        {{ strtoupper(substr($report->assignedMentor->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $report->assignedMentor->name)[1] ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <div class="mentor-name">{{ $report->assignedMentor->name }}</div>
                        <div class="mentor-email">{{ $report->assignedMentor->email }}</div>
                        @if($report->assignedMentor->specialization)
                        <span class="mentor-spec">{{ $report->assignedMentor->specialization }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endif

           

            {{-- Report Info --}}
            <div class="detail-card">
                <div class="card-title">
                    <i class="fas fa-info-circle" style="color: var(--blue);"></i>
                    Report info
                </div>
                <table class="meta-table">
                    <tr>
                        <td class="mlbl">Report ID</td>
                        <td class="mval">#{{ $report->id }}</td>
                    </tr>
                    <tr>
                        <td class="mlbl">Reference</td>
                        <td class="mval">{{ $report->reference_number }}</td>
                    </tr>
                    <tr>
                        <td class="mlbl">Created</td>
                        <td class="mval-normal">{{ $report->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="mlbl">Last updated</td>
                        <td class="mval-normal">{{ $report->updated_at->diffForHumans() }}</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ── Assign Mentor ── */
document.getElementById('assignMentorForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('assignBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
    btn.disabled = true;

    try {
        const res  = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: new FormData(this)
        });
        const data = await res.json();
        if (data.success) {
            showToast('Mentor assigned successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    } catch {
        showToast('Failed to assign mentor. Please try again.', 'error');
    } finally {
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Assign mentor';
        btn.disabled = false;
    }
});

/* ── Quick Actions ── */
function markInProgress() {
    if (confirm('Mark this report as in progress?')) updateStatus('reviewing', 'Report marked in progress by admin.');
}
function markResolved() {
    const note = prompt('Please provide a resolution note:');
    if (note?.trim()) updateStatus('resolved', note);
    else if (note !== null) alert('A resolution note is required.');
}
function markDismissed() {
    const reason = prompt('Please provide a reason for dismissal:');
    if (reason?.trim()) updateStatus('dismissed', reason);
    else if (reason !== null) alert('A dismissal reason is required.');
}

async function updateStatus(status, responseText) {
    try {
        const res  = await fetch('{{ route("admin.reports.respond", $report->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status, response: responseText })
        });
        const data = await res.json();
        if (data.success) {
            showToast('Status updated successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    } catch {
        showToast('Failed to update status. Please try again.', 'error');
    }
}

/* ── Toast ── */
function showToast(message, type = 'success') {
    const t = document.createElement('div');
    t.className = 'toast toast-' + type;
    t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                   <span>${message}</span>
                   <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;color:white;cursor:pointer;font-size:16px;">&times;</button>`;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
}
</script>
@endpush