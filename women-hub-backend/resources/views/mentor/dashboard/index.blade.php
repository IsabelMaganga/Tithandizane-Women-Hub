@extends('mentor.layouts.dashboard')

@section('title','Dashboard')

@push('styles')
<style>

body{
    background:#f8fafc;
}

.dashboard-bg{
    min-height:100vh;
    background:
    radial-gradient(circle at top right,#ede9fe 0%,transparent 35%),
    radial-gradient(circle at bottom left,#ddd6fe 0%,transparent 30%),
    #f8fafc;
}

.glass{
    backdrop-filter:blur(14px);
    background:rgba(255,255,255,.75);
}

.card-hover{
    transition:.35s;
}

.card-hover:hover{
    transform:translateY(-6px);
    box-shadow:0 18px 40px rgba(0,0,0,.08);
}

.icon-box{

    width:60px;
    height:60px;

    border-radius:18px;

    display:flex;

    justify-content:center;

    align-items:center;

}

.emoji{

animation:bounce 2.2s infinite;

}

@keyframes bounce{

0%,100%{

transform:translateY(0px);

}

50%{

transform:translateY(-8px);

}

}

</style>
@endpush

@section('content')

@php

$reportsArray=$reportCounts->toArray();

$totalReports=array_sum($reportsArray);

$categoryCount=count($reportsArray);

$topCategory=$categoryCount
?array_search(max($reportsArray),$reportsArray)
:'-';

$hour=now()->hour;

$greeting=$hour<12?'Good Morning':($hour<18?'Good Afternoon':'Good Evening');

@endphp

<div class="dashboard-bg px-6 py-8">

<div class="mx-auto max-w-7xl">

<!-- HEADER -->

<div class="glass rounded-3xl border border-white/60 p-8 shadow-xl">

<div class="flex flex-wrap items-center justify-between">

<div class="flex items-center gap-5">

<div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-purple-500 text-5xl shadow-lg">

<span id="emoji" class="emoji">👋</span>

</div>

<div>

<p class="text-purple-500 font-semibold">

{{ $greeting }}

</p>

<h1 class="mt-2 text-4xl font-bold text-slate-800">

{{ $mentorName }}

</h1>

<p class="mt-2 text-slate-500">

Welcome back to your mentor dashboard.

</p>

</div>

</div>

<div class="text-right">

<p class="font-semibold text-slate-700">

{{ $mentorEmail }}

</p>

<p class="text-sm text-slate-400">

Today {{ now()->format('l, d M Y') }}

</p>

</div>

</div>

</div>

<!-- STATISTICS -->

<div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-4">

<!-- REPORTS -->

<div class="card-hover rounded-3xl bg-white p-7 shadow">

<div class="flex justify-between">

<div>

<p class="text-slate-500">

Total Reports

</p>

<h2 class="mt-4 text-5xl font-bold text-slate-800">

{{ $totalReports }}

</h2>

</div>

<div class="icon-box bg-purple-100 text-purple-600">

<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2">

<path d="M4 6h16M4 12h16M4 18h7"/>

</svg>

</div>

</div>

</div>

<!-- CATEGORIES -->

<div class="card-hover rounded-3xl bg-white p-7 shadow">

<div class="flex justify-between">

<div>

<p class="text-slate-500">

Categories

</p>

<h2 class="mt-4 text-5xl font-bold text-slate-800">

{{ $categoryCount }}

</h2>

</div>

<div class="icon-box bg-blue-100 text-blue-600">

<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2">

<circle cx="12" cy="12" r="9"/>

</svg>

</div>

</div>

</div>

<!-- TOP CATEGORY -->

<div class="card-hover rounded-3xl bg-white p-7 shadow">

<div class="flex justify-between">

<div>

<p class="text-slate-500">

Most Reported

</p>

<h2 class="mt-4 text-2xl font-bold capitalize text-slate-800">

{{ $topCategory }}

</h2>

</div>

<div class="icon-box bg-amber-100 text-amber-600">

<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2">

<path d="M12 2L15 8L22 9L17 14L18 22L12 19L6 22L7 14L2 9L9 8Z"/>

</svg>

</div>

</div>

</div>

<!-- ACTIVE -->

<div class="card-hover rounded-3xl bg-gradient-to-r from-purple-500 to-indigo-500 p-7 text-white shadow-xl">

<p>

Platform Status

</p>

<h2 class="mt-4 text-5xl font-bold">

Active

</h2>

<p class="mt-3 text-purple-100">

Everything is running smoothly.

</p>

</div>

</div>

    <!-- ===================== -->
    <!-- Analytics Section -->
    <!-- ===================== -->

    <div class="mt-10 grid gap-6 xl:grid-cols-2">

        <!-- Reports -->
        <div class="rounded-3xl bg-white p-7 shadow-sm">

            <div class="mb-6 flex items-center justify-between">

                <div>

                    <h2 class="text-xl font-bold text-slate-800">
                        Reports Analytics
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Distribution of reported harassment categories.
                    </p>

                </div>

                <a href="{{ route('mentor.harassment.analytics') }}"
                   class="rounded-xl bg-purple-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-600">

                    View All

                </a>

            </div>

            <div id="bar" class="h-96"></div>

        </div>

        <!-- Monthly Activity -->

        <div class="rounded-3xl bg-white p-7 shadow-sm">

            <div class="mb-6">

                <h2 class="text-xl font-bold text-slate-800">

                    Monthly Chats

                </h2>

                <p class="mt-1 text-sm text-slate-500">

                    Conversations completed during the last 9 months.

                </p>

            </div>

            <div id="area" class="h-96"></div>

        </div>

    </div>



    <!-- ===================== -->
    <!-- Engagement -->
    <!-- ===================== -->

    <div class="mt-8 rounded-3xl bg-white p-7 shadow-sm">

        <div class="mb-6">

            <h2 class="text-xl font-bold text-slate-800">

                Mentor Engagement

            </h2>

            <p class="text-sm text-slate-500">

                Compare engagement performance across recent months.

            </p>

        </div>

        <div id="radar"></div>

    </div>



    <!-- ===================== -->
    <!-- Quick Actions -->
    <!-- ===================== -->

    <div class="mt-10">

        <div class="mb-6">

            <h2 class="text-2xl font-bold text-slate-800">

                Quick Actions

            </h2>

        </div>

        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">

            <!-- Settings -->

            <a href="{{ route('mentor.settings') }}"
               class="group rounded-3xl bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-2 hover:bg-purple-500">

                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-100 transition group-hover:bg-white">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="28"
                         height="28"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         class="text-purple-600">

                        <path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>

                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2
                        2 0 11-2.83 2.83l-.06-.06a1.65
                        1.65 0 00-1.82-.33
                        1.65 1.65 0 00-1
                        1.51V21a2 2 0
                        11-4 0v-.09a1.65
                        1.65 0 00-1-1.51
                        1.65 1.65 0
                        00-1.82.33l-.06.06a2
                        2 0 11-2.83-2.83l.06-.06a1.65
                        1.65 0 00.33-1.82
                        1.65 1.65 0
                        00-1.51-1H3a2
                        2 0 110-4h.09a1.65
                        1.65 0 001.51-1
                        1.65 1.65 0
                        00-.33-1.82l-.06-.06a2
                        2 0 112.83-2.83l.06.06a1.65
                        1.65 0 001.82.33H9A1.65
                        1.65 0 0010
                        3.09V3a2 2 0
                        114 0v.09A1.65
                        1.65 0 0015
                        4.6a1.65
                        1.65 0 001.82-.33l.06-.06a2
                        2 0 112.83 2.83l-.06.06A1.65
                        1.65 0
                        0019.4 9V9A1.65
                        1.65 0 0021
                        10h.09a2 2 0
                        110 4H21a1.65
                        1.65 0
                        00-1.6 1z"/>

                    </svg>

                </div>

                <h3 class="mt-6 text-lg font-bold text-slate-800 group-hover:text-white">

                    Settings

                </h3>

                <p class="mt-2 text-sm text-slate-500 group-hover:text-purple-100">

                    Manage dashboard preferences.

                </p>

            </a>



            <!-- Profile -->

            <a href="{{ route('mentor.profile') }}"
               class="group rounded-3xl bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-2 hover:bg-purple-500">

                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-100 group-hover:bg-white">

                    👤

                </div>

                <h3 class="mt-6 text-lg font-bold group-hover:text-white">

                    Profile

                </h3>

                <p class="mt-2 text-sm text-slate-500 group-hover:text-purple-100">

                    Update your mentor profile.

                </p>

            </a>



            <!-- Guidance -->

            <a href="{{ route('mentor.Guidance') }}"
               class="group rounded-3xl bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-2 hover:bg-purple-500">

                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-100 group-hover:bg-white">

                    📚

                </div>

                <h3 class="mt-6 text-lg font-bold group-hover:text-white">

                    Guidance

                </h3>

                <p class="mt-2 text-sm text-slate-500 group-hover:text-purple-100">

                    Access mentor resources.

                </p>

            </a>



            <!-- Logout -->

            <a href="{{ route('mentor.logout') }}"
               class="group rounded-3xl border border-red-200 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-2 hover:bg-red-500">

                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-100 group-hover:bg-white">

                    🚪

                </div>

                <h3 class="mt-6 text-lg font-bold text-red-500 group-hover:text-white">

                    Logout

                </h3>

                <p class="mt-2 text-sm text-red-400 group-hover:text-red-100">

                    Sign out securely.

                </p>

            </a>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>

document.addEventListener("DOMContentLoaded", function () {

    /* ==========================================
       Animated Greeting Emoji
    ========================================== */

    const emoji = document.getElementById("emoji");

    if (emoji) {

        const emojis = [
            "👋",
            "😊",
            "😎",
            "🤝",
            "✨",
            "🚀",
            "💜"
        ];

        let i = 0;

        setInterval(() => {

            emoji.textContent = emojis[i];

            i++;

            if(i >= emojis.length){

                i = 0;

            }

        },2500);

    }

    /* ==========================================
       Shared Theme
    ========================================== */

    const palette = {

        purple:"#8B5CF6",

        indigo:"#6366F1",

        pink:"#EC4899",

        green:"#10B981",

        orange:"#F59E0B",

        red:"#EF4444",

        cyan:"#06B6D4",

        gray:"#94A3B8"

    };

    /* ==========================================
       Reports Bar Chart
    ========================================== */

    if(document.querySelector("#bar")){

        const categories = @json(array_keys($reportsArray));

        const values = @json(array_values($reportsArray));

        const colorMap = {

            physical:"#8B5CF6",

            verbal:"#EF4444",

            sexual:"#F59E0B",

            cyber:"#06B6D4",

            emotional:"#10B981",

            other:"#64748B"

        };

        const colors = categories.map(c => colorMap[c] ?? palette.purple);

        new ApexCharts(document.querySelector("#bar"),{

            chart:{
                type:"bar",
                toolbar:{show:false},
                animations:{
                    enabled:true,
                    easing:"easeinout",
                    speed:1000
                }
            },

            series:[{

                name:"Reports",

                data:values

            }],

            xaxis:{

                categories:categories,

                labels:{
                    style:{
                        fontSize:"13px"
                    }
                }

            },

            colors:colors,

            plotOptions:{

                bar:{

                    borderRadius:12,

                    columnWidth:"55%",

                    distributed:true

                }

            },

            dataLabels:{

                enabled:false

            },

            grid:{

                borderColor:"#F1F5F9"

            },

            tooltip:{

                theme:"light"

            }

        }).render();

    }

    /* ==========================================
       Monthly Chats Area
    ========================================== */

    if(document.querySelector("#area")){

        new ApexCharts(document.querySelector("#area"),{

            chart:{
                type:"area",
                toolbar:{show:false},
                zoom:{enabled:false}
            },

            stroke:{
                curve:"smooth",
                width:4
            },

            fill:{
                type:"gradient",
                gradient:{
                    opacityFrom:.45,
                    opacityTo:.03
                }
            },

            colors:[
                palette.purple
            ],

            series:[{

                name:"Chats",

                data:[30,42,55,38,71,65,84,97,121]

            }],

            xaxis:{

                categories:[
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "May",
                    "Jun",
                    "Jul",
                    "Aug",
                    "Sep"
                ]

            },

            dataLabels:{
                enabled:false
            },

            grid:{
                borderColor:"#F1F5F9"
            }

        }).render();

    }

    /* ==========================================
       Radar Chart
    ========================================== */

    if(document.querySelector("#radar")){

        new ApexCharts(document.querySelector("#radar"),{

            chart:{

                type:"radar",

                toolbar:{show:false}

            },

            series:[

                {

                    name:"This Term",

                    data:[68,74,82,91,70,88]

                },

                {

                    name:"Last Term",

                    data:[41,55,60,63,57,61]

                }

            ],

            labels:[

                "Apr",

                "May",

                "Jun",

                "Jul",

                "Aug",

                "Sep"

            ],

            colors:[

                palette.purple,

                palette.orange

            ],

            stroke:{

                width:3

            },

            markers:{

                size:5

            },

            fill:{

                opacity:.25

            },

            legend:{

                position:"top"

            }

        }).render();

    }

});
</script>
@endpush