@extends('mentor.layouts.dashboard')

@section('title') Dashboard @endsection

@push('styles')
    <style>
        .emoji-bounce {
            animation: bounce 2.5s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .stat-icon {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@section('content')

    @php
        $reportsArray = $reportCounts->toArray();
        $totalReports = array_sum($reportsArray);
        $categoryCount = count($reportsArray);
        $topCategory = $categoryCount ? array_search(max($reportsArray), $reportsArray) : '—';
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    @endphp

    <div class="min-h-screen px-3 py-8 space-y-6 backdrop-blur-2xl md:px-6">

        {{-- Welcome banner --}}
        <div class="relative overflow-hidden rounded-2xl border border-slate-900/10 bg-gradient-to-br from-[#1e1b4b] via-[#312e81] to-[#5b21b6] p-7 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <span id="emoji" class="text-4xl leading-none emoji-bounce">🙂</span>
                    <div>
                        <p class="text-sm font-medium text-indigo-200">{{ $greeting }}</p>
                        <h2 class="text-2xl font-semibold text-white">{{ $mentorName }}</h2>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-indigo-100">{{ $mentorEmail }}</p>
                    <p class="mt-1 text-xs text-indigo-300">Here's what's happening with your platform today</p>
                </div>
            </div>
        </div>

        {{-- Stat summary --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

            <div class="flex items-center gap-4 p-6 bg-white border shadow-sm rounded-xl border-slate-900/10">
                <div class="text-indigo-600 stat-icon bg-indigo-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m6 10V11M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-slate-800">{{ $totalReports }}</p>
                    <p class="text-sm text-slate-400">Total reports</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-6 bg-white border shadow-sm rounded-xl border-slate-900/10">
                <div class="stat-icon bg-violet-50 text-violet-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-slate-800">{{ $categoryCount }}</p>
                    <p class="text-sm text-slate-400">Report categories</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-6 bg-white border shadow-sm rounded-xl border-slate-900/10">
                <div class="stat-icon bg-amber-50 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-slate-800">{{ $topCategory }}</p>
                    <p class="text-sm text-slate-400">Most reported</p>
                </div>
            </div>

        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

            <div class="p-5 bg-white border shadow-sm rounded-xl border-slate-900/10">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <h3 class="text-base font-semibold text-slate-800">Reports by type</h3>
                        <a href="{{ route('mentor.harassment.analytics') }}" class="text-xs inline-flex items-center text-indigo-600 hover:underline">
                            View details
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                    <span class="text-xs text-slate-400">All time</span>
                </div>
                <a href="{{ route('mentor.harassment.analytics') }}" class="block">
                    <div id="bar"></div>
                </a>
            </div>

            <div class="p-5 bg-white border shadow-sm rounded-xl border-slate-900/10">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold text-slate-800">Monthly chats</h3>
                    <span class="text-xs text-slate-400">Last 9 months</span>
                </div>
                <div id="area"></div>
            </div>

        </div>

        <div class="p-5 bg-white border shadow-sm rounded-xl border-slate-900/10">
            <h3 class="mb-3 text-base font-semibold text-slate-800">Engagement overview</h3>
            <div id="radar"></div>
        </div>

        {{-- Quick links --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-4">

            <a href="{{ route('mentor.settings') }}" class="group flex items-center gap-3 rounded-xl border border-slate-900/10 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="stat-icon bg-[#081e77]/10 text-[#081e77]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                </span>
                <span class="text-sm font-medium text-slate-700">Settings</span>
            </a>

            <a href="{{ route('mentor.profile') }}" class="group flex items-center gap-3 rounded-xl border border-slate-900/10 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="stat-icon bg-[#081e77]/10 text-[#081e77]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <span class="text-sm font-medium text-slate-700">Profile</span>
            </a>

            <a href="{{ route('mentor.Guidance') }}" class="group flex items-center gap-3 rounded-xl border border-slate-900/10 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <span class="stat-icon bg-[#081e77]/10 text-[#081e77]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                </span>
                <span class="text-sm font-medium text-slate-700">Guidance</span>
            </a>

            <a href="{{ route('mentor.logout') }}" class="group flex items-center gap-3 rounded-xl border border-red-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:bg-red-50">
                <span class="text-red-500 stat-icon bg-red-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 5v1a3 3 0 01-3 3H6a3 3 0 01-3-3V6a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </span>
                <span class="text-sm font-medium text-red-500">Logout</span>
            </a>

        </div>

    </div>

@endsection


@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', () => {

            const emoji = document.getElementById('emoji');
            if (emoji) {
                const emojis = ['🙂', '😀', '😎', '👋', '🤓', '🧐', '😇'];
                let index = 0;
                setInterval(() => {
                    emoji.textContent = emojis[index];
                    index = (index + 1) % emojis.length;
                }, 3000);
            }

            // TODO: wire up an Echo listener here for real-time chat requests
            // once broadcasting is configured (mentor.{id} channel, NewChatRequest event).

            const palette = { indigo: '#4F46E5', violet: '#7C3AED', amber: '#F59E0B' };
            const sharedGrid = { borderColor: '#F1F5F9' };

            if (document.getElementById('bar')) {
                const categories = @json(array_keys($reportsArray));
                const seriesData = @json(array_values($reportsArray));

                // color mapping consistent with analytics view
                const colorMap = {
                    physical: '#7c3aed',
                    verbal:   '#dc2626',
                    sexual:   '#f97316',
                    cyber:    '#06b6d4',
                    other:    '#6b7280'
                };

                const colors = categories.map(c => colorMap[c] ?? '#4F46E5');

                new ApexCharts(document.querySelector('#bar'), {
                    chart: { type: 'bar', toolbar: { show: false } },
                    series: [{ name: 'Reports', data: seriesData }],
                    xaxis: { categories: categories },
                    colors: colors,
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '50%', distributed: true } },
                    dataLabels: { enabled: false },
                    grid: sharedGrid,
                }).render();
            }

            if (document.getElementById('area')) {
                new ApexCharts(document.querySelector('#area'), {
                    chart: { type: 'area', toolbar: { show: false } },
                    series: [{
                        name: 'Chats',
                        data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
                    }],
                    xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'] },
                    colors: [palette.violet],
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
                    dataLabels: { enabled: false },
                    grid: sharedGrid,
                }).render();
            }

            if (document.getElementById('radar')) {
                new ApexCharts(document.querySelector('#radar'), {
                    chart: { type: 'radar', toolbar: { show: false } },
                    series: [
                        { name: 'This term', data: [45, 52, 38, 24, 33, 10] },
                        { name: 'Last term', data: [26, 21, 20, 6, 8, 15] },
                    ],
                    labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                    colors: [palette.indigo, palette.amber],
                }).render();
            }

        });
    </script>
@endpush
