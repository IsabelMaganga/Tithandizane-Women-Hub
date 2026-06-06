@extends('admin.layouts.admin')
@section('title', 'Report Details')
@section('page-title', 'Harassment Report Details')
@section('page-subtitle', 'Review and assign mentor to handle this report')

@push('styles')
<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .detail-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #6B7280;
        margin-bottom: 8px;
    }
    .detail-value {
        font-size: 14px;
        color: #1F2937;
        margin-bottom: 16px;
    }
    .status-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }
    .assign-form {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-4">
        <a href="{{ route('admin.reports.index') }}" class="text-purple-600 hover:text-purple-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Reports
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Report Details -->
            <div class="detail-card">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold">Report #{{ $report->reference_number }}</h3>
                        <p class="text-sm text-gray-500">Submitted {{ $report->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="status-badge status-{{ $report->status }}">
                        {{ ucfirst($report->status) }}
                    </span>
                </div>

                <div class="border-t pt-4">
                    <div class="detail-label">Incident Type</div>
                    <div class="detail-value capitalize">{{ $report->incident_type }}</div>

                    <div class="detail-label">Title</div>
                    <div class="detail-value">{{ $report->incident_title }}</div>

                    <div class="detail-label">Description</div>
                    <div class="detail-value whitespace-pre-wrap">{{ $report->incident_description }}</div>

                    <div class="detail-label">Incident Date & Location</div>
                    <div class="detail-value">
                        {{ $report->incident_date->format('F d, Y') }} at {{ $report->incident_location }}
                    </div>

                    @if($report->perpetrator_info)
                    <div class="detail-label">Perpetrator Info</div>
                    <div class="detail-value">{{ $report->perpetrator_info }}</div>
                    @endif

                    @if(!$report->is_anonymous)
                    <div class="detail-label">Victim Information</div>
                    <div class="detail-value">
                        <strong>Name:</strong> {{ $report->victim_name }}<br>
                        <strong>Email:</strong> {{ $report->victim_email }}<br>
                        @if($report->victim_phone)
                        <strong>Phone:</strong> {{ $report->victim_phone }}
                        @endif
                    </div>
                    @else
                    <div class="bg-gray-50 rounded-lg p-4 mt-2">
                        <i class="fas fa-user-secret text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-600">This report was submitted anonymously</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Admin Response -->
            @if($report->admin_response)
            <div class="detail-card">
                <h4 class="font-bold mb-3">Admin Response</h4>
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-gray-700">{{ $report->admin_response }}</p>
                    <p class="text-xs text-gray-500 mt-2">Responded {{ $report->responded_at->diffForHumans() }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Assign Mentor Form -->
            @if($report->status !== 'resolved' && $report->status !== 'dismissed')
            <div class="assign-form mb-6">
                <h4 class="font-bold text-lg mb-3">
                    <i class="fas fa-user-plus mr-2"></i> Assign Mentor
                </h4>
                <p class="text-sm opacity-90 mb-4">Select a mentor to handle this harassment report</p>
                
                <form id="assignMentorForm" method="POST" action="{{ route('admin.reports.assign', $report->id) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="mentor_id" id="mentor_id" class="w-full rounded-lg border-0 px-3 py-2 text-gray-700" required>
                            <option value="">Select a mentor...</option>
                            @foreach($mentors as $mentor)
                            <option value="{{ $mentor->id }}" {{ $report->assigned_mentor_id == $mentor->id ? 'selected' : '' }}>
                                {{ $mentor->name }} ({{ $mentor->assignedReports->whereIn('status', ['assigned', 'reviewing'])->count() }} active cases)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="notes" rows="2" placeholder="Add notes for the mentor (optional)" 
                                  class="w-full rounded-lg border-0 px-3 py-2 text-gray-700 text-sm"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-white text-purple-600 font-semibold py-2 rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-check-circle mr-2"></i> Assign Mentor
                    </button>
                </form>
            </div>
            @endif

            <!-- Current Assignment -->
            @if($report->assignedMentor)
            <div class="detail-card">
                <h4 class="font-bold mb-3">
                    <i class="fas fa-chalkboard-teacher mr-2 text-purple-600"></i> Assigned Mentor
                </h4>
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold">{{ $report->assignedMentor->name }}</p>
                        <p class="text-sm text-gray-500">{{ $report->assignedMentor->email }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="detail-card">
                <h4 class="font-bold mb-3">Quick Actions</h4>
                <div class="space-y-2">
                    <button onclick="markInProgress()" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
                        <i class="fas fa-play mr-2"></i> Mark In Progress
                    </button>
                    <button onclick="markResolved()" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
                        <i class="fas fa-check mr-2"></i> Mark Resolved
                    </button>
                    <button onclick="markDismissed()" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-times mr-2"></i> Dismiss Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('assignMentorForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✓ Mentor assigned successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Failed to assign mentor. Please try again.');
    }
});

function markInProgress() {
    updateStatus('reviewing', 'Mark report as in progress?');
}

function markResolved() {
    const response = prompt('Please provide a resolution note:');
    if (response) {
        updateStatusWithResponse('resolved', response);
    }
}

function markDismissed() {
    const reason = prompt('Please provide a reason for dismissal:');
    if (reason) {
        updateStatusWithResponse('dismissed', reason);
    }
}

async function updateStatus(status, confirmMessage) {
    if (!confirm(confirmMessage)) return;
    
    try {
        const response = await fetch('{{ route("admin.reports.respond", $report->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: status,
                response: `Report marked as ${status} by admin`
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✓ Status updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Failed to update status. Please try again.');
    }
}

async function updateStatusWithResponse(status, responseText) {
    try {
        const response = await fetch('{{ route("admin.reports.respond", $report->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: status,
                response: responseText
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('✓ Status updated successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Failed to update status. Please try again.');
    }
}
</script>
@endsection