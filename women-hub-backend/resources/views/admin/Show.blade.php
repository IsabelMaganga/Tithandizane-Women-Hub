@extends('layouts.admin')

@section('title', $mentor->name)
@section('page-title', 'Mentor Profile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>{{ $mentor->name }}</h2>
        <p style="color:#9A7A8E;margin:4px 0 0;font-size:.875rem;">Mentor Profile</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.mentors.edit', $mentor) }}" class="btn btn-primary-hub">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.mentors.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">← Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="form-card text-center">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary-lt),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:2rem;margin:0 auto 16px;">
                {{ strtoupper(substr($mentor->name,0,1)) }}
            </div>
            <h5 style="font-family:'Playfair Display',serif;color:var(--primary);">{{ $mentor->name }}</h5>
            <p style="font-size:.85rem;color:#9A7A8E;">{{ $mentor->email }}</p>
            @if($mentor->phone)
                <p style="font-size:.85rem;color:#6B3D57;"><i class="bi bi-phone me-1"></i>{{ $mentor->phone }}</p>
            @endif
            <span class="badge {{ $mentor->status === 'active' ? 'badge-active' : 'badge-inactive' }} d-inline-block mb-3">
                {{ ucfirst($mentor->status) }}
            </span>
            <div class="d-flex flex-column gap-2 mt-2">
                <a href="{{ route('admin.mentors.toggle', $mentor) }}" class="btn btn-sm w-100" style="background:#E8F5EB;color:#2E8B3C;border-radius:8px;">
                    <i class="bi bi-arrow-repeat me-1"></i>Toggle Status
                </a>
                <form method="POST" action="{{ route('admin.mentors.destroy', $mentor) }}" onsubmit="return confirm('Delete this mentor?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm w-100" style="background:#FDECEC;color:#D94040;border-radius:8px;">
                        <i class="bi bi-trash me-1"></i>Remove Mentor
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="form-card mb-3">
            <h6 style="font-family:'Playfair Display',serif;color:var(--primary);margin-bottom:12px;">About</h6>
            <p style="font-size:.9rem;line-height:1.7;color:#4A2A3E;">{{ $mentor->bio }}</p>
        </div>

        <div class="form-card">
            <h6 style="font-family:'Playfair Display',serif;color:var(--primary);margin-bottom:16px;">Availability</h6>
            <div class="row g-3">
                <div class="col-sm-6">
                    <div style="font-size:.75rem;color:#9A7A8E;margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em;">Area of Support</div>
                    <span class="badge" style="background:#F3E8F0;color:var(--primary);font-size:.82rem;">{{ $mentor->area_label }}</span>
                </div>
                <div class="col-sm-6">
                    <div style="font-size:.75rem;color:#9A7A8E;margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em;">Time Slot</div>
                    <span style="font-size:.875rem;font-weight:600;color:#4A2A3E;">{{ $mentor->available_time_from }} – {{ $mentor->available_time_to }}</span>
                </div>
                <div class="col-12">
                    <div style="font-size:.75rem;color:#9A7A8E;margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em;">Available Days</div>
                    <div>
                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                            <span class="badge me-1 mb-1" style="
                                background:{{ in_array($day, $mentor->available_days ?? []) ? 'var(--primary)' : '#F0EEF5' }};
                                color:{{ in_array($day, $mentor->available_days ?? []) ? '#fff' : '#9A7A8E' }};
                                font-size:.75rem;padding:6px 10px;">
                                {{ substr($day,0,3) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection