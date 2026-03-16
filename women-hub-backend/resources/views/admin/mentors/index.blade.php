@extends('layouts.admin')

@section('title', 'Mentors')
@section('page-title', 'Mentors')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Mentors</h4>
                <a href="{{ route('admin.mentors.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Mentor
                </a>
            </div>
        </div>
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
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    {{-- Mentors Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if ($mentors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Area of Support</th>
                                        <th>Availability</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mentors as $mentor)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($mentor->photo)
                                                        <img src="{{ asset('storage/' . $mentor->photo) }}" 
                                                             alt="{{ $mentor->name }}" 
                                                             class="rounded-circle me-2" 
                                                             width="40" height="40">
                                                    @else
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                                             style="width: 40px; height: 40px; font-size: 14px;">
                                                            {{ substr($mentor->name, 0, 2) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $mentor->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $mentor->qualification ?? 'No qualification' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $mentor->email }}</td>
                                            <td>{{ $mentor->phone ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $mentor->area_label }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ $mentor->availability_string }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($mentor->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.mentors.show', $mentor) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.mentors.edit', $mentor) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" 
                                                          action="{{ route('admin.mentors.toggleStatus', $mentor) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-{{ $mentor->status == 'active' ? 'danger' : 'success' }}" 
                                                                title="{{ $mentor->status == 'active' ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fas fa-{{ $mentor->status == 'active' ? 'ban' : 'check' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" 
                                                          action="{{ route('admin.mentors.destroy', $mentor) }}" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this mentor?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $mentors->firstItem() }} to {{ $mentors->lastItem() }} 
                                of {{ $mentors->total() }} mentors
                            </div>
                            {{ $mentors->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No mentors found</h5>
                            <p class="text-muted">
                                @if (request()->has('search') || request()->has('area') || request()->has('status'))
                                    Try adjusting your search criteria or 
                                    <a href="{{ route('admin.mentors.index') }}" class="btn btn-sm btn-outline-primary">clear filters</a>
                                @else
                                    Start by adding your first mentor using the button above.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
