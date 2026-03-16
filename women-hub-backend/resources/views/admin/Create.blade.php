<!-- @extends('layouts.admin')

@section('title', 'Add Mentor')
@section('page-title', 'Add Mentor')

@section('content')
<div class="page-header">
    <h2>Add New Mentor</h2>
    <p>Fill in the details below to register a new mentor.</p>
</div>

<form method="POST" action="{{ route('admin.mentors.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.mentors._form')
    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary-hub"><i class="bi bi-check-lg me-1"></i>Save Mentor</button>
        <a href="{{ route('admin.mentors.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">Cancel</a>
    </div>
</form>
@endsection -->