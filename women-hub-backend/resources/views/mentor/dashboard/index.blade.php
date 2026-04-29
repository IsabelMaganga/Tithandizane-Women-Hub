@extends('mentor.layouts.dashboard')

@section('title') home @endsection

@section('content')


    {{-- stat cards --}}
    <div class="relative grid min-h-screen grid-cols-1 gap-5 p-5 px-3 pt-10 backdrop-blur-2xl md:grid-cols-3">

        <div class="grid grid-cols-1 col-span-1 row-span-4 gap-5 left">

            {{-- mentor short detail --}}
            <div class="relative col-span-1 break-all transition delay-200 border cursor-pointer group bg-gradient-to-t from-purple-800 to-purple-800 wrap-break-word border-slate-900/10 rounded-xl p-7">

                <div class="flex items-center justify-start gap-2 mt-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName) }}&background=0D8F81&color=fff&size=128"
                        class="w-10 h-10 rounded-full">
                    <h2 class="text-xl font-semibold text-gray-100">{{ $mentorName }}</h2>
                </div>

                <p class="mt-1 text-sm text-purple-200">{{ $mentorEmail }}</p>
                <p class="mt-1 text-sm text-purple-300">
                    Here's what's happening with your platform today
                </p>
            </div>

            {{-- line chart --}}
            <div  class="relative h-full col-span-1 p-1 break-all transition delay-200 bg-white border cursor-pointer group row-span-0 wrap-break-word rounded-xl border-slate-900/10">
                <div id="bar" class="chart"></div>
            </div>

            {{-- line chart --}}
            <div  class="relative h-full col-span-1 p-1 break-all transition delay-200 bg-white border cursor-pointer group row-span-0 wrap-break-word rounded-xl border-slate-900/10">
                <h1 class="mx-2 mt-3 ">Monthly chats</h1>
                <div id="chart" class="chart"></div>
            </div>

        </div>


        {{-- bar chart --}}
        <div class="col-span-2 row-span-4 bg-white border left rounded-2xl border-slate-900/10">
            <h1 class="mx-3 mt-3 text-2xl ">Monthly stats</h1>
            <div  id="area" class="chart"></div>
        </div>

        <div class="col-span-2 bg-white border left rounded-2xl p-7 row-span-8 border-slate-900/10">
            <div id="radar" class="rader"></div>
        </div>

    </div>


    {{-- Availability --}}
    {{-- <div class="p-4 mx-3 bg-white rounded shadow">
        <h2 class="mb-4 text-lg font-bold">Availability</h2>

        <form action="#" method="POST">
            @csrf
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_available" onchange="this.form.submit">
                <p>Offline</p>
            </label>
        </form>
    </div> --}}

    {{-- Extra Cards --}}
    <div class="col-span-3 mx-3 mt-5 transition delay-200 bg-white shadow cursor-pointer hover:shadow-sm rounded-xl p-7">
        <p class="mt-1 text-sm text-gray-400">
            Here's what's happening with your platform today
        </p>
    </div>

    {{-- Footer Links --}}
    <div class="grid grid-cols-1 gap-5 mx-3 mt-5 md:grid-cols-4">
        <a href="{{ route('mentor.settings')}}" class="text-sm rounded-xl p-5 shadow cursor-pointer bg-[#081e77] hover:bg-[#0f2ca1] active:bg-[#081e77] transition-all ease-in-out delay-75 text-gray-200">Settings</a>
        <a href="{{ route('mentor.profile')}}" class="text-sm rounded-xl p-5 shadow cursor-pointer bg-[#081e77] hover:bg-[#0f2ca1] active:bg-[#081e77] transition-all ease-in-out delay-75 text-gray-200">Profile</a>
        <a href="{{ route('mentor.Guidance')}}" class="text-sm rounded-xl p-5 shadow cursor-pointer bg-[#081e77] hover:bg-[#0f2ca1] active:bg-[#081e77] transition-all ease-in-out delay-75 text-gray-200">Guidance</a>
        <a href="{{ route('mentor.logout')}}" class="p-5 text-sm text-gray-100 bg-red-500 shadow cursor-pointer rounded-xl">Logout</a>
    </div>



@endsection


@push('scripts')
    <script>

        window.addEventListener('DOMContentLoaded', ()=>{


        // alert('test');
        console.log('welcome to your dashboard');

        // Listen for new chat requests instantly
        // window.Echo.private('mentor.' . {{ auth()->id() }})
        //     .listen('NewChatRequest', (e) => {
        //         // Add new request instantly
        //         addNewChatRequest(e);

        //         console.log('test');
        //         // Play sound
        //         playNotificationSound();

        //         // Show browser notification
        //         // showNotification(`${e.girl_name} wants to chat`);
        // });



        // line chart dataset
        var options = {
            chart: {
                type: 'line'
            },
            series: [{
                name: 'messages',
                data: [30,40,35,50,49,60,70,91,125]
            }],
            xaxis: {
                categories: ['jan','Feb','Mar','Apr','May', 'Jun', 'Jul', "Aug", 'Sep']
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();

        // area chart dataset
        var options = {
            chart: {
                type: 'area'
            },
            series: [{
                name: 'messages',
                data: [30,40,35,50,49,60,70,91,125]
            }],
            xaxis: {
                categories: ['jan','Feb','Mar','Apr','May', 'Jun', 'Jul', "Aug", 'Sep']
            }
        };

        var chart = new ApexCharts(document.querySelector("#area"), options);

        chart.render();

        {{--  alert(200);  --}}
        // bar chart dataset
        var options = {
            chart: {
                type: 'bar'
                {{--  height: 400  --}}
            },
            series: [{
                name: 'Reports',
                data: @json(array_values($reportCounts->toArray()))
            }],
            xaxis: {
                categories: @json(array_keys($reportCounts->toArray()))
            },
            colors: ['#3b82f6'],
            title: {
                text: 'Reports by Type',
                align: 'center'
            },
        };

        var chart = new ApexCharts(document.querySelector("#bar"), options);

        chart.render();

        // radar chart dataset
        var options = {
            chart: {
                type: 'radar'
            },
            series: [
                {
                name: "Radar Series 1",
                data: [45, 52, 38, 24, 33, 10]
                },
                {
                name: "Radar Series 2",
                data: [26, 21, 20, 6, 8, 15]
                }
            ],
            labels: ['April', 'May', 'June', 'July', 'August', 'September']

        };

        var chart = new ApexCharts(document.querySelector("#radar"), options);

        chart.render();

        })

    </script>
@endpush

