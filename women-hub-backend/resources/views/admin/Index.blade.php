@extends('layouts.admin')

@section('title', 'Mentors')
@section('page-title', 'Mentors')

@section('content')
<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h2>Mentors</h2>
        <p>Manage all registered mentors on the platform.</p>
    </div>
    <a href="{{ route('admin.mentors.create') }}" class="btn btn-primary-hub">
        <i class="bi bi-plus-lg me-1"></i>Add Mentor
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="mb-3">
    <div class="row g-2">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email…"
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="area" class="form-select">
                <option value="">All Areas</option>
                <option value="menstrual_hygiene" {{ request('area')=='menstrual_hygiene'?'selected':'' }}>Menstrual Hygiene</option>
                <option value="general_issues"    {{ request('area')=='general_issues'?'selected':'' }}>General Issues</option>
                <option value="both"              {{ request('area')=='both'?'selected':'' }}>Both</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="active"   {{ request('status')=='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-hub w-100">Filter</button>
        </div>
    </div>
</form>

<div class="data-table">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Mentor</th>
                <th>Area of Support</th>
                <th>Availability</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mentors as $mentor)
            <tr>
                <td style="color:#9A7A8E;font-size:.8rem;">{{ $mentors->firstItem() + $loop->index }}</td>
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--primary-lt),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0;">
                            {{ strtoupper(substr($mentor->name,0,1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;">{{ $mentor->name }}</div>
                            <div style="font-size:.75rem;color:#9A7A8E;">{{ $mentor->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge" style="background:#F3E8F0;color:var(--primary);">{{ $mentor->area_label }}</span>
                </td>
                <td style="font-size:.8rem;color:#6B3D57;">{{ $mentor->availability_string }}</td>
                <td>
                    <span class="badge {{ $mentor->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($mentor->status) }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.mentors.show', $mentor) }}" class="btn btn-sm" style="background:#F3E8F0;color:var(--primary);border-radius:8px;" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.mentors.edit', $mentor) }}" class="btn btn-sm" style="background:#FDF0E6;color:var(--accent);border-radius:8px;" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="{{ route('admin.mentors.toggle', $mentor) }}" class="btn btn-sm" style="background:#E8F5EB;color:#2E8B3C;border-radius:8px;" title="Toggle Status"><i class="bi bi-arrow-repeat"></i></a>
                        <form method="POST" action="{{ route('admin.mentors.destroy', $mentor) }}" onsubmit="return confirm('Remove this mentor?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background:#FDECEC;color:#D94040;border-radius:8px;" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-5" style="color:#B8A0B0;">
                    <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                    No mentors found. <a href="{{ route('admin.mentors.create') }}" style="color:var(--primary);">Add the first one →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $mentors->links() }}
</div>
@endsection