@extends('mentor.layouts.dashboard')

@section('title') general @endsection

@section('content')
<div class="max-w-5xl px-4 py-0 mx-auto sm:px-6 lg:px-8">
     <!-- Header with Back Navigation -->
    <div class="mt-10 mb-4 ">

        <div class="flex items-center gap-2 mb-2 text-sm text-gray-600">
            <a href="{{ route('mentor.Guidance') }}" class="flex items-center gap-1 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>guidance</span>
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-900">general</span>
        </div>
    </div>

    <!-- Header Section -->
        <div class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-600 p-8 md:p-10">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-10 -mb-10"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="text-2xl text-white fas fa-hand-sparkles"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white md:text-4xl">general Content</h1>
                    </div>
                </div>
                <p class="max-w-2xl text-purple-100 text-sm md:text-base leading-relaxed">
                    Explore our comprehensive collection of hygiene resources designed to promote health, wellness, and cleanliness in your community. Stay informed with expert guidelines and practical tips.
                </p>


            </div>
        </div>




    {{-- details --}}
    <div class="grid w-full grid-cols-1 gap-4  md:grid-cols-3">

        @foreach ($general as $item)
            <div class="mb-8 p-3 grid hover:scale-105 transition delay-200 overflow-hidden bg-[#fff] gap-3 shadow-xl rounded-xl  grid-cols-1 ">

                <div class="flex flex-col items-start justify-center w-full col-span-1 px-2">
                    <div class="flex flex-col flex-1 w-full div">
                        <h1 class="flex font-bold  text-md">{{$item->title}}</h1>
                        <p class=" text-[12px] text-gray-600 mt-2 select-none">{{$item->content}}</p>
                    </div>

                    <div class="flex items-center justify-start w-full grid-cols-2 gap-3 px-2 py-2 mx-auto mt-3 text-gray-500 capitalize rounded-full  bg-gray-100/10" >

                        <p class=" bg-purple-700 hover:bg-purple-800 active:bg-purple-900 select-none transition delay-75 p-3 text-center shadow cursor-pointer text-[10px] rounded-3xl text-amber-50">{{$item->icon}}</p>
                        @if ($item->is_published == 1)
                            <p class=" bg-purple-700 w-full hover:bg-purple-800 active:bg-purple-900 select-none transition delay-75 p-3 text-center shadow cursor-pointer text-[12px] rounded-3xl text-amber-50">Published</p>
                        @else
                            <p class=" bg-purple-700 w-full hover:bg-purple-800 active:bg-purple-900 select-none transition delay-75 p-3 text-center shadow cursor-pointer text-[12px] rounded-3xl text-amber-50">Not Published</p>
                        @endif


                    </div>
                </div>

            </div>
        @endforeach

     </div>


</div>
@endsection
