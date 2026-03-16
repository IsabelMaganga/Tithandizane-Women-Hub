@extends('layouts.admin')

@section('title', 'Edit Mentor')
@section('page-title', 'Edit Mentor')

@section('content')
<div class="page-header">
    <h2>Edit Mentor</h2>
    <p>Update the details for <strong>{{ $mentor->name }}</strong>.</p>
</div>

<form method="POST" action="{{ route('admin.mentors.update', $mentor) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.mentors._form')
    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary-hub"><i class="bi bi-check-lg me-1"></i>Update Mentor</button>
        <a href="{{ route('admin.mentors.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;">Cancel</a>
    </div>
</form>
@endsection