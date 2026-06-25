@extends('mentor.layouts.dashboard')

@section('title', 'Dashboard')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white">

    <div class="px-6 py-8 max-w-7xl mx-auto">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Dashboard
                </h1>
                <p class="text-slate-400">
                    Welcome back, {{ $mentorName ?? 'Mentor' }}
                </p>
            </div>

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center font-bold">
                    {{ strtoupper(substr($mentorName ?? 'M', 0, 2)) }}
                </div>

                <div class="text-sm">
                    <p class="font-semibold">{{ $mentorName ?? 'Mentor' }}</p>
                    <p class="text-slate-400 text-xs">{{ $mentorEmail ?? '' }}</p>
                </div>

            </div>

        </div>

        {{-- TOP STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <p class="text-slate-400 text-sm">Incoming Sessions</p>
                <h2 class="text-3xl font-bold text-purple-400 mt-2">
                    {{ $incomingCount ?? 0 }}
                </h2>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <p class="text-slate-400 text-sm">Missed Sessions</p>
                <h2 class="text-3xl font-bold text-red-400 mt-2">
                    {{ $missedCount ?? 0 }}
                </h2>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                <p class="text-slate-400 text-sm">Unattended</p>
                <h2 class="text-3xl font-bold text-yellow-400 mt-2">
                    {{ $unattendedCount ?? 0 }}
                </h2>
            </div>

        </div>

        {{-- MAIN GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: QUICK ACTIONS --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- PROFILE CARD --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex justify-between items-center">

                    <div>
                        <h2 class="text-xl font-semibold">Good afternoon 👋</h2>
                        <p class="text-slate-400 text-sm mt-1">
                            Manage your mentorship activities
                        </p>
                    </div>

                    <a href="{{ route('mentor.showProfile') }}"
                       class="bg-purple-600 hover:bg-purple-700 px-5 py-2 rounded-xl text-white font-medium">
                        View Profile
                    </a>

                </div>

                {{-- QUICK CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <a href="{{ route('mentor.appointment') }}"
                       class="bg-slate-900 border border-slate-800 rounded-2xl p-6 hover:border-purple-500 transition">

                        <h3 class="font-semibold text-lg">Appointments</h3>
                        <p class="text-slate-400 text-sm mt-2">
                            Manage mentorship sessions
                        </p>

                    </a>

                    <a href="{{ route('mentor.chat.index') }}"
                       class="bg-slate-900 border border-slate-800 rounded-2xl p-6 hover:border-purple-500 transition">

                        <h3 class="font-semibold text-lg">Chat</h3>
                        <p class="text-slate-400 text-sm mt-2">
                            Talk with mentees
                        </p>

                    </a>

                    <a href="{{ route('mentor.guidance.index') }}"
                       class="bg-slate-900 border border-slate-800 rounded-2xl p-6 hover:border-purple-500 transition">

                        <h3 class="font-semibold text-lg">Guidance</h3>
                        <p class="text-slate-400 text-sm mt-2">
                            Articles & help resources
                        </p>

                    </a>

                    <a href="{{ route('mentor.settings.index') }}"
                       class="bg-slate-900 border border-slate-800 rounded-2xl p-6 hover:border-purple-500 transition">

                        <h3 class="font-semibold text-lg">Settings</h3>
                        <p class="text-slate-400 text-sm mt-2">
                            Profile & security
                        </p>

                    </a>

                </div>

            </div>

            {{-- RIGHT SIDEBAR --}}
            <div class="space-y-6">

                {{-- TIMELINE CARD --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">

                    <h3 class="font-semibold mb-4">Important Dates</h3>

                    <div class="space-y-4 text-sm">

                        <div class="flex gap-3">
                            <div class="w-2 h-2 mt-2 rounded-full bg-purple-500"></div>
                            <div>
                                <p>Semester Start</p>
                                <p class="text-slate-400">April 7, 2026</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-2 h-2 mt-2 rounded-full bg-green-500"></div>
                            <div>
                                <p>Registration Open</p>
                                <p class="text-slate-400">April 7, 2026</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-2 h-2 mt-2 rounded-full bg-red-500"></div>
                            <div>
                                <p>Late Registration</p>
                                <p class="text-slate-400">June 6, 2026</p>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- NOTIFICATIONS --}}
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">

                    <h3 class="font-semibold mb-4">
                        Notifications ({{ $unreadCount ?? 0 }})
                    </h3>

                    <div class="space-y-3 max-h-64 overflow-y-auto">

                        @forelse($notifications ?? [] as $note)
                            <div class="p-3 bg-slate-800 rounded-xl text-sm">
                                {{ $note->data['message'] ?? 'New update' }}
                            </div>
                        @empty
                            <p class="text-slate-400 text-sm">No notifications</p>
                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection