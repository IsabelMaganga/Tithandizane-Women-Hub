@extends('mentor.layouts.dashboard')

@section('title', 'Appointments')

@section('content')

<div class="max-w-7xl px-4 sm:px-6 py-8 mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Appointments</h1>
        <p class="mt-1 text-slate-500">Manage and track your mentorship sessions.</p>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex flex-wrap gap-3 mb-8">
        <button onclick="showTab('incoming')" id="incomingBtn" class="tab-btn px-5 py-2.5 rounded-xl bg-purple-600 text-white font-medium shadow-sm">Incoming ({{ $incomingSessions->count() }})</button>
        <button onclick="showTab('missed')" id="missedBtn" class="tab-btn px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">Missed ({{ $missedSessions->count() }})</button>
        <button onclick="showTab('unattended')" id="unattendedBtn" class="tab-btn px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">Unattended ({{ $unattendedSessions->count() }})</button>
        <button onclick="showTab('completed')" id="completedBtn" class="tab-btn px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">Completed ({{ $completedSessions->count() }})</button>
    </div>

    {{-- Content Sections --}}
    <div id="incoming" class="tab-content grid gap-4 md:grid-cols-2">
        @forelse($incomingSessions as $session)
            @include('mentor.appointments._card', ['session' => $session, 'color' => 'green'])
        @empty
            <div class="p-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-500 md:col-span-2">No incoming sessions.</div>
        @endforelse
    </div>

    <div id="missed" class="tab-content hidden grid gap-4 md:grid-cols-2">
        @forelse($missedSessions as $session)
            @include('mentor.appointments._card', ['session' => $session, 'color' => 'red'])
        @empty
            <div class="p-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-500 md:col-span-2">No missed sessions.</div>
        @endforelse
    </div>

    <div id="unattended" class="tab-content hidden grid gap-4 md:grid-cols-2">
        @forelse($unattendedSessions as $session)
            @include('mentor.appointments._card', ['session' => $session, 'color' => 'yellow'])
        @empty
            <div class="p-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-500 md:col-span-2">No unattended sessions.</div>
        @endforelse
    </div>

    <div id="completed" class="tab-content hidden grid gap-4 md:grid-cols-2">
        @forelse($completedSessions as $session)
            @include('mentor.appointments._card', ['session' => $session, 'color' => 'blue'])
        @empty
            <div class="p-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-500 md:col-span-2">No completed sessions.</div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTab(tab) {
    // 1. Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // 2. Remove active styling from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-purple-600', 'text-white', 'shadow-sm');
        btn.classList.add('bg-white', 'border-slate-200', 'text-slate-600');
    });

    // 3. Show the target content
    document.getElementById(tab).classList.remove('hidden');

    // 4. Set active styling on the clicked button
    const activeBtn = document.getElementById(tab + 'Btn');
    activeBtn.classList.remove('bg-white', 'border-slate-200', 'text-slate-600');
    activeBtn.classList.add('bg-purple-600', 'text-white', 'shadow-sm');
}
</script>
@endpush