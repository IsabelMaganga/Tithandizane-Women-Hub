@extends('mentor.layouts.dashboard')

@section('title') home @endsection

@section('content')
    <div class=" flex justify-between items-center">
        <h1 class=" text-3xl mb-6 capitalize">Welcome back</h1>
        <h1 id="time" class=" text-3xl mb-6 capitalize">time</h1>
    </div>

    <div class="grid relative gap-5 grid-cols-1 backdrop-blur-2xl md:grid-cols-3 ">

        <div class=" group relative bg-gradient-to-t from-purple-800 to-purple-800  wrap-break-word break-all transition delay-200 hover:scale-105 hover:shadow-sm rounded-xl p-7 shadow cursor-pointer">
            <div class="div flex justify-between items-center">
                <p class="text-sm text-purple-400 ">Mentor</p>
                <p class="text-sm bg-green-500 w-5 h-5 rounded-full text-gray-300"></p>
            </div>

            <div class="flex justify-star gap-2 mt-2 items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($mentorName) }}&background=0D8F81&color=fff&size=128" class="w-10 h-10 rounded-full">
                <h2 class="text-xl  font-semibold text-gray-100">{{ $mentorName }}</h2>
            </div>

            <p class="text-sm mt-1  text-purple-200">{{ $mentorEmail }}</p>
            <p class="text-sm mt-1 text-purple-300">Here's what's happening with your platform today</p>
        </div>

        {{-- total chats --}}
        <div class="bg-white rounded-lg cursor-pointer shadow p-6 transition delay-200 hover:scale-105 hover:shadow-sm">
            <div class="flex flex-col items-start">
                <div class="flex justify-between w-full items-center">
                    <div class="p-3 bg-blue-100 w-20 flex items-center justify-between rounded-full">
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                        <i class="fas fa-add text-blue-600 text"></i>
                        <i class="fas fa-user text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-2xl p-2 px-3 bg-[#f3f4f6] border-[#2563eb]/20 border-2 rounded-full font-normal">20</p>
                </div>

                <div class="mt-2 flex w-full items-center justify-between">
                    <p class="text-3xl text-gray-700">Total chats</p>

                </div>
                <p class=" text-sm text-gray-400 mt-1">Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi il</p>
            </div>

        </div>

    </div>

    @for ($i = 0; $i < 10; $i++)
    <div class=" bg-white col-span-3 mt-5 transition delay-200  hover:shadow-sm rounded-xl p-7 shadow cursor-pointer">
        <p class="text-sm mt-1 text-gray-400">Here's what's happening with your platform today</p>
    </div>
    @endfor

    <div class="  grid grid-cols-1 md:grid-cols-4 gap-5 mt-5">
        <p class="text-sm  rounded-xl p-5 shadow cursor-pointer bg-white mt-1 text-gray-400">Settings</p>
        <p class="text-sm  rounded-xl p-5 shadow cursor-pointer bg-white mt-1 text-gray-400">Guidance</p>
        <p class="text-sm  rounded-xl p-5 shadow cursor-pointer bg-white mt-1 text-gray-400">Guidance</p>
        <p class="text-sm  rounded-xl p-5 shadow cursor-pointer bg-red-500 text-gray-100 mt-1">Logout</p>
    </div>


@endsection
