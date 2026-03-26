@extends('mentor.layouts.dashboard')

@section('title') home @endsection

{{-- resources/views/mentor/dashboard.blade.php --}}

{{-- @extends('layouts.app') --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Pending Chat Requests</h2>
    </div>

    <div id="pending-chats-container" class="bg-white rounded-lg shadow p-4">
        @include('mentor.partials.pending-chats', ['pendingChats' => $pendingChats])
    </div>


</div>
@endsection

@push('scripts')
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>




</script>
@endpush
